<?php

namespace Core\Validator;

require __DIR__ . "/../../vendor/autoload.php";

use App\CrudUtil\CrudUtil;
use Exception;

/**
 * Class description
 * 
 * Encapsulates the validation tasks performed on user input or query parameters
 *  
 */
class Validator
{
    /**
     * @access private
     * @var array $errors stores the violation results if there are any
     *                      when validating a set of values
     */
    private array $errors = array();

    /**
     * @access private
     * @var bool $passed the results of the validation 
     */
    private bool $passed = false;

    /**
     * @access private
     * @var CrudUtil $crud_util CRUD object 
     */
    private CrudUtil $crud_util;


    /**
     * @access public
     * @return Validator
     * @throws Exception
     */
    public function __construct()
    {
        try {
            $this->crud_util = new CrudUtil();
        } catch (Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Function description
     *
     * @access public
     * @param array $values takes an associative array to validate
     * @param string $schema takes schema to get the validation rules
     *
     * Used to validate set of values depending on a schema
     * example => validation user login inputs sent by the client
     *            read the validation rules from the schemas in this case it's login
     *            then, evaluate the values based on the rules defined under that
     * See the Schemas/ directory for validation schemas and rules
     *
     * @return void
     * @throws Exception
     */
    public function validate(array $values = array(), string $schema = ""): void
    {
        if (!empty($schema)) {
            try {
                $requirements = $this->readRules($schema);
                if (!empty($values)) {
                    foreach ($values as $key => $value) {
                        foreach ($requirements[$key] as $rule => $rule_value) {
                            switch ($rule) {
                                case 'required': {
                                        if ($rule_value && empty($value))
                                            $this->errors[] = str_replace('_', ' ', $key) . " is required";
                                    }
                                    break;
                                case 'min': {
                                        if (strlen($value) < $rule_value)
                                            $this->errors[] = str_replace('_', ' ', $key) . " needs to have a minimum length of " . str_replace('_', ' ', $rule_value) . " characters";
                                    }
                                    break;
                                case 'max': {
                                        if (strlen($value) > $rule_value)
                                            $this->errors[] = str_replace('_', ' ', $key) . " needs to have a maximum length of " . str_replace('_', ' ', $rule_value) . " characters";
                                    }
                                    break;
                                case 'email': {
                                        if (!filter_var($value, FILTER_VALIDATE_EMAIL))
                                            $this->errors[] = str_replace('_', ' ', $key) . " is not in a valid format";
                                    }
                                    break;
                                case 'unique': {
                                        if ($rule_value) {
                                            $sql_string = "SELECT {$key} FROM {$requirements["table"]} WHERE {$key} = :{$key}";
                                            $result = $this->crud_util->execute($sql_string, array("{$key}" => $value));
                                            if ($result->getCount()) {
                                                $this->errors[] = str_replace('_', ' ', $key) . " already exists";
                                            }
                                        }
                                    }
                                    break;
                                case 'format': {
                                        if (!preg_match($rule_value, $value)) {
                                            $this->errors[] = str_replace('_', ' ', $key) . " is not in a valid format";
                                        }
                                    }
                                    break;
                                case 'match': {
                                        if ($value !== $values[$rule_value]) {
                                            $this->errors[] = str_replace('_', ' ', $rule_value) . " must match " . str_replace('_', ' ', $key);
                                        }
                                    }
                                    break;
                                case 'exists': {
                                        // check whether the property exists
                                    }
                                    break;
                                case 'enum': {
                                        if (!in_array($value, $rule_value)) {
                                            $this->errors[] = "Input is not valid, could be only one of the following $rule_value";
                                        }
                                    }
                                    break;
                                default: {
                                        $this->errors[] = "{$rule} is not a valid rule";
                                    }
                                    break;
                            };
                        }
                    }
                    if (empty($this->errors)) $this->passed = true;
                    else $this->passed = false;
                }
            } catch (Exception $exception) {
                throw $exception;
            }
        }
    }

    /**
     * Function description
     * 
     * @param string $schema
     * @return array
     * @throws Exception if the schema is not a valid schema
     * 
     * used to read the rules of a given schema 
     * 
     */
    private function readRules(string $schema): array
    {
        // reading the requirements of the requested schema
        $file =  file_get_contents(__DIR__ . '/Schemas/' . $schema . '.json');
        if ($file) {
            return json_decode($file, true); // this will return an object
        } else {
            throw new Exception("Schema requested cannot be found check the schema once again");
        }
    }

    /**
     * Function description
     * 
     * @return bool 
     * getter method for @var bool $passed
     * 
     */
    public function getPassed(): bool
    {
        return $this->passed;
    }

    /**
     * Function description
     * 
     * @return array 
     * getter method for @var array $errors
     * 
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
