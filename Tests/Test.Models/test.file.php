<?php

require __DIR__ . '/../../vendor/autoload.php';

use App\Model\File;

function get_Unit_Test_Format_Heading(string $test_case):void
{
    echo('<br>');
    echo('<br>');
    echo("Test case : " . $test_case);
    echo('<br>================================================');
    echo('<br>');
}

function get_Unit_Test_Format_Passed_message(string $test_case):void
{
    echo('Test case ' . $test_case . ' passed');
    echo('<br>');
}

function get_Unit_Test_Format_failed_message(string $test_case):void
{
    echo('Test case ' . $test_case . ' failed');
    echo('<br>');
}

function get_test_result(string $test_case, $result, $expected_result_type):void
{
    switch($expected_result_type){
        case 'is empty':
            if(empty($result)){
                get_Unit_Test_Format_Passed_message($test_case);
            }else{
                get_Unit_Test_Format_failed_message($test_case);
            }
            break;
        case 'is object':
            if(is_object($result)){
                get_Unit_Test_Format_Passed_message($test_case);
            }else{
                get_Unit_Test_Format_failed_message($test_case);
            }
            break;
        case 'is array':
            if(is_array($result)){
                get_Unit_Test_Format_Passed_message($test_case);
            }else{
                get_Unit_Test_Format_failed_message($test_case);
            }
            break;
        case 'is false':
            if(!$result){
                get_Unit_Test_Format_Passed_message($test_case);
            }else{
                get_Unit_Test_Format_failed_message($test_case);
            }
            break;
        case 'is true':
            if($result){
                get_Unit_Test_Format_Passed_message($test_case);
            }else{
                get_Unit_Test_Format_failed_message($test_case);
            }
            break;
        default :
            echo 'test result is failed';
    }
}

function test_get_files(){

    $test_case = '1.1 test get files with valid arguments';
    get_Unit_Test_Format_Heading($test_case);

    $file = new File();

    $condition = "project_id = 4";
    $result = $file->getFiles($condition);
    get_test_result($test_case, $result, 'is array');
    


    $test_case = '1.2 test get files with invalid arguments';
    get_Unit_Test_Format_Heading($test_case);

    $file = new File();

    $condition = "projecwt_id = 4";
    $result = $file->getFiles($condition);
    get_test_result($test_case, $result, 'is empty');
    


    $test_case = '1.3 test get files with empty arguments';
    get_Unit_Test_Format_Heading($test_case);

    $file = new File();

    $condition = "";
    $result = $file->getFiles($condition);
    get_test_result($test_case, $result, 'is empty');


    $test_case = '1.4 test get files with null arguments';
    get_Unit_Test_Format_Heading($test_case);

    $file = new File();

    $condition = null;
    $result = $file->getFiles($condition);
    get_test_result($test_case, $result, 'is empty');
    
}

test_get_files();