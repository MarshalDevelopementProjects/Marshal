<?php

namespace App\Model;

use App\CrudUtil\CrudUtil;
use \Exception;

class Meeting
{
    private CrudUtil $crud_util;
    public function __construct()
    {
        try{
            $this->crud_util = new CrudUtil();
        } catch(Exception $exception) {
            // TODO: HANDLE THESE EXCEPTIONS IN A GOOD WAY
            throw $exception;
        }
    }

    // TODO: Decide the format of the data being sent
    // TODO: Add the newly scheduled meeting to the notification table as well
    public function scheduleMeeting(array $args) {
    }


    // TODO: QUERY THE SAME TABLE BUT WITH DIFFERENT IDS
    // TODO: ONLY FOR THE CLIENT AND THE PROJECT LEADER
    // TODO: PERHAPS EXTEND FOR GROUP MEMBERS AS WELL
    public function getScheduledMeetings(string|int $id) {
    }
}