<?php

namespace Tests;

use Exception;

require __DIR__ . '/../vendor/autoload.php';

/**
 * This class can be used for unit testing and component testing
 * Simple construct +> Only for simple testing
 * */

abstract class Tester
{
    private int $number_of_test = 0;
    private int $passed_tests = 0;
    private int $failed_tests = 0;

    public function __construct(protected string $unit_name)
    {
        assert_options(ASSERT_ACTIVE,   true);
        assert_options(ASSERT_BAIL,     false);
        assert_options(ASSERT_WARNING,  false);
        assert_options(ASSERT_EXCEPTION,  false);
        assert_options(ASSERT_CALLBACK, Tester::class . "::assertHandler");
        echo "<br>";
        echo "<pre>";
        printf("=======================================================================\n");
        printf("==================== Testing :: {$this->unit_name} ===============\n");
    }

    private function assertHandler($file, $line, $code, $description = null): void
    {
        echo "<br>";
        printf("Assertion failed in the file :: %s\n At the line :: %d\n Code :: %s\n", $file, $line, $code);
        if($description) {
            printf(" Description :: %s\n", $description);
        }
    }

    protected function setup(): void {}

    public abstract function run(): void; // add all your test function calls to this

    protected function assertException(callable|string|object $callback, array $args, string $message = ""): void
    {
        $this->number_of_test++;
        try {

            if (is_string($callback)) {
                $parts = explode('::', $callback);
                if ($parts) {
                    $class_name = $parts[0];
                    if (class_exists($class_name)) {
                        $controller_object = new $class_name();
                        $action = $parts[1];
                        if (is_callable([$controller_object, $action])) {
                            $controller_object->$action($args);
                        }
                    }
                }
            } else {
                if (is_callable($callback)) {
                    call_user_func_array($callback, array("data" => $args));
                } else if (is_object($callback)) {
                    $callback->callback($args);
                }
            }

            $this->failed_tests++;
            printf("Test case number: %d => %s +> %s\n", $this->number_of_test, "Test case failed", "the given callback did not threw an exception.");
        } catch (Exception $exception) {
            if ($message) {
                if ($exception->getMessage() === $message) {
                    $this->passed_tests++;
                } else {
                    printf("Test case number: %d => %s\n", $this->number_of_test, "Test case failed");
                    $this->failed_tests++;
                }
            } else {
                $this->passed_tests++;
            }
        }
    }

    protected function assertTrue(mixed $value): void
    {
        $this->number_of_test++;
        if (assert($value == true)) {
            $this->passed_tests++;
        } else {
            printf("%s +> %s\n", "Test case failed", "{$value} is not equal to true.");
            $this->failed_tests++;
        }
    }

    protected function assertFalse(mixed $value): void
    {
        //var_dump($value);
        $this->number_of_test++;
        if (assert($value == false)) {
            $this->passed_tests++;
        } else {
            printf("%s +> %s\n", "Test case failed", "{$value} is not equal to false.");
            $this->failed_tests++;
        }
    }

    protected function assertNull(mixed $value): void
    {
        $this->number_of_test++;
        if (assert($value == null)) {
            $this->passed_tests++;
        } else {
            printf("%s +> %s\n", "Test case failed", "{$value} is not equal to false.");
            $this->failed_tests++;
        }
    }

    protected function assertEqual(mixed $value, mixed $target): void
    {
        $this->number_of_test++;
        if (assert($value == $target)) {
            printf("%s\n", "Test case successfully passed");
            $this->passed_tests++;
        } else {
            printf("%s\n", "Test case failed passed");
            $this->failed_tests++;
        }
    }

    protected function assertEqualWithTypeCheck(mixed $value, mixed $target): void
    {
        $this->number_of_test++;
        if (assert($value === $target)) {
            $this->passed_tests++;
        } else {
            printf("%s +> %s \n", "Test case failed", "{$value} if not the same as {$target}");
            $this->failed_tests++;
        }
    }

    public function summary(): void
    {
        echo "<br>";
        printf("Total number of test cases +> %d \n", $this->number_of_test);
        printf("Number of passed test cases +> %d \n", $this->passed_tests);
        printf("Number of failed test cases +> %d \n", $this->failed_tests);
        printf("=======================================================================\n");
        echo "</pre>";
    }
}