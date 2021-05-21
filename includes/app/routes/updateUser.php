<?php
/**
 * Update Users Route
 */

 // Get Request and Response
 use \Psr\Http\Message\ServerRequestInterface as Request;
 use \Psr\Http\Message\ResponseInterface as Response; 

 $app->put('/api/users/update/{id}', function(Request $request, Response $response, $args) use ($app) {

    // Get request values
    $put_values = $request->getParsedBody();

    // Validate request values
    $cleaned_values = validateRequestValues($app, $put_values);

    // Call function to update user
    $store_results = handleUser($app, $cleaned_values, $args, 'update');

    echo json_encode($store_results['message']);

    // Return status response
    return $response->withStatus($store_results['status'])->withHeader('content-type', 'application/json');

     
 })->setName('UpdateUser');