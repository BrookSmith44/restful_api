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
    private $db_wrapper;
    private $logger;

    // Magic construct and destruct methods
    public function __construct($logger) {
        $this->search_param = null;
        $this->id = null;
        $this->fname = null;
        $this->surname = null;
        $this->dob = null;
        $this->phoneNo = null;
        $this->email = null;
        $this->db_wrapper = new \Database\DatabaseWrapper;
        $this->logger = $logger;
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

    public function setConnectionSettings($connection_settings) {
        $this->connection_settings = $connection_settings;
    }

    public function setLogger($logger) {
        $this->logger = $logger;
    }

    // Methods
    // Connection method
    public function connect() {
        // Set local variables
        $db_wrapper = $this->db_wrapper;
        $logger = $this->logger;
        $success = [];

        // Set database properties
        $db_wrapper->setLogger($logger);

        // Make connections
        $success['result'] = $db_wrapper->makeDbConnection();

        if ($success['result'] == false) {
            $success['message'] = 'Failed to connect to the database';
        }

        return $success;
    }

    public function getUsers($query_string) {
        // Make database connection 
        $connection_success = $this->connect();

        // Set local variables
        $search_param = $this->search_param;
        $db_wrapper = $this->db_wrapper;

        // Empty array for results
        $results = [];

        // Check if connection was made successfully
        if ($connection_success['result'] == true) {

            if (isset($search_param)) {
                // Get specific user query string
                // Set empty parameters
                $query_parameters = [
                    ':param_search' => '%' . $search_param . '%'
                ];
            } else {
                $query_parameters = [];
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

    public function createUser($query_string) {
        // Make database connection
        $connection_success = $this->connect();

        // Set local variables
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

    public function updateUser($query_string) {
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

    public function deleteUser($query_string) {
        // Connect to database
        $connection_success['result'] = $this->connect();

        // Set local variables
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