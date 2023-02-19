<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class Client implements Model
{
    private CrudUtil $crud_util;
    private object|int $project_data;

    public function __construct(string|int $project_id)
    {
        $this->crud_util = new CrudUtil();
    }

    public function saveProjectFeedbackMessage(string|int $project_id, string $msg)
    {
    }

    public function getReportData(string|int $project_id)
    {
    }
}
