<?php

require __DIR__ . '/../../../vendor/autoload.php';

use Core\PdfGenerator;

function testPdfGenerator()
{
    $PdfGenerator = new PdfGenerator();
    $PdfGenerator->renderPDF(
        path_to_html_markup: "/Tests/Test.Core/test.pdfgenerator/pdf-template.html",
        path_to_style_sheet: "/Tests/Test.Core/test.pdfgenerator/pdf-styles.css",
        file_name: "first_pdf.pdf"
    );
}

testPdfGenerator();
