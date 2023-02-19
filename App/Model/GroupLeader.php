<?php

namespace App\Model;

require __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class GroupLeader implements Model
{
    private CrudUtil $crud_util;
    private object|array $group_data;

    public function __construct(string|int $group_id)
    {
        $this->crud_util = new CrudUtil();
    }

    public function saveGroupMessage(string|int $project_id, string|int $group_id, string $msg)
    {
    }

    public function saveGroupFeedbackMessage(string|int $project_id, string|int $group_id, string $msg)
    {
    }
}
