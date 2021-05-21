<?php
/**
 * Get Users Route
 */

 // Get Request and Response
 use \Psr\Http\Message\ServerRequestInterface as Request;
 use \Psr\Http\Message\ResponseInterface as Response; 

 $app->get('/api/users/fetch[/{search}]', function(Request $request, Response $response, $args) use ($app) {
    
   // Call function to return users
    $results = retrieveUsers($app, $args);

    // empty variable for message
    $message = [];

    if (isset($results['results'])) {
       $message['response'] =  $results['results'];
    } else {
      $message['response'] = $results['message'];
    }

    echo json_encode($message['response']);
    
    // Output users or error in json format
    return $response->withStatus($results['status'])->withHeader('Content-Type', 'application/json');

     
 })->setName('Users');

 function retrieveUsers($app, $args) {
    // Set containers and get model
    $user_model = setContainers($app);
    // Empty variable for results
    $results = [];
    // Check whether id parameter exists
    if ($args && !empty($args)) {
        // Set user id property
        $user_model->setSearchParameter($args['search']);
    }

    // Call method to return users
    $results = $user_model->getUsers();

    // Return results
    return $results;
 }