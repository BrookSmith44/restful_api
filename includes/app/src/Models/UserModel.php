<?php

/**
 * 
 * Model class to handle all user data
 * 
 */

namespace Model;

 class UserModel {

    // Properties
    private $search_param;
    private $id;
    private $fname;
    private $surname;
    private $dob;
    private $phoneNo;
    private $email;
    private $key;
    private $db_wrapper;
    private $connection_settings;
    private $sql_queries;
    private $libsodium_wrapper;
    private $base64_wrapper;
    private $authentication;
    private $logger;

    // Magic construct and destruct methods
    public function __construct() {
        $this->search_param = null;
        $this->id = null;
        $this->fname = null;
        $this->surname = null;
        $this->dob = null;
        $this->phoneNo = null;
        $this->email = null;
        $this->key = null;
        $this->db_wrapper = null;
        $this->connection_settings = null;
        $this->sql_queries = null;
        $this->libsodium_wrapper = null;
        $this->base64_wrapper = null;
        $this->authentication = null;
        $this->logger = null;
    }

    public function __destruct() {}

    // Setter Methods
    public function setSearchParameter($search_param) {
        $this->search_param = $search_param;
    }
    
    public function setUserId($id) {
        $this->id = $id;
    }

    public function setFname($fname) {
        $this->fname = $fname;
    }

    public function setSurname($surname) {
        $this->surname = $surname;
    }

    public function setDob($dob) {
        $this->dob = $dob;
    }

    public function setPhoneNo($phoneNo) {
        $this->phoneNo = $phoneNo;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function setDbWrapper($db_wrapper) {
        $this->db_wrapper = $db_wrapper;
    }

    public function setConnectionSettings($connection_settings) {
        $this->connection_settings = $connection_settings;
    }

    public function setSqlQueries($sql_queries) {
        $this->sql_queries = $sql_queries;
    }

    public function setLibsodium($libsodium_wrapper) {
        $this->libsodium_wrapper = $libsodium_wrapper;
    }

    public function setBase64($base64_wrapper) {
        $this->base64_wrapper = $base64_wrapper;
    }

    public function setAuthentication($authentication) {
        $this->authentication = $authentication;
    }

    public function setLogger($logger) {
        $this->logger = $logger;
    }

    // Methods
    // Connection method
    public function connect() {
        // Set local variables
        $db_wrapper = $this->db_wrapper;
        $authentication = $this->authentication;
        $connection_settings = $this->connection_settings;
        $logger = $this->logger;
        $success = [];
        // Set authentication properties
        $authentication->setLibsodium($this->libsodium_wrapper);
        $authentication->setBase64($this->base64_wrapper);
        $authentication->setLogger($logger);

        // Authenticate before allowing access to database
        $authorization = $this->authentication->authenticate();

        // Check if authorization has been successful
        if ($authorization['result'] == true) {
            // Set database properties
            $db_wrapper->setDbConnectionSettings($connection_settings);
            $db_wrapper->setLogger($logger);

            // Make connections
            $success['result'] = $db_wrapper->makeDbConnection();

            if ($success['result'] == false) {
                $success['message'] = 'Failed to connect to the database';
            }
        } else {
            $success = [
                'result' => false,
                'message' => 'Token Authentication Failed',
                'status' => 401
            ];
        }

        return $success;
    }

    public function getUsers() {
        // Make database connection 
        $connection_success = $this->connect();

        // Set local variables
        $sql_queries = $this->sql_queries;
        $db_wrapper = $this->db_wrapper;
        $search_param = $this->search_param;
        // Empty array for results
        $results = [];

        // Check if connection was made successfully
        if ($connection_success['result'] == true) {

            if ($search_param !== null) {
                // Set empty parameters
                $query_parameters =[
                    ':param_search' => '%' . $search_param . '%'
                ];

                // Get specific user query string
                $query_string = $sql_queries->searchUser();
            } else {
                // Set empty parameters
                $query_parameters =[];

                // Get all users query string
                $query_string = $sql_queries->getAllUsers();
            }

            // Retrieve query results from database
            $results = [
                'results' => $db_wrapper->getValues($query_parameters, $query_string),
                'status' => 200
            ];

            // Create error object if no results are returned
            if (empty($results['results'])) {
                $results = [
                    'message' => 'User does not exist in database',
                    'status' => 200
                ];
            }
        } else {
            $results = [
                'result' => $connection_success['result'],
                'message' => $connection_success['message'],
                'status' => 409
            ];
        }

        // Encode in JSON format before returning datax      
        return $results;
    }

    public function createUser() {
        // Make database connection
        $connection_success = $this->connect();

        // Set local variables
        $sql_queries = $this->sql_queries;
        $db_wrapper = $this->db_wrapper;
        
        // Set local user variables
        $fname = $this->fname;
        $surname = $this->surname;
        $dob = $this->dob;
        $phoneNo = $this->phoneNo;
        $email = $this->email;

        // Empty array for results
        $store_results = [];

        // Check if connection was successful
        if ($connection_success['result'] == true) {

            // Set query parameters
            $query_parameters = [
                ':param_fname' => $fname,
                ':param_surname' => $surname,
                ':param_dob' => $dob,
                ':param_phoneNo' => $phoneNo,
                ':param_email' => $email
            ];

            // Get query string to insert user
            $query_string = $sql_queries->insertUser();

            // Call database wrapper method to store data
            $store_results = [
                'result' => $db_wrapper->storeData($query_parameters, $query_string),
                'message' => 'User successfully added to the system',
                'status' => 201
            ]; 

        } else {
            $store_results = [
                'result' => $connection_success['result'],
                'message' => $connection_success['message'],
                'status' => 409
            ];
        }

        return $store_results;
    }

    public function updateUser() {
        // Make database connection
        $connection_success = $this->connect();

        // Set local variables
        $sql_queries = $this->sql_queries;
        $db_wrapper = $this->db_wrapper;
        
        // Set local user variables
        $fname = $this->fname;
        $surname = $this->surname;
        $dob = $this->dob;
        $phoneNo = $this->phoneNo;
        $email = $this->email;
        $id = $this->id;

        // Empty array for results
        $store_results = [];

        // Check if connection was successful
        if ($connection_success['result'] == true) {

            // Set query parameters
            $query_parameters = [
                ':param_fname' => $fname,
                ':param_surname' => $surname,
                ':param_dob' => $dob,
                ':param_phoneNo' => $phoneNo,
                ':param_email' => $email,
                ':param_id' => $id
            ];

            // Get query string to update user
            $query_string = $sql_queries->updateUser();

            // Call database wrapper method to store data
            $store_results = [
                'result' => $db_wrapper->storeData($query_parameters, $query_string),
                'message' => 'User successfully updated in the system',
                'status' => 201
            ];

        } else {
            $results = [
                'result' => $connection_success['result'],
                'message' => $connection_success['message'],
                'status' => 409
            ];
        }

        return $store_results;
    }

    public function deleteUser() {
        // Connect to database
        $connection_success['result'] = $this->connect();

        // Set local variables
        $sql_queries = $this->sql_queries;
        $db_wrapper = $this->db_wrapper;
        
        // Set local user variables
        $id = $this->id;
        $store_results = [];

        // Check if connection was successful then run query
        // Check if connection was successful
        if ($connection_success == true) {

            // Set query parameters
            $query_parameters = [
                ':param_id' => $id
            ];

            // Get query string to delete user
            $query_string = $sql_queries->deleteUser();

            // Call database wrapper method to delete data
            $store_results = [
                'result' => $db_wrapper->storeData($query_parameters, $query_string),
                'message' => 'User successfully deleted from the system',
                'status' => 200
            ];

        } else {
            $store_results = [
                'result' => $connection_success['result'],
                'message' => $connection_success['message'],
                'status' => 409
            ];
        }

        return $store_results;
    }
 }