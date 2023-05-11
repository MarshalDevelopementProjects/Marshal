<?php

// TODO :: WHEN THERE ARE NO CLIENTS PLEASE RESOLVE THIS

namespace App\Controller\Client;

use App\Controller\Conference\ConferenceController;
use App\Controller\PDF\PDFController;
use App\Controller\User\UserController;
use App\Model\Client;
use App\Model\Project;
use Exception;

require __DIR__ . '/../../../vendor/autoload.php';

class ClientController extends UserController
{
    private Client $client;

    private Project $project;

    private ConferenceController $conferenceController;

    public function __construct()
    {
        try {
            parent::__construct();
            if (array_key_exists("project_id", $_SESSION) && $this->user->checkUserRole(req_id: $_SESSION["project_id"], role: "CLIENT", type: "PROJECT")) {
                $this->client = new Client($_SESSION["project_id"]);
                $this->project = new Project($this->user->getUserData()->id, $_SESSION["project_id"]);
                $this->conferenceController = new ConferenceController();
            } else {
                $this->sendResponse(
                    view: "/errors/403.html",
                    status: "unauthorized"
                );
                // throw new Exception("Bad request missing arguments");
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // save the message to the project table
    // $args format {"message" => "message string"}
    public function postMessageToProjectFeedback(array|object $args): void
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        try {
            if (!empty($args) && array_key_exists("message", $args)) {
                if (!empty($args["message"])) {
                    $id = $this->user->getUserData()->id;
                    if ($this->client->saveProjectFeedbackMessage(id: $id, msg: $args["message"])) {
                        $this->sendJsonResponse("success");
                    } else {
                        $this->sendJsonResponse("internal_server_error", ["message" => "Message cannot be saved!"]);
                    }
                } else {
                    $this->sendJsonResponse("error", ["message" => "Empty message body!"]);
                }
            } else {
                $this->sendJsonResponse("error", ["message" => "Bad request"]);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    public function getProjectFeedbackMessages(): void
    {
        // TODO: NEED TO HAVE MESSAGE VALIDATION TO DETECT ANY UNAUTHORIZED CHARACTERS
        try {
            if ($this->client->getProjectFeedbackMessages()) {
                $this->sendJsonResponse("success", ["message" => "Successfully retrieved", "messages" => $this->client->getMessageData() ?? []]);
            } else {
                $this->sendJsonResponse("error", ["message" => "Some error occurred"]);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * ###Function description###
     * Redirects a project leader to the meeting/conference page for video chatting
     * Function returns nothing and accept no arguments
     * @throws Exception
     */
    public function gotoConference(array $args): void
    {
        if($args && sizeof($args) === 1  && array_key_exists("conf_id", $args)) {
            $args["status"] = "DONE";
            $returned = $this->conferenceController->changeConferenceStatus(args: $args);
            if (is_bool($returned) && $returned) {
                $this->sendResponse(
                    view: "/user/meeting.html",
                    status: "success",
                    // TODO: PASS THE NECESSARY INFORMATION OF THE REDIRECTING PAGE
                    content: [
                        "user_data" => [
                            "username" => $this->user->getUserData()->username,
                            "profile_picture" => $this->user->getUserData()->profile_picture,
                        ],
                        "peer" => $this->project->getProjectMembersByRole($_SESSION["project_id"], "LEADER") && !empty($this->project->getProjectMemberData()) ? $this->project->getProjectMemberData()[0] : [],
                        "project_id" => $_SESSION["project_id"],
                    ]
                );
            }
        } else {
            $this->sendResponse(
                view: 404,
                status: "error",
                content: [
                    "message" => "Requested service cannot be found"
                ]
            );
        }
    }

    /**
     * ###Function description###
     * Redirects a project leader to the meeting/conference scheduling page to
     * schedule a video conference or to check the conferences
     * Function returns nothing and accept no arguments
     * @throws Exception
     */
    public function gotoConferenceScheduler(): void
    {
        $this->sendResponse(
            view: "/client/meeting_schedule_page.html",
            status: "success",
            // TODO: PASS THE NECESSARY INFORMATION OF THE REDIRECTING PAGE
            content: [
                "message" => "Successfully retrieved",
                "user_data" => [
                    "username" => $this->user->getUserData()->username,
                    "profile_picture" => $this->user->getUserData()->profile_picture
                ],
                "project_conference_details" => $this->conferenceController->getScheduledConferenceDetailsByProject(
                    id: $this->user->getUserData()->id,
                    project_id: $_SESSION["project_id"],
                    initiator: "CLIENT"
                ),
                "all_conference_details" => $this->conferenceController->getScheduledConferenceDetails(
                    id: $this->user->getUserData()->id,
                    initiator: "CLIENT"
                ),
                "leaders_of_the_project" => $this->project->getProjectMembersByRole(
                    project_id: $_SESSION["project_id"],
                    role: "LEADER"
                ) ? $this->project->getProjectMemberData() : [],
            ]
        );
    }

    /**
     * ###Function description###
     * Schedule a conference using the valid information provided by the
     * client, if invalid information was provided the user will be
     * informed
     */
    public function ScheduleConference(array $args): void
    {
        try {
            $args["client_id"] = $this->user->getUserData()->id;
            if ($this->project->getProjectMembersByRole(project_id: $_SESSION["project_id"], role: "LEADER")) {
                if (!empty($this->project->getProjectMemberData())) {
                    $args["leader_id"] = $this->project->getProjectMemberData()[0]->id;
                    $args["project_id"] = $_SESSION["project_id"];
                    $returned = $this->conferenceController->scheduleConference(args: $args);
                    if (is_bool($returned) && $returned) {
                        $this->sendJsonResponse(status: "success", content: [
                            "message" => "Meeting was successfully added to the schedule",
                        ]);
                    } else {
                        $this->sendJsonResponse(status: "error", content: [
                            "message" => "Meeting cannot be successfully scheduled",
                            "errors" => $returned
                        ]);
                    }
                } else {
                    $this->sendJsonResponse(status: "error", content: []);
                }
            } else {
                $this->sendJsonResponse(status: "error", content: []);
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * ####Function description####
     * Used to change the status code of a given Schedule
     * Check the model of the conference controller to get an IDEA<br>
     * ANY CONFERENCE WITH THE STATUS CODE "CANCELLED" CANNOT BE ALTERED
     *
     * The format of the args should be  => [
     *                                          "conf_id" => "id of the conference that we are going to change the status of",
     *                                          "status"  => "The new status"
     *                                      ]
     *
     *
     * */
    public function setStatusOfSchedule(array $args): void
    {
        try {
            $returned = $this->conferenceController->changeConferenceStatus($args);
            if (is_bool($returned) && $returned) {
                $this->sendJsonResponse("success", ["message" => "Status is successfully changed"]);
            } else {
                $this->sendJsonResponse("error", ["message"  => "Status cannot be changed", "errors" => $returned]);
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * ###Function description###
     * Find conference details of a particular client
     * (all the conference scheduled will be returned to the user regardless the project)
     */
    public function getScheduledConferenceDetails(): void
    {
        // TODO: NOTHING TO CHECK JUST RETURN THE DATA OF THE CLIENT
        try {
            $returned = $this->conferenceController->getScheduledConferenceDetails(
                id: $this->user->getUserData()->id,
                initiator: "CLIENT"
            );
            $this->sendJsonResponse("success", ["message" => "Data retrieved", "conferences" => $returned]);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function getScheduledConferenceDetailsOfProject(): void
    {
        try {
            $returned = $this->conferenceController->getScheduledConferenceDetailsByProject(
                id: $this->user->getUserData()->id,
                project_id: $_SESSION["project_id"],
                initiator: "CLIENT"
            );
            $this->sendJsonResponse("success", ["message" => "Data retrieved", "conferences" => $returned]);
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    public function generateProjectReport(): void
    {
        try {
            $pdfGenerator = new PDFController();
            // TODO: GET THE PROJECT DATA HERE
            if ($this->client->getPDFData(project_id: $_SESSION["project_id"])) {
                $data = $this->client->getProjectData();
                $pdfGenerator->generateGeneralFormatPDF(
                    path_to_html_markup: "/View/src/client/pdf-templates/pdf-template.html",
                    path_to_style_sheet: "/View/src/client/pdf-templates/pdf-styles.css",
                    file_name: "Report.pdf",
                    attributes: $data
                );
            } else {
                $this->sendResponse(
                    view: "/error/505.html",
                    status: "error",
                    content: [
                        "message" => "Pdf file cannot be generated, Sorry for the inconvenience"
                    ]
                );
            }
        } catch (Exception $exception) {
            throw $exception;
        }
    }
}