<?php

namespace App\Controller\PDF;

require __DIR__ . '/../../../vendor/autoload.php';

class PDFController
{
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

    /**
     * Method description
     * @param string $path_to_html_markup
     * @param string $path_to_style_sheet
     * @param string $file_name takes the name of the resulting file
     *
     * Generates a pdf with a given name using the given content
     * @param array $attributes
     */
    public function generateGeneralFormatPDF(string $path_to_html_markup, string $path_to_style_sheet, string $file_name, array $attributes = []): void
    {
        $html = file_get_contents("D:/xampp/htdocs" . $path_to_html_markup);
        $css = file_get_contents("D:/xampp/htdocs" . $path_to_style_sheet);

        $html = str_replace("<!-- CDN -->", $this->CDN, $html);
        $html = str_replace("/*{{ styles }}*/", $css . " " . $this->FOOTERCSS, $html);
        $html = str_replace("<!-- SCRIPT -->", $this->SCRIPT, $html);

        // process task data
        if(!empty($attributes)) $attributes = $this->processGeneralFormatPDFData($attributes);

        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $html = str_replace("<!-- " . $key ." -->", $value, $html);
            }
        }

        echo $html;
    }

    // TODO: DOCUMENT THIS PLEASE
    /*
    <div class="task">
        <div class="task-name">
            <p> Lorem ipsum dolor sit amet consectetur, adipisicing elit. Assumenda, quod?</p>
        </div>
        <div class="completed-date">
            <p>2023-3-19</p>
        </div>
    </div>
     **/
    private function processGeneralFormatPDFData(array $args): array
    {
        // TODO: PROCESS THE GIVEN ARRAY AND MAKE A NEW ARRAY WITH THE APPROPRIATE ATTRIBUTES
        if (
            !empty($args) &&
            array_key_exists("project_data", $args) &&
            !empty($args["project_data"]) &&
            array_key_exists("task_data", $args) &&
            !empty($args["task_data"])
        ) {
            // TODO: CHANGE THE KEYS TO HTML COMMENTED KEYS
            $processed_data = [];
            $project_data = $args["project_data"];
            $task_data = $args["task_data"];
            foreach ($project_data as $attr_name => $value) {
                $processed_data[$attr_name] = $value;
            }

            $processed_data["task_data"] = "";

            foreach ($task_data as $task) {
                $processed_data["task_data"] .= "\n" . ' <div class="task"> <div class="task-name"> <p> ' . $task["task_name"] . ' </p> </div> <div class="completed-date"> <p> ' . $task["task_completed_date"] . ' </p> </div> </div> ' . "\n";
            }
            return $processed_data;
        }
        return [];
    }
}