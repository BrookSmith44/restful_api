<?php
/**
 * Delete User Route
 */

 // Get Request and Response
 use \Psr\Http\Message\ServerRequestInterface as Request;
 use \Psr\Http\Message\ResponseInterface as Response; 

 $app->delete('/api/users/delete/{id}', function(Request $request, Response $response, $args) use ($app) {

   // Call function to delete user
   $store_result = handleUser($app, '', $args, 'delete');
   
   echo json_encode($store_result['message']);

   return $response->withStatus($store_result['status'])->withHeader('content-type', 'application/json');

})->setName('Homepage');
