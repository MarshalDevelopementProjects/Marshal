<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class ProjectLeader implements Model
{

    private CrudUtil $crud_util;
    private array|object $project_data;

    public function __construct(string|int $project_id)
    {
        $this->crud_util = new CrudUtil();
    }

    public function saveProjectFeedbackMessage(string|int $project_id, string $msg)
    {
        // add the message to the project feedback table as well as the messages table
    }

    public function saveForumMessage(string|int $project_id, string $msg)
    {
        // add the message to the project message table and the messages table
    }

    // used to give the feedback for the groups
    public function saveGroupFeedbackMessage(string|int $project_id, string|int $group_id, string $msg)
    {
        // add the message to the project message table and the messages table
    }

    public function getReportData(string|int $project_id)
    {
    }
}
