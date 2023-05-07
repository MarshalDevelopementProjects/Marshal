<?php

namespace Core;

require __DIR__ . "/../vendor/autoload.php";

use Core\Response;
use App\CrudUtil\CrudUtil;

/**
 * This class encapsulates all the functionalities required for 
 * uploading files
 */
class FileUploader
{
    /**
     * Here the capacity should be given in bytes (max_cap) by default the max file size is 1MB
     * , string $upload_to, string $upload_as = "", int $max_cap = 1048576
     * 
     * A field must contain the following 
     * 
     * array(
     *  $field_to_look_for => what is the field to look for in the $_FILES super global variable and the value for this is an array,
     *      array(
     *          $upload_to => directory to upload the file to
     *          $upload_as => a proper name to be given for the file
     *          $max_cap => maximum capacity that is applicable to this field
     *          $query => database query for uploading the file)
     *      )
     * )
     * 
     * 
     */
    public static function upload(array $allowed_file_types, array $fields)
    {
        if (!empty($_FILES)) {
            foreach ($fields as $key => $values) {
                $values["allowed_file_types"] = $allowed_file_types;
                $values["field_to_look_for"] = $key;
                $return_val = forward_static_call(
                    array(FileUploader::class, 'performChecksAndUpload'),
                    $values['allowed_file_types'],
                    $values['field_to_look_for'],
                    $values['upload_to'],
                    $values['upload_as'],
                    $values['max_cap'],
                    $values['query']
                );
                // if (!array_key_exists("max_cap", $values)) self::performChecks($allowed_file_types, $values["field"], $values["upload_to"], $values["upload_as"]);
                // else self::performChecks($allowed_file_types, $values["field"], $values["upload_to"], $values["upload_as"], $values["max_cap"]);
                if (!$return_val) {
                    return false;
                }
            }
            return true;
        } else {
            Response::sendJsonResponse(
                status: "error",
                content: array(
                    "message" => "Form submission without any content is not permitted"
                )
            );
            exit;
        }
    }

    private static function performChecksAndUpload(
        array $allowed_file_types,
        string|array $field_to_look_for,
        string $upload_to,
        string $upload_as = "",
        int $max_cap = 1048576,
        string $query = ""
    ) {
        if ($_FILES[$field_to_look_for]["error"] !== UPLOAD_ERR_OK) {
            switch ($_FILES[$field_to_look_for]["error"]) {
                case UPLOAD_ERR_PARTIAL:
                    Response::sendJsonResponse(
                        status: "error",
                        content: array(
                            "message" => "File only partially uploaded, try again"
                        )
                    );
                    exit;
                    break;
                case UPLOAD_ERR_NO_FILE:
                    Response::sendJsonResponse(
                        status: "error",
                        content: array(
                            "message" => "No file to upload"
                        )
                    );
                    exit;
                    break;
                case UPLOAD_ERR_EXTENSION:
                    Response::sendJsonResponse(
                        status: "internal_server_error",
                        content: array(
                            "message" => "File cannot be uploaded, an extension failure occurred"
                        )
                    );
                    exit;
                    break;
                case UPLOAD_ERR_INI_SIZE:
                    Response::sendJsonResponse(
                        status: "internal_server_error",
                        content: array(
                            "message" => "File cannot be uploaded, the maximum file size specified in the php.ini file is exceeded"
                        )
                    );
                    exit;
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    Response::sendJsonResponse(
                        status: "internal_server_error",
                        content: array(
                            "message" => "File cannot be uploaded, the temporary file directory doesn't exist"
                        )
                    );
                    exit;
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    Response::sendJsonResponse(
                        status: "internal_server_error",
                        content: array(
                            "message" => "File cannot be uploaded, cannot write to the temporary directory"
                        )
                    );
                    exit;
                    break;
                default:
                    Response::sendJsonResponse(
                        status: "error",
                        content: array(
                            "message" => "File cannot be uploaded, some unknown error occurred"
                        )
                    );
                    exit;
                    break;
            }
        } else {
            // check the maximum file size, if the incoming file exceeds this rate then reject the file
            if ($_FILES[$field_to_look_for]["size"] <= $max_cap) {
                // check for the valid file types
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mime_type = $finfo->file($_FILES[$field_to_look_for]["tmp_name"]);
                if (in_array($_FILES[$field_to_look_for]["type"], $allowed_file_types)) {
                    // if the file is valid move the file to a permanent location

                    $pathinfo = pathinfo($_FILES[$field_to_look_for]["name"]);
                    $base = $pathinfo["filename"];
                    $base = preg_replace("/[^\w-]/", "_", $base);
                    $filename = $base . "." . $pathinfo["extension"];

                    // $destination = $upload_to . '\\' . ($upload_as != "" ? $upload_as : $filename);
                    $destination = "D:/xampp/htdocs" . $upload_to . '/' . ($upload_as != "" ? $upload_as : $filename);
                    if (!move_uploaded_file($_FILES[$field_to_look_for]["tmp_name"], $destination)) {
                        // file cannot be moved failed to move the file
                        throw new \Exception("File cannot be moved due to permission errors");
                    } else {
                        // file successfully moved and added to the database
                        if (!empty($query)) {
                            try {
                                $crud_util = new CrudUtil();
                                $crud_util->execute($query, array($field_to_look_for => preg_replace("/\\\\/", "/", $upload_to . "/" . $filename)));
                            } catch (\Exception $exception) {
                                throw $exception;
                            }
                        }
                    }
                    return true;
                } else {
                    Response::sendJsonResponse(
                        status: "error",
                        content: array(
                            "message" => "Invalid file type!"
                        )
                    );
                    exit;
                }
            } else {
                Response::sendJsonResponse(
                    status: "error",
                    content: array(
                        "message" => "File cannot be uploaded, exceeded the maximum upload size"
                    )
                );
                exit;
            }
        }
    }
}
