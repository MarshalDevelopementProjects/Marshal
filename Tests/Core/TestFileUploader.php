<?php

namespace Tests\Core;

require __DIR__ . '/../../vendor/autoload.php';

use Core\FileUploader;
use Tests\Tester;

class TestFileUploader extends Tester
{

    public function __construct()
    {
        parent::__construct(FileUploader::class);
    }

    function testUploadWithEmptyArgs(): void
    {
        // HAVE TO CHECK THE RESPONSES MANUALLY

        // both arguments are empty
        FileUploader::upload(allowed_file_types: [],fields: []);

        // single empty argument cases
        FileUploader::upload(
            allowed_file_types: ['image/png', 'image/jpg', 'image/gif'],
            fields: []
        );

        // single empty argument cases
        FileUploader::upload(
            allowed_file_types: [],
            fields: ['file_upload']
        );
    }

    public function run(): void
    {
        $this->testUploadWithEmptyArgs();
        $this->summary();
    }
}