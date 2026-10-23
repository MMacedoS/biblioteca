<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Autor;
use App\Entity\Livro;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AutorControllerTest extends WebTestCase
{
    public function testListagemDeAutoresAbreComSucesso(): void
    {
        $client = static::createClient();

        $client->request('GET', '/autores');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Autores');
    }

    public function testCadastraAutor(): void
    {
        $client = static::createClient();
        $client->request('GET', '/autores/novo');

        $client->submitForm('Salvar', [
            'autor[nome]' => 'Cecília Meireles',
        ]);

        self::assertResponseRedirects('/autores');
        $client->followRedirect();
        self::assertSelectorTextContains('.alert-success', 'Autor cadastrado com sucesso.');
        self::assertSelectorTextContains('table', 'Cecília Meireles');
    }

    public function testRecusaNomeSoComEspacos(): void
    {
        $client = static::createClient();
        $client->request('GET', '/autores/novo');

        $client->submitForm('Salvar', [
            'autor[nome]' => '   ',
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertSelectorExists('.invalid-feedback');
    }

    public function testNaoExcluiAutorQuePossuiLivros(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $autor = (new Autor())->setNome('Autor com livro');
        $livro = (new Livro())
            ->setTitulo('Livro de teste')
            ->setEditora('Editora de teste')
            ->setEdicao(1)
            ->setAnoPublicacao('2000')
            ->setValor('10.00');
        $livro->addAutor($autor);
        $entityManager->persist($autor);
        $entityManager->persist($livro);
        $entityManager->flush();

        $crawler = $client->request('GET', '/autores');
        $formulario = $crawler
            ->filter(sprintf('form[action="/autores/%d/excluir"]', $autor->getId()))
            ->form();
        $client->submit($formulario);

        self::assertResponseRedirects('/autores');
        $client->followRedirect();
        self::assertSelectorTextContains('.alert-danger', 'não pode ser excluído');
    }
}
