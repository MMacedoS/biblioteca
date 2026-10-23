<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Assunto;
use App\Entity\Livro;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AssuntoControllerTest extends WebTestCase
{
    public function testListagemDeAssuntosAbreComSucesso(): void
    {
        $client = static::createClient();

        $client->request('GET', '/assuntos');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Assuntos');
    }

    public function testCadastraAssunto(): void
    {
        $client = static::createClient();
        $client->request('GET', '/assuntos/novo');

        $client->submitForm('Salvar', [
            'assunto[descricao]' => 'Romance',
        ]);

        self::assertResponseRedirects('/assuntos');
        $client->followRedirect();
        self::assertSelectorTextContains('.alert-success', 'Assunto cadastrado com sucesso.');
        self::assertSelectorTextContains('table', 'Romance');
    }

    public function testRecusaNomeSoComEspacos(): void
    {
        $client = static::createClient();
        $client->request('GET', '/assuntos/novo');

        $client->submitForm('Salvar', [
            'assunto[descricao]' => '   ',
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertSelectorExists('.invalid-feedback');
    }

    public function testNaoExcluiAssuntoQuePossuiLivros(): void
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $assunto = (new Assunto())->setDescricao('Assunto com livro');
        $livro = (new Livro())
            ->setTitulo('Livro de teste')
            ->setEditora('Editora de teste')
            ->setEdicao(1)
            ->setAnoPublicacao('2000')
            ->setValor('10.00');
        $livro->addAssunto($assunto);
        $entityManager->persist($assunto);
        $entityManager->persist($livro);
        $entityManager->flush();

        $crawler = $client->request('GET', '/assuntos');
        $formulario = $crawler
            ->filter(sprintf('form[action="/assuntos/%d/excluir"]', $assunto->getId()))
            ->form();
        $client->submit($formulario);

        self::assertResponseRedirects('/assuntos');
        $client->followRedirect();
        self::assertSelectorTextContains('.alert-danger', 'não pode ser excluído');
    }
}
