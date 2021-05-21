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

     // Magic methods
     public function __construct() {
         $this->key = null;
         $this->libsodium_wrapper = null;
         $this->base64_wrapper = null;
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

     public function setLibsodium($libsodium_wrapper) {
        $this->libsodium_wrapper = $libsodium_wrapper;
    }

    public function setBase64($base64_wrapper) {
        $this->base64_wrapper = $base64_wrapper;
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
        $base64_wrapper = $this->base64_wrapper;
        $libsodium_wrapper = $this->libsodium_wrapper;

        // Variable to store headers
        $headers = apache_request_headers();

        if (isset($headers['Authorization'])) {
            // Get authorization token from header
            $encrypted_token = $headers['Authorization'];

            // Variable to store success of authorization
            $authorization_success = [
                'result' => true
            ];

            try {
                // Decrypt token
                $token = $libsodium_wrapper->decryption(
                    $base64_wrapper,
                    $encrypted_token
                );
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