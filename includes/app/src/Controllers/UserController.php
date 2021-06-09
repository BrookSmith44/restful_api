<?php

namespace Controller;

 // Get Request and Response
 use \Psr\Http\Message\ServerRequestInterface as Request;
 use \Psr\Http\Message\ResponseInterface as Response;

class UserController {
    // Properties
    private $app;
    // Magic methods
    public function __construct($app) {
        $this->app = $app;
    }

    public function __destruct() {}


    // Methods
    public function index($request, $response) {
        
        return $this->app->view->render($response,
        'homepage.html.twig',
        [
           'title' => 'Intouch Assessment',
           'css_path' => CSS_PATH,
           'js_path' => JS_PATH,
           'validate' => VALIDATE_PATH,
           'action' => 'api/users/insert'
        ]);
    }

    public function users($request, $response, $args) {
        // Authenticate before allowing access to database
        $authorization = $this->authenticate($request);

        // Empty array for results
        $results = [];

        // Check if authorization has been successful
        if ($authorization['result'] == true) {
            // Call function to return users
            $results = $this->retrieveUsers($args);

            // empty variable for message
            $message = [];

            // Check if results have been set
            if (isset($results['results'])) {
                $message['response'] =  $results['results'];
            } else {
                $message['response'] = $results['message'];
            }

            // Convert into JSON
            echo json_encode($message['response']);

            
        } else {
            // Create error
            $results = [
                'result' => false,
                'message' => 'Token Authentication Failed',
                'status' => 401
            ];
        }
        
        // Output users or error in json format
        return $response->withStatus($results['status'])->withHeader('Content-Type', 'application/json');
    }

    public function createUser($request, $response, $args) {
        // Authenticate before allowing access to database
        $authorization = $this->authenticate($request);

        // empty array for results
        $results = [];

        // Check if authorization has been successful
        if ($authorization['result'] == true) {

            // Get request values
            $post_values = $request->getParsedBody();
        
            // Validate all data
            $cleaned_values = $this->validateRequestValues($post_values);
            
            // Call function to create user and return 
            $results = $this->handleUser($cleaned_values, $args, 'create');
            
        } else {
            // Create error
            $results = [
                'result' => false,
                'message' => 'Token Authentication Failed',
                'status' => 401
            ];
        }
    
        // Return JSON error if storage failed
        echo json_encode($results['message']);
    
        return $response->withStatus($results['status'])->withHeader('content-type', 'application/json');   
     }

     public function updateUser($request, $response, $args) {
         // Authenticate before allowing access to database
        $authorization = $this->authenticate($request);

        // empty array for results
        $results = [];
        
        // Check if authorization has been successful
        if ($authorization['result'] == true) {

            // Get request values
            $put_values = $request->getParsedBody();

            // Validate request values
            $cleaned_values = $this->validateRequestValues($put_values);

            // Call function to update user
            $results = $this->handleUser($cleaned_values, $args, 'update');

        } else {
            // Create error
            $results = [
                'result' => false,
                'message' => 'Token Authentication Failed',
                'status' => 401
            ];
        }
    
        // Return JSON error if storage failed
        echo json_encode($results['message']);
    
        return $response->withStatus($results['status'])->withHeader('content-type', 'application/json');
     }

     public function deleteUser($request, $response, $args) {
        // Authenticate before allowing access to database
        $authorization = $this->authenticate($request);

        // empty array for results
        $results = [];
        
        // Check if authorization has been successful
        if ($authorization['result'] == true) {

            // Call function to delete user
            $results = $this->handleUser('', $args, 'delete');

        } else {
            // Create error
            $results = [
                'result' => false,
                'message' => 'Token Authentication Failed',
                'status' => 401
            ];
        }
    
        // Return JSON error if storage failed
        echo json_encode($results['message']);
    
        return $response->withStatus($results['status'])->withHeader('content-type', 'application/json'); 
     }
    
     // Functions 
     // Validate post values
     public function validateRequestValues( $values) {
         // Get validator container
         $validator = $this->app->get('validator');
    
         // Empty array for cleaned values
         $cleaned_values = [];
         
         // Sanitize values
         $cleaned_values['fname'] = $validator->sanitizeString($values['FirstName']);
         $cleaned_values['surname'] = $validator->sanitizeString($values['Surname']);
         $cleaned_values['dob'] = $validator->sanitizeDate($values['DateOfBirth']);
         $cleaned_values['phoneNo'] = $validator->sanitizeString($values['PhoneNumber']);
         $cleaned_values['email'] = $validator->sanitizeEmail($values['Email']);
    
         // Return cleaned values
         return $cleaned_values;
     }
    
     // Function to create/update user
     public function handleUser($cleaned_values, $args, $operation) {
         // Get user model
         $user_model = $this->app->get('userModel');
         $sql_queries = $this->app->get('sqlQueries');

         // Set user properties for create and update functionality
         if ($operation == 'create' || $operation == 'update') {
            // Call function to set the user class properties
            $this->setUserProperties($user_model, $cleaned_values);
         }
    
         // Set user id for update and delete functionality
         if ($operation == 'update' || $operation == 'delete') {
            // Get id from route parameters
            $id = $args['id'];
            // Set ID of user to be altered
            $user_model->setUserId($id);
         }
    
         // Switch case to deciper create or update
         switch ($operation) {
             case 'create':
                // Get query string to insert user
                $query_string = $sql_queries->insertUser();
                // Call user model method to create user
                $store_result = $user_model->createUser($query_string);
                break;
            case 'update':
                // Get query string to update user
                $query_string = $sql_queries->updateUser();
                // Call user model method to create user
                $store_result = $user_model->updateUser($query_string);
                break;
            case 'delete':
                // Get query string to delete user
                $query_string = $sql_queries->deleteUser();
                // Call user model method to create user
                $store_result = $user_model->deleteUser($query_string);
                break;
         }
    
         return $store_result;
     }
    
     // Set user properties
     public function setUserProperties($user_model, $properties) {
         // set properties
         $user_model->setFname($properties['fname']);
         $user_model->setSurname($properties['surname']);
         $user_model->setDob($properties['dob']);
         $user_model->setPhoneNo($properties['phoneNo']);
         $user_model->setEmail($properties['email']);
     }

    // Methods
    public function retrieveUsers($args) {
        // Set containers and get model
        $user_model = $this->app->get('userModel');
        $sql_queries = $this->app->get('sqlQueries');
        // Empty variable for results
        $results = [];
        // Set empty variables for query parameters and string
        $query_string;

        if (isset($args['search'])) {
            // Set search parameter
            $user_model->setSearchParameter($args['search']);
            // Get specific user query string
            $query_string = $sql_queries->searchUser();
        } else {
            // Get all users query string
            $query_string = $sql_queries->getAllUsers();
        }
    
        // Call method to return users
        $results = $user_model->getUsers($query_string);
    
        // Return results
        return $results;
    }

    public function authenticate($request) {
        // Get containers
        $authorization = $this->app->get('authentication');

        // Get authorization key from URL
        $api_key = $request->getParam('auth');

        // Set api key property
        $authorization->setApiKey($api_key);

        // Authenticate before allowing access to database
        $authorization = $authorization->authenticate();

        return $authorization;
    }
}