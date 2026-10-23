<?php

declare(strict_types=1);

namespace App\Observer;

trait NormalizaTexto
{
    private function normalizarTexto(string $texto): string
    {
        return (string) preg_replace('/\s+/u', ' ', trim($texto));
    }
}
