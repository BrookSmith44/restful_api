<?php
/**
 * class to validate and santize any data from the front-end
 *
 * @author - Brook Smith
 */

namespace Validation;

class Validator {

    // Empty magic construct method
    public function __construct()
    {

    }
    // Empty magic destruct method
    public function __destruct() {

    }

    // Method to sanitize strings for safety (defends against sql injection, etc)
    // e.g. change single quotes to double quotes
    public function sanitizeString($string_to_sanitize) {
        // Set bool to false initially - this represents that the string has not been sanitized yet
        $sanitized_string = false;

        // if statement to ensure that variable has value set
        if (!empty($string_to_sanitize)) {
            // strips tags, strip special characters - filter_var will return bool
            $sanitized_string = filter_var($string_to_sanitize, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        }

        // Return sanitized value
        return $sanitized_string;
    }

    // Method to sanitize email
    public function sanitizeEmail($email_to_sanitize) {
        // Set variable to false initially - this represents email has not been sanitized yet
        $sanitized_email = false;

        // if statement to check variable has value set
        if (!empty($email_to_sanitize)) {
            // removed all special characters except letters, digits and some special characters such as "@"
            $sanitized_email = filter_var($email_to_sanitize, FILTER_SANITIZE_EMAIL);
        }

        // Return sanitized email
        return $sanitized_email;
    }

    public function sanitizeDate($date_to_sanitize) {
        // Set variable to false initially
        $sanitized_date = false;

        // Check parameter is not empty
        if (!empty($date_to_sanitize)) {
            // Remove any special characters from date
            $sanitized_date = preg_replace('#(\d{2})/(\d{2})/(\d{4})\s(.*)#', '$3-$2-$1 $4', $date_to_sanitize);

            // Convert string to date
            $converted_date = strtotime($sanitized_date);

            // Format date
            $formatted_date = date('Y-m-d', $converted_date);
        }

        // Return date
        return $formatted_date;
    }
}