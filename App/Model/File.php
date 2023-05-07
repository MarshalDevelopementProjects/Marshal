<?php

namespace App\Model;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\CrudUtil\CrudUtil;

class File
{
    private CrudUtil $crud_util;
    public function __construct()
    {
        try {
            $this->crud_util = new CrudUtil();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getFiles($condition){
        $sql = "SELECT * FROM `files` WHERE ";
        $sql .= $condition;

        try {
            $result = $this->crud_util->execute($sql);
            if ($result->getCount() > 0) {
                return $result->getResults();
            } else {
                return array();
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}