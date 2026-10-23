<?php

declare(strict_types=1);

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

final class GeradorPdf
{
    public function gerar(string $html): string
    {
        $options = new Options();
        $options->setDefaultFont('DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return (string) $dompdf->output();
    }
}
