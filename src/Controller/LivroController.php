<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\LivroData;
use App\Entity\Livro;
use App\Exception\RegistroNaoEncontradoException;
use App\Form\LivroType;
use App\Service\LivroService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/livros', name: 'livro_')]
final class LivroController extends AbstractController
{
    public function __construct(
        private readonly LivroService $livroService,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('livro/index.html.twig', [
            'livros' => $this->livroService->listar(),
        ]);
    }

    #[Route('/novo', name: 'novo', methods: ['GET', 'POST'])]
    public function novo(Request $request): Response
    {
        $data = new LivroData();
        $form = $this->createForm(LivroType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->livroService->criar($data);
            $this->addFlash('success', 'Livro cadastrado com sucesso.');

            return $this->redirectToRoute('livro_index');
        }

        return $this->render('livro/form.html.twig', [
            'form' => $form,
            'titulo' => 'Novo livro',
        ]);
    }

    #[Route('/{id}/editar', name: 'editar', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function editar(int $id, Request $request): Response
    {
        $livro = $this->obterOu404($id);
        $data = LivroData::fromEntity($livro);
        $form = $this->createForm(LivroType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->livroService->atualizar($livro, $data);
            $this->addFlash('success', 'Livro atualizado com sucesso.');

            return $this->redirectToRoute('livro_index');
        }

        return $this->render('livro/form.html.twig', [
            'form' => $form,
            'titulo' => 'Editar livro',
        ]);
    }

    #[Route('/{id}/excluir', name: 'excluir', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function excluir(int $id, Request $request): Response
    {
        $livro = $this->obterOu404($id);

        if (!$this->isCsrfTokenValid('excluir_livro_'.$id, $request->getPayload()->getString('_token'))) {
            $this->addFlash('danger', 'Não foi possível validar a solicitação. Tente novamente.');

            return $this->redirectToRoute('livro_index');
        }

        $this->livroService->excluir($livro);
        $this->addFlash('success', 'Livro excluído com sucesso.');

        return $this->redirectToRoute('livro_index');
    }

    private function obterOu404(int $id): Livro
    {
        try {
            return $this->livroService->obter($id);
        } catch (RegistroNaoEncontradoException $e) {
            throw $this->createNotFoundException($e->getMessage(), $e);
        }
    }
}
