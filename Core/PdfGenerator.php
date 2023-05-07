<?php

namespace Core;

use Dompdf\Dompdf;
use Dompdf\Options;

require __DIR__ . '/../vendor/autoload.php';

/* Official documentation for the library => https://github.com/dompdf/dompdf
This is an HTML(and CSS) to PDF converter
all the styling can be done using HTML and CSS */

/*use Dompdf\Dompdf;
use Dompdf\Options;*/

class PdfGenerator
{
    private Options $options;
    private Dompdf $dompdf;

    public function __construct()
    {

         // options object of the dompdf object
         $this->options = new Options();

         // setting the root directory
         $this->options->setChroot(__DIR__ . '/../');

         // setting the default paper size to "A4"
         // $this->options->setDefaultPaperSize("A4");

         $this->dompdf = new Dompdf(options: $this->options);

    }

    /**
     * Method description
     * @param string $path_to_html_markup
     * @param string $path_to_style_sheet
     * @param string $file_name takes the name of the resulting file
     *
     * Generates a pdf with a given name using the given content
     *
     *                                  ## Content ##
     * #######################################################################################
     *
     * Project name                                         Date of report generation
     *
     * Project description
     *
     *
     *
     * Marshal - footer will be included(as the footer)
     * #######################################################################################
     * @param array $attributes
     */
    public function renderPDF(string $path_to_html_markup, string $path_to_style_sheet, string $file_name, array $attributes = []): void
    {
        $html = file_get_contents("D:/xampp/htdocs" . $path_to_html_markup);
        $css = file_get_contents("D:/xampp/htdocs" . $path_to_style_sheet);

        $html = str_replace("/*{{ styles }}*/", $css, $html);

        // loading the html to generate the pdf
        $this->dompdf->loadHtml($html);
        // rendering the pdf
        $this->dompdf->render();

        // adding meta-data
        $this->dompdf->addInfo("Title", "Report-generate-on-" . date("F j, Y, g:i a"));
        $this->dompdf->addInfo("Author", "ReportGenerator@Marshal");

        // output the generated document to the browser with the given default name
        // the "Attachment" => 0 is used to output the generated pdf to the browser itself rather that prompting for direct downloading
        $this->dompdf->stream(filename: $file_name, options: ["Attachment" => 0]);
    }
}