<?php
/** 
 * Database connection class
 */

namespace Database;

 class DatabaseWrapper {

     // properties
     private $db_connection_settings;
     private $db_handle;
     private $prepared_statement;
     private $errors;
     private $logger;

     // Maguc Methods
     public function __construct() {
         $this->db_connection_settings = null;
         $this->db_handle = null;
         $this->prepared_statement = null;
         $this->logger = null;
         $this->errors = [];
     }

     public function __destruct() {}


     // Setter Methods
     public function setDbConnectionSettings($db_connection_settings) {
         $this->db_connection_settings = $db_connection_settings;
     }

     public function setLogger($logger) {
        $this->logger = $logger;
    }


    // Methods
     public function makeDbConnection() {
         $pdo = false;
         $success = true;

         // Set local variables
         /*$db_settings = $this->db_connection_settings;
         $host_name = $db_settings['rdbms'] . ':host=' . $db_settings['host'];
         $port_number = ';port=' . $db_settings['port'];
         $user_db = ';dbname=' . $db_settings['db_name'];
         $host_details = $host_name . $port_number . $user_db;
         $username = $db_settings['username'];
         $password = $db_settings['password'];
         $pdo_attributes = $db_settings['options'];*/

         $db_settings = $this->db_connection_settings;
         $host_name = 'mysql' . ':host=' . 'localhost';
         $port_number = ';port=' . '3360';
         $user_db = ';dbname=' . 'intouch_assessment';
         $host_details = $host_name . $port_number . $user_db;
         $username = 'root';
         $password = '';
         $pdo_attributes =[
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => true,

        ];

         try {
            $pdo_handle = new \PDO($host_details, $username, $password, $pdo_attributes);
            $this->db_handle = $pdo_handle;
            $this->logger->notice("Connected to database successfully!");
         }
         catch(\PDOException $exception_object) {
            $success = false;
            $this->logger->warning("Failed to connect to database!");
         }

         return $success;
     }

     // Method to store data
     public function storeData($query_parameters, $query_string) {
 
         // Call method to query database
        $store_result =  $this->safeQuery($query_string, $query_parameters);

        return $store_result;
     }

     public function getValues($query_parameters, $query_string) {

        $this->safeQuery($query_string, $query_parameters);

        $results = $this->prepared_statement->fetchAll();
        
        return $results;
     }

     public function safeQuery($query_string, $params = null) {
        $store_result = false;
        $this->errors['db_error'] = false;
        $query_parameters = $params;

        try {
            $this->prepared_statement = $this->db_handle->prepare($query_string);
            $execute_result = $this->prepared_statement->execute($query_parameters);
            $this->errors['execute-OK'] = $execute_result;
            $store_result = true;
        } 
        catch (PDOException $exception_object) {
            $error_message = 'PDO Exception caught.';
            $error_message .= 'Error with the database access.' . "\n";
            $error_message .= 'SQL query: ' . $query_string . "\n";
            $error_message .= 'Error: ' . var_dump($this->prepared_statement->errorInfo(), true) . "\n";
            // Log this
            $this->errors['db_errors'] = true;
            $this->errors['sql_error'] = $error_message;
            $this->logger->warning($error_message);
        }

        return $store_result;
     }
 }