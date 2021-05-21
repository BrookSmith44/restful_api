<?php
/**
 * 
 * File to store all database queries
 * 
 */

 namespace Database;

 class SQLQueries {
     // Properties

     // Magic construct and destruct methods
     public function __construct() {}

     public function __destruct() {}

     // Methods
     public function getAllUsers() {
         $query_string = "SELECT * FROM users ORDER BY user_id DESC";

         return $query_string;
     }

     public function searchUser() {
        $query_string = "SELECT * FROM users WHERE FirstName LIKE :param_search OR ";
        $query_string .= "Surname LIKE :param_search OR ";
        $query_string .= "DateOfBirth LIKE :param_search OR ";
        $query_string .= "PhoneNumber LIKE :param_search OR ";
        $query_string .= "email LIKE :param_search";

        return $query_string;
    }

    public function insertUser() {
        $query_string = "INSERT INTO users(FirstName, Surname, DateOfBirth, PhoneNumber, Email) ";
        $query_string .= "VALUES (:param_fname, :param_surname, :param_dob, :param_phoneNo, :param_email)";

        return $query_string;
    }

    public function updateUser() {
        $query_string = "UPDATE users SET FirstName = :param_fname, Surname = :param_surname, DateOfBirth = :param_dob, ";
        $query_string .= "PhoneNumber = :param_phoneNo, Email = :param_email WHERE user_id = :param_id";

        return $query_string;
    }

    public function deleteUser() {
        $query_string = "DELETE FROM users WHERE user_id = :param_id";

        return $query_string;
    }
 }