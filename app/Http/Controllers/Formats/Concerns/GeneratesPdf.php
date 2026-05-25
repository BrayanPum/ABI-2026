<?php

namespace App\Http\Controllers\Formats\Concerns;

use Barryvdh\DomPDF\Facade\Pdf;

trait GeneratesPdf
{
    protected function generarPdf(string $vista, array $datos, string $nombreArchivo)
    {
        return Pdf::loadView($vista, $datos)->download($nombreArchivo);
    }
}
