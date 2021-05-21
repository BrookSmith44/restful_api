<?php
/**
 * Authorization route Route
 */

 // Get Request and Response
 use \Psr\Http\Message\ServerRequestInterface as Request;
 use \Psr\Http\Message\ResponseInterface as Response; 

 $app->get('/auth', function(Request $request, Response $response) use ($app) {

    authorization($app);
})->setName('Authorization');

// Functions
function authorization($app) {
    // Get containers
    $authentication = $app->getContainer()->get('authentication');

    $authorization = $authentication->initiateAuthentication();

    $encrypted_data = encryptString($app, $authorization['token']);
    
    $response_params;

    if($authorization) {
        $response_params['code'] = http_response_code(200);
        echo json_encode(
            $encrypted_data
        );
    } else {
        $response_params['code'] = http_response_code(404);
        json_encode($response_params['error'] = [
            'type' => 'danger',
            'title' => 'failed',
            'text' => 'Authorization Failed'
        ]);
    }
}

// Encrypt string
function encryptString($app, $string) {
    // Get Encryption containers
    // Get lib sodium container
    $libsodium = $app->getContainer()->get('libSodiumWrapper');
    // Get base64 container
    $base64 = $app->getContainer()->get('base64Wrapper');

    // Set empty array for encrypted data
    $encrypted_data = [];

    // Encrypt string
    $encrypted_string['string_and_nonce'] = $libsodium->encryption($string);
    
    // Encode string
    $encrypted_data = $base64->encode($encrypted_string['string_and_nonce']['nonce_and_encrypted_string']);

    return $encrypted_data;
 }