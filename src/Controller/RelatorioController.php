<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\GeradorPdf;
use App\Service\RelatorioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/relatorio', name: 'relatorio_')]
final class RelatorioController extends AbstractController
{
    public function __construct(
        private readonly RelatorioService $relatorioService,
        private readonly GeradorPdf $geradorPdf,
    ) {}

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $grupos = $this->relatorioService->gerarLivrosPorAutor();

        return $this->render('relatorio/index.html.twig', [
            'grupos' => $grupos,
            'totalGeral' => $this->relatorioService->calcularTotalGeral($grupos),
        ]);
    }

    #[Route('/pdf', name: 'pdf', methods: ['GET'])]
    public function pdf(): Response
    {
        $grupos = $this->relatorioService->gerarLivrosPorAutor();

        $html = $this->renderView('relatorio/pdf.html.twig', [
            'grupos' => $this->relatorioService->gerarLivrosPorAutor(),
            'geradoEm' => new \DateTimeImmutable(),
            'totalGeral' => $this->relatorioService->calcularTotalGeral($grupos),
        ]);

        return new Response($this->geradorPdf->gerar($html), Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_INLINE,
                'relatorio-livros-por-autor.pdf',
            ),
        ]);
    }
}
