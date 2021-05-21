<?php
/**
 * 
 * Wrapper for the libsodium library
 * 
 * Encrypt and decrypt strings
 * Encrypt and decrypt using base64 encoding
 * 
 */

 namespace Encryption;

class LibSodiumWrapper {
    // Properties
    private $key;

    // Magic Construct Method - executes when an instance of the class is created
    public function __construct() {
        // Run method to create key
        $this->initiateEncryption();
    }

    // Magic Destruct Method - Executes when an instance of the class no longer has any references
    public function __destruct() {
        // Sodium Memzero overwrites buf with zeros
        sodium_memzero($this->key);
    }

    // Method to create key for encryption 
    private function initiateEncryption() {
        // Create key
        $this->key = 'Charlie the dog barks twice 2day';

        // Check the key is the correct size
        if (mb_strlen($this->key, '8bit') !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            // Throw error exception - Output error message
            throw new \RangeException('Key must be 32 bytes');

        }
    }

    // Method to encrypt string
    public function encryption($string) {
        // Create a nonce
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        // Create empty array to return encryption array
        $encryption_data = [];

        // Empty encryption string
        $encryption_string = '';

        // Encrypt Message
        $encryption_string = sodium_crypto_secretbox($string, $nonce, $this->key);

        // Store encrypted data in array
        // Nonce has to be stored with the encrypted data for later decryption
        $encryption_data['nonce'] = $nonce;
        $encryption_data['encrypted_string'] = $encryption_string;
        $encryption_data['nonce_and_encrypted_string'] = $nonce . $encryption_string;

        // Overwrite string buf with zeros
        sodium_memzero($string);

        // Return encryption data array
        return $encryption_data;
    }

    public function decryption($base64, $string) {
        // Create string for decryption data to be returned
        $decrypted_string = '';

        // Decode string using base64 decode method 
        $decoded = $base64->decode($string);

        // Check the decode worked - if not throw error exception
        if ($decoded === false) {
            // Output error message 
            throw new \Exception('Decoding failed');
        }

        // Check the message is the right size and was not truncated
        if (mb_strlen($decoded, '8bit') < (SODIUM_CRYPTO_SECRETBOX_NONCEBYTES + SODIUM_CRYPTO_SECRETBOX_MACBYTES)) {
            // Output error message
            throw new \Exception('The message was truncated');
        }

        // Get the nonce from the decoded variable
        $nonce = mb_substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');

        // Get cipher text from decoded variable
        $cipher = mb_substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');

        // Decrypt function
        $decrypted_string = sodium_crypto_secretbox_open($cipher, $nonce, $this->key);

        // if decryption fails throw error
        if ($decrypted_string === false) {
            throw new \Exception('Decryption Failed');
        }

        sodium_memzero($cipher);

        // Return decryted string
        return $decrypted_string;
    }
}