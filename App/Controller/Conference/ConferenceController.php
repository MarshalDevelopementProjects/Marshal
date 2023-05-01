<?php

namespace App\Controller\Conference;

require __DIR__ . '/../../../vendor/autoload.php';

use Core\Validator\Validator;
use App\Model\Conference;
use Exception;

class ConferenceController
{

    private Validator $validator;
    private Conference $conference;

    public function __construct()
    {
        try {
           $this->validator = new Validator();
           $this->conference = new Conference();
        } catch (Exception $exception) {
            // TODO: Handle the exceptions
            throw $exception;
        }
    }

    /**
     * ###Function description###
     * Schedule a conference using the valid information provided by the
     * user, if invalid information was provided the user will be informed
     *
     * The check case for the function in somewhat ambiguous -> for the false this returns
     * an array and for failure or success the function returns -> false or true
     *
     * To avoid this use the function like this =>
     *
     * $returned = $conferenceController->scheduleConference($args)
     *
     * if(is_bool($returned) && $returned) {} => to check for success  or failure
     * else {
     *      print_r($returned) => this will give you the errors
     * }
     *
     */
    public function scheduleConference(array|object $args): array|bool
    {
        // TODO: Validate the $args
        var_dump($args);
        try {
            $this->validator->validate(values: $args, schema: "schedule_conference");
            if ($this->validator->getPassed()) {
                return $this->conference->scheduleConference($args);
            } else {
                return $this->validator->getErrors();
            }
        } catch(Exception $exception) {
            // TODO: Handle the exceptions
            throw $exception;
        }
    }

    /**
     * ###Function description###
     * Find conference details of a particular user
     * (all the conference scheduled will be returned to the user regardless the project)
     */
    public function getScheduledConferenceDetails(string|int $id, string $initiator): bool|array
    {
        if ($initiator === "CLIENT" || $initiator === "LEADER") {
            try {
                return $this->conference->getScheduledConferences(id: $id, role: $initiator) ?
                    $this->conference->getConferenceData() :
                    [];
            } catch (Exception $exception) {
                throw $exception;
            }
        }
        return false;
    }

    public function getScheduledConferenceDetailsByProject(string|int $id, string|int $project_id, string $initiator): bool|array
    {
        if ($initiator === "CLIENT" || $initiator === "LEADER") {
            try {
                return $this->conference->getScheduledConferencesByProject(id: $id, project_id: $project_id, role: $initiator) ?
                    $this->conference->getConferenceData() :
                    [];
            } catch (Exception $exception) {
                throw $exception;
            }
        }
        return false;
    }

    /**
     * ###Function description###
     * Controller function for changing the status of an existing conference that is not yet cancelled
     * Status of a cancelled function cannot be changed
    */
    public function changeConferenceStatus(array|object $args): bool|array
    {
        // TODO: used to change the status of a conference
        // TODO: Inform the other party as well that the meeting was completed
        // TODO: validate the conference id and status(if cancelled then the status cannot be changed)
        if (array_key_exists("conf_id", $args) && array_key_exists("status", $args)) {
            try {
                $this->validator->validate($args, "conference_status_change");
                if ($this->validator->getPassed()) {
                    return $this->conference->changeStatusOfConference(conf_id: $args["conf_id"], status: $args["status"]);
                } else {
                    return $this->validator->getErrors();
                }
            } catch (Exception $exception) {
                throw $exception;
            }
        } else {
            return false;
        }
    }
}