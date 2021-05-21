<?php
/**
 * Create Users Route
 */

 // Get Request and Response
 use \Psr\Http\Message\ServerRequestInterface as Request;
 use \Psr\Http\Message\ResponseInterface as Response; 

 $app->post('/api/users/insert', function(Request $request, Response $response, $args) use ($app) {

    // Get request values
    $post_values = $request->getParsedBody();

    // Validate all data
    $cleaned_values = validateRequestValues($app, $post_values);
    
    // Call function to create user and return 
    $store_result = handleUser($app, $cleaned_values, $args, 'create');

    // Return JSON error if storage failed
    echo json_encode($store_result['message']);

    return $response->withStatus($store_result['status'])->withHeader('content-type', 'application/json');

     
 })->setName('CreateUser');

 // Functions

 // Validate post values
 function validateRequestValues($app, $values) {
     // Get validator container
     $validator = $app->getContainer()->get('validator');

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
 function handleUser($app, $cleaned_values, $args, $operation) {
     // Set containers and  het user model
     $user_model = setContainers($app);

     // Set user properties for create and update functionality
     if ($operation == 'create' || $operation == 'update') {
        // Call function to set the user class properties
        setUserProperties($user_model, $cleaned_values);
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
            // Call user model method to create user
            $store_result = $user_model->createUser();
            break;
        case 'update':
            // Call user model method to create user
            $store_result = $user_model->updateUser();
            break;
        case 'delete':
            // Call user model method to create user
            $store_result = $user_model->deleteUser();
            break;
     }

     return $store_result;
 }

 // Set user properties
 function setUserProperties($user_model, $properties) {
     // set properties
     $user_model->setFname($properties['fname']);
     $user_model->setSurname($properties['surname']);
     $user_model->setDob($properties['dob']);
     $user_model->setPhoneNo($properties['phoneNo']);
     $user_model->setEmail($properties['email']);
 }