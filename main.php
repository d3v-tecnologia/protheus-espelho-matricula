<?php

use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Parser;

require_once "pdfparser/alt_autoload.php";
require_once "fpdi/src/autoload.php";
require_once "fpdf/fpdf.php";

$source = "source/ponr010.pdf";

$parser = new Parser();
$pdf = $parser->parseFile($source);

$matriculas = [];

foreach ($pdf->getPages() as $pagina) {
    $texto = $pagina->getText();
    $inicio = strpos($texto, "Matrícula:") + 11;
    $matricula = trim(substr($texto, $inicio, 12));
    $matriculas[$matricula][] = $pagina->getPageNumber() + 1;
}

foreach ($matriculas as $matricula => $paginas) {
    $pdf = new Fpdi();
    foreach ($paginas as $p) {
        $pdf->AddPage("L");
        $pdf->setSourceFile($source);
        $pdf->useTemplate($pdf->importPage($p));
    }
    $pdf->Output("output/matricula_$matricula.pdf", "F");
}
