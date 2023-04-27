<?php

namespace App\Database;

require_once __DIR__ . "/../../vendor/autoload.php";

use Core\Config;

/**
 * Class description
 * 
 * Singleton class used to establish a database connection
 *
 */
class Database
{
    /**
     * @var Database $instance
     */
    private static $instance = null;

    /**
     * @var \PDO $pdo
     */
    private \PDO $pdo;

    /**
     * Constructor
     * 
     * This establishes a connection with the database.
     * 
     * @access private
     * @throws \Exception thrown incase of failure
     * 
     */
    private function __construct()
    {
        try {

            // Linux config 

            // $this->pdo = new \PDO('mysql:host=127.0.0.1;dbname=login_auth_rest_api;port=3306', 'marshal', 'password_is_2021_ID');

            // WAMPP config

            // database 1.0
            // $this->pdo = new \PDO('mysql:host=localhost;dbname=marshal', 'root', '');

            // database 2.0
            $this->pdo = new \PDO('mysql:host=localhost;dbname=marshal3_0', 'root', '');

            // enabling PDO errors	
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * Function description
     * 
     * Used to create an instance of this class
     * @access public
     * @static
     * @return Database
     * 
     */
    public static function getInstance(): Database
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Function description
     * 
     * Getter method for the $pdo
     * @access public
     * @return \PDO
     * 
     */
    public function getPDO(): \PDO
    {
        return $this->pdo;
    }
}
