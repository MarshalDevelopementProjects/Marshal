<?php

namespace Core\PdfGenerator;

require __DIR__ . '/../../vendor/autoload.php';

/* Official documentation for the library => https://github.com/dompdf/dompdf
This is an HTML(and CSS) to PDF converter
all the styling can be done using HTML and CSS */

use Dompdf\Dompdf;
use Dompdf\Options;
use App\CrudUtil\CrudUtil;

class PdfGenerator
{
    private Options $options;
    private Dompdf $dompdf;
    private string $default_html;
    private string $default_css;
    private string $footer = "footer {
                                position: absolute;
                                bottom: 0;
                                left: 0;
                                right: 0;
                                background-color: #ff0000;
                                color: #fff;
                                font-size: 10px;
                                text-align: center;
                                padding: 10px;
                              }";

    public function __construct()
    {
        // options object of the dompdf object
        $this->options = new Options();

        // setting the root directory
        $this->options->setChroot(__DIR__ . '/../');

        // setting the default paper size to "A4"
        $this->options->setDefaultPaperSize("A4");

        $this->dompdf = new Dompdf(options: $this->options);
        // $this->dompdf->setPaper("A4");

        // basic structure implementation of the documentation
        $this->default_html = file_get_contents(__DIR__ . '/pdf-template.html');
        $this->default_css = file_get_contents(__DIR__ . '/pdf-styles.css');
    }

    /** 
     * Method description
     * @param string $content takes the inner content of the pdf
     * @param string $styles takes the styles(css) required for the pdf
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
     * 
     */
    public function renderPDF(string $content, string $styles = "", string $file_name)
    {
        // creating the content using html and css
        // $styles = "<style>" . ($styles !== "" ? $styles : $this->default_css) . $this->footer . "</style>";
        $styles = ($styles !== "" ? $styles : $this->default_css) . $this->footer;
        $content = str_replace(
            ["/*{{ styles }}*/", "{{ content }}"], // replacements
            [
                $styles, // styles
                $content // file content, should be handled by the calling code
            ],
            $this->default_html // the template file 
        );

        // loading the html to generate the pdf
        $this->dompdf->loadHtml($content);
        // rendering the pdf
        $this->dompdf->render();

        // adding meta-data
        $this->dompdf->addInfo("Title", "Report-generate-on-" . date("F j, Y, g:i a"));
        $this->dompdf->addInfo("Author", "ReportGenerator@Marshal");

        // output the generate document to the browser with the given default name
        // the "Attachment" => 0 is used to output the generated pdf to the browser itself rather that prompting for direct downloading
        $this->dompdf->stream(filename: $file_name, options: ["Attachment" => 0]);
    }
}
