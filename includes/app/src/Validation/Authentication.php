<?php

/**
 * 
 * Class to handle token creation and authentication 
 * 
 */

 namespace Validation;

 use Firebase\JWT\JWT;

 class Authentication {
    // Properties
    private $key;
    private $libsodium_wrapper;
    private $base64_wrapper;
    private $api_key;

    // Magic methods
    public function __construct($logger) {
        $this->key = null;
        $this->libsodium_wrapper = null;
        $this->base64_wrapper = null;
        $this->api_key = null;
        $this->logger = $logger;
    }

    public function __destruct() {
        // Overwite key with 0's once all references to class have ended
        if ($this->key != null) {
            sodium_memzero($this->key);
        }
    }
    
    // Setter methods
    public function setKey() {
        // Set key
        $this->key = 'fudge the rabbit likes big hopsz';
    }

    public function setApiKey($api_key) {
        $this->api_key = $api_key;
    }

    public function setLogger($logger) {
        $this->logger = $logger;
    }

     // Method to authorize api access
    public function initiateAuthentication() {
        // Set key
        $this->setKey();
        // Time when authorization was issues
        $time_issued = time();
        // Set expiration time
        $expiration = $time_issued + 60 * 60;

        // payload to include the token issuer, audience, time issued and expiration time
        $payload = array(
            "iss" => "http://localhost:8080/REST_API/public/auth",
            "aud" => "http://localhost:8080/REST_API/public/",
            "iat" => $time_issued,
            "exp" => $expiration
        );

        // Encode token 
        $jwt = JWT::encode($payload, $this->key);

        // Return token values
        return array(
            'token' => $jwt
        );
    }

    public function authenticate() {
        // Set key
        $this->setKey();
        // Set local variables
        $key = $this->key;
        $logger = $this->logger;

        if (isset($this->api_key)) {
            // Get authorization token from header
            $token = $this->api_key;

            // Variable to store success of authorization
            $authorization_success = [
                'result' => true
            ];

            try {
                // Decode token
                $decoded_token = JWT::decode($token, $key, array('HS256'));
            } catch (\Exception $exception) {
                // Set failed authorization
                $authorization_success = [
                    'result' => false,
                    'message' => $exception->getMessage(),
                ];
                $logger->warning('Token Authentication Failed: ' . $authorization_success['message']);
            }
        } else {
            $authorization_success = [
                'result' => false,
                'message' => 'Authorization token was not provided',
            ];
            $logger->warning($authorization_success['message']);
        }

        return $authorization_success;
    }
 }