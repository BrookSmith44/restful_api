<?php

namespace Controller;

 // Get Request and Response
 use \Psr\Http\Message\ServerRequestInterface as Request;
 use \Psr\Http\Message\ResponseInterface as Response;

class AuthController {
    // properties
    private $app;
    
    // Magic methods
    public function __construct($app) {
        $this->app = $app;
    }

    public function __destruct() {}

    // Methods
    public function authorization() {
        // Get containers
        $authentication = $this->app->get('authentication');

        // Create api key
        $authorization = $authentication->initiateAuthentication();
        
        // Create empty variable for response
        $response_params;

        if ($authorization) {
            $response_params['code'] = http_response_code(200);
            echo json_encode(
                $authorization['token']
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
}