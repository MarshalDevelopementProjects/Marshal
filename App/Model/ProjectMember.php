<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class ProjectMember implements Model
{
    private CrudUtil $crud_util;
    private array|object $project_data;

    public function __construct(string|int $project_id)
    {
        $this->crud_util = new CrudUtil();
    }

    public function saveForumMessage(string|int $project_id, string $msg)
    {
        // add the message to the database
    }

    public function getReportData(string|int $project_id)
    {
        // get the data relevant to the report from the database
    }
}
