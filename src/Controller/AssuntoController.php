<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\AssuntoData;
use App\Entity\Assunto;
use App\Exception\AssuntoPossuiLivrosException;
use App\Exception\RegistroNaoEncontradoException;
use App\Form\AssuntoType;
use App\Service\AssuntoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/assuntos', name: 'assunto_')]
final class AssuntoController extends AbstractController
{
    public function __construct(
        private readonly AssuntoService $assuntoService,
    ) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('assunto/index.html.twig', [
            'assuntos' => $this->assuntoService->listar(),
        ]);
    }

    #[Route('/novo', name: 'novo', methods: ['GET', 'POST'])]
    public function novo(Request $request): Response
    {
        $data = new AssuntoData();
        $form = $this->createForm(AssuntoType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->assuntoService->criar($data);
            $this->addFlash('success', 'Assunto cadastrado com sucesso.');

            return $this->redirectToRoute('assunto_index');
        }

        return $this->render('assunto/form.html.twig', [
            'form' => $form,
            'titulo' => 'Novo assunto',
        ]);
    }

    #[Route('/{id}/editar', name: 'editar', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function editar(int $id, Request $request): Response
    {
        $assunto = $this->obterOu404($id);
        $data = AssuntoData::fromEntity($assunto);
        $form = $this->createForm(AssuntoType::class, $data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->assuntoService->atualizar($assunto, $data);
            $this->addFlash('success', 'Assunto atualizado com sucesso.');

            return $this->redirectToRoute('assunto_index');
        }

        return $this->render('assunto/form.html.twig', [
            'form' => $form,
            'titulo' => 'Editar assunto',
        ]);
    }

    #[Route('/{id}/excluir', name: 'excluir', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function excluir(int $id, Request $request): Response
    {
        $assunto = $this->obterOu404($id);

        if (!$this->isCsrfTokenValid('excluir_assunto_'.$id, $request->getPayload()->getString('_token'))) {
            $this->addFlash('danger', 'Não foi possível validar a solicitação. Tente novamente.');

            return $this->redirectToRoute('assunto_index');
        }

        try {
            $this->assuntoService->excluir($assunto);
            $this->addFlash('success', 'Assunto excluído com sucesso.');
        } catch (AssuntoPossuiLivrosException $e) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('assunto_index');
    }

    private function obterOu404(int $id): Assunto
    {
        try {
            return $this->assuntoService->obter($id);
        } catch (RegistroNaoEncontradoException $e) {
            throw $this->createNotFoundException($e->getMessage(), $e);
        }
    }
}
