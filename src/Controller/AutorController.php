<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\AutorData;
use App\Entity\Autor;
use App\Exception\AutorPossuiLivrosException;
use App\Exception\RegistroNaoEncontradoException;
use App\Form\AutorType;
use App\Service\AutorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/autores', name: 'autor_')]
final class AutorController extends AbstractController
{
    public function __construct(
        private readonly AutorService $autorService,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('autor/index.html.twig', [
            'autores' => $this->autorService->listar(),
        ]);
    }

    #[Route('/novo', name: 'novo', methods: ['GET', 'POST'])]
    public function novo(Request $request): Response
    {
        $data = new AutorData();
        $form = $this->createForm(AutorType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->autorService->criar($data);
            $this->addFlash('success', 'Autor cadastrado com sucesso.');

            return $this->redirectToRoute('autor_index');
        }

        return $this->render('autor/form.html.twig', [
            'form' => $form,
            'titulo' => 'Novo autor',
        ]);
    }

    #[Route('/{id}/editar', name: 'editar', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function editar(int $id, Request $request): Response
    {
        $autor = $this->obterOu404($id);
        $data = AutorData::fromEntity($autor);
        $form = $this->createForm(AutorType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->autorService->atualizar($autor, $data);
            $this->addFlash('success', 'Autor atualizado com sucesso.');

            return $this->redirectToRoute('autor_index');
        }

        return $this->render('autor/form.html.twig', [
            'form' => $form,
            'titulo' => 'Editar autor',
        ]);
    }

    #[Route('/{id}/excluir', name: 'excluir', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function excluir(int $id, Request $request): Response
    {
        $autor = $this->obterOu404($id);

        if (!$this->isCsrfTokenValid('excluir_autor_'.$id, $request->getPayload()->getString('_token'))) {
            $this->addFlash('danger', 'Não foi possível validar a solicitação. Tente novamente.');

            return $this->redirectToRoute('autor_index');
        }

        try {
            $this->autorService->excluir($autor);
            $this->addFlash('success', 'Autor excluído com sucesso.');
        } catch (AutorPossuiLivrosException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('autor_index');
    }

    private function obterOu404(int $id): Autor
    {
        try {
            return $this->autorService->obter($id);
        } catch (RegistroNaoEncontradoException $e) {
            throw $this->createNotFoundException($e->getMessage(), $e);
        }
    }
}
