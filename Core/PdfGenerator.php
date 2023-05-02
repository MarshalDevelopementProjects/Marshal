<?php

namespace Core;

require __DIR__ . '/../vendor/autoload.php';

/* Official documentation for the library => https://github.com/dompdf/dompdf
This is an HTML(and CSS) to PDF converter
all the styling can be done using HTML and CSS */

/*use Dompdf\Dompdf;
use Dompdf\Options;*/

class PdfGenerator
{
    /*private Options $options;
    private Dompdf $dompdf;*/
    private $CDN = '<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>';
    private $SCRIPT = '
        <script>
            function generatePDF() {
                const element = document.getElementById("pdf-template"); // Replace "my-element" with the ID of the HTML element you want to generate a PDF from
                const opt = {
                    margin: [0, 0, 0, 0],
                    filename: "Report.pdf",
                    image: { type: "jpeg", quality: 1.98 },
                    html2canvas: { scale: 8 },
                    jsPDF: { unit: "in", format: "a4", orientation: "portrait" }
                };
                html2pdf().set(opt).from(element).save();
            }
            // generatePDF();
        </script>
    ';

    private $FOOTERCSS = "
                          footer {
                              position: absolute;
                              bottom: 0;
                              left: 0;
                              right: 0;
                              background-color: #ff0000;
                              color: #fff;
                              font-size: 10px;
                              text-align: center;
                              padding: 10px;
                          }
                          ";

    public function __construct()
    {
        /*
         // options object of the dompdf object
         $this->options = new Options();

         // setting the root directory
         $this->options->setChroot(__DIR__ . '/../');

         // setting the default paper size to "A4"
         // $this->options->setDefaultPaperSize("A4");

         $this->dompdf = new Dompdf(options: $this->options);
        */
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
     */
    public function renderPDF(string $path_to_html_markup, string $path_to_style_sheet, string $file_name, array $attributes = []): void
    {
        $html = file_get_contents("D:/xampp/htdocs" . $path_to_html_markup);
        $css = file_get_contents("D:/xampp/htdocs" . $path_to_style_sheet);

        $html = str_replace("<!-- CDN -->", $this->CDN, $html);
        $html = str_replace("/*{{ styles }}*/", $css . " " . $this->FOOTERCSS, $html);
        $html = str_replace("<!-- SCRIPT -->", $this->SCRIPT, $html);



        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $html = str_replace("<!-- " . $key ." -->", $value, $html);
            }
        }

        /*echo "<pre>";
        var_dump($html);
        echo "</pre>";*/

        echo $html;
        /*
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
        */
    }
}