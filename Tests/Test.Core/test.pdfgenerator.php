<?php

require __DIR__ . '/../../vendor/autoload.php';

use Core\PdfGenerator\PdfGenerator;

function testPdfGenerator()
{
    $PdfGenerator = new PdfGenerator();
    $PdfGenerator->renderPDF(content: "<h1>Test PDF</h1>", styles: "", file_name: "first_pdf.pdf");
}

testPdfGenerator();
