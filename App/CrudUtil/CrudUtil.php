<?php

namespace App\CrudUtil;

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Database\Database;

class CrudUtil
{
    /**
     * @access private
     * @var Database|null $database
     */
    private Database|null $database = null;

    /**
     * @access private
     * @var \PDO $pdo
     */

    /**
     * @access private
     * @var bool $error
     */

    /**
     * @access private
     * @var array $results 
     */

    /**
     * @access private
     * @var int $count
     */

    /**
     * @access private
     * @var \PDOStatement|false $query
     */
    private $pdo = null,
        $error,
        $results,
        $count,
        $query;

    /**
     * Constructor
     * 
     * Creates a database connection and resets all the
     * variables used to their default values
     * @access public
     * @throws \Exception incase if anything goes wrong 
     * 
     */
    public function __construct()
    {
        try {
            $this->database = Database::getInstance();
            $this->pdo = $this->database->getPDO();
            $this->error = false;
            $this->count = 0;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    // the sql_string is of the named parameter format
    // the params array is of the form 'name' => value
    // reference => https://www.php.net/manual/en/pdostatement.bindparam.php
    /**
     * 
     * Method description
     * 
     * @param string $sql_string takes a sql statement as a string
     * @param array $params takes an array of key, value pairs as arguments to the query
     * @throws \Exception is thrown incase of an exception 
     * @return CrudUtil returns itself with the results encapsulated
     * 
     */
    public function execute(string $sql_string, array $params = array()): CrudUtil
    {
        // make a prepared statement here and call the execute function on the pdo
        // if there are results returns them in an array
        try {
            $this->error = false;
            // prepare the statement
            $this->query = $this->pdo->prepare($sql_string);
            // if there are parameters supplied bind them
            if (count($params) > 0) {
                // used named parameters when adding variables
                foreach ($params as $key => &$value) {
                    $this->query->bindParam($key, $value, \PDO::PARAM_STR);
                }
            }
            // execute the query
            $this->query->execute();
            $this->results = $this->query->fetchAll(\PDO::FETCH_OBJ);
            $this->count = $this->query->rowCount();
            return $this;
        } catch (\Exception $exception) {
            $this->error = true;
            $this->results = null;
            $this->count = 0;
            $this->database = null;
            throw $exception;
        }
    }

    /**
     * Method description
     * 
     * Getter method for @var $_results 
     * @access public
     * @return array
     * 
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Method description
     * 
     * Getter method for @var $_count
     * @access public
     * @return int
     * 
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Method description
     * 
     * Getter method of the first result stored
     * the array @var $_results
     * @access public
     * @return int
     * 
     */
    public function getFirstResult(): object|null|int|false|array
    {
        return $this->results[0];
    }

    /**
     * Method description
     * 
     * If any errors occurred @var $_error
     * will be set to true. The value stored
     * can be retrieve from this method
     * @access public
     * @return bool 
     * 
     */
    public function hasErrors(): bool
    {
        return $this->error;
    }
}
