<?php
/**
 * Wrapper class for base 64 encoding library 
 * 
 * Class to encode and decode strings 
 * 
 */

 namespace Encryption;

 class Base64Wrapper {
    // Methods
    public function __construct() {}

    public function __destruct() {}

    // Method to encode string
    public function encode($string) {
        // Initially set encode variable to false
        $encoded_string = false;

        // Check if string is not empty
        if(!empty($string)) {
            // Encode string
            $encoded_string = base64_encode($string);
        }

        // Return encoded variable
        return $encoded_string;
    }

    public function decode($string) {
        // Set decoded string variable to false initially
        $decoded_string = false;

        // Check if string is not empty
        if (!empty($string)) {
            $decoded_string = base64_decode($string);
        }

        // Return decoded variable
        return $decoded_string;
    }
 }