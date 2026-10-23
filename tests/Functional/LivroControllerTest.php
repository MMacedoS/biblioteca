<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Autor;
use App\Entity\Livro;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Form;

final class LivroControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testCadastraLivroComValorNoFormatoBrasileiro(): void
    {
        $this->criarAutor('Machado de Assis');

        $formulario = $this->abrirFormularioDeNovoLivro();
        $formulario['livro[valor]'] = '49,90';
        $formulario['livro[autores]'][0]->tick();
        $this->client->submit($formulario);

        self::assertResponseRedirects('/livros');
        $this->client->followRedirect();
        self::assertSelectorTextContains('.alert-success', 'Livro cadastrado com sucesso.');
        self::assertSelectorTextContains('table', 'Dom Casmurro');
        self::assertSelectorTextContains('table', 'Machado de Assis');
        self::assertSelectorTextContains('table', '49,90');
    }

    public function testRecusaAnoDePublicacaoNoFuturo(): void
    {
        $this->criarAutor('Machado de Assis');

        $formulario = $this->abrirFormularioDeNovoLivro();
        $formulario['livro[anoPublicacao]'] = '2099';
        $formulario['livro[autores]'][0]->tick();
        $this->client->submit($formulario);

        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('form', 'O ano de publicação não pode ser no futuro.');
    }

    public function testRecusaLivroSemAutor(): void
    {
        $formulario = $this->abrirFormularioDeNovoLivro();
        $this->client->submit($formulario);

        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('form', 'Selecione ao menos um autor.');
    }

    public function testExcluiLivro(): void
    {
        $autor = $this->criarAutor('Machado de Assis');
        $livro = (new Livro())
            ->setTitulo('Livro para excluir')
            ->setEditora('Editora de teste')
            ->setEdicao(1)
            ->setAnoPublicacao('2000')
            ->setValor('10.00');
        $livro->addAutor($autor);
        $this->salvar($livro);

        $crawler = $this->client->request('GET', '/livros');
        $formulario = $crawler
            ->filter(sprintf('form[action="/livros/%d/excluir"]', $livro->getId()))
            ->form();
        $this->client->submit($formulario);

        self::assertResponseRedirects('/livros');
        $this->client->followRedirect();
        self::assertSelectorTextContains('.alert-success', 'Livro excluído com sucesso.');
        self::assertSelectorTextNotContains('body', 'Livro para excluir');
    }

    private function abrirFormularioDeNovoLivro(): Form
    {
        $crawler = $this->client->request('GET', '/livros/novo');

        $formulario = $crawler->selectButton('Salvar')->form();
        $formulario['livro[titulo]'] = 'Dom Casmurro';
        $formulario['livro[editora]'] = 'Garnier';
        $formulario['livro[edicao]'] = '1';
        $formulario['livro[anoPublicacao]'] = '1899';
        $formulario['livro[valor]'] = '10,00';

        return $formulario;
    }

    private function criarAutor(string $nome): Autor
    {
        $autor = (new Autor())->setNome($nome);
        $this->salvar($autor);

        return $autor;
    }

    private function salvar(object $entidade): void
    {
        $entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $entityManager->persist($entidade);
        $entityManager->flush();
    }
}
