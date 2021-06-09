<?php
/**
 * Authorization route Route
 */

 // Get Request and Response
 use \Psr\Http\Message\ServerRequestInterface as Request;
 use \Psr\Http\Message\ResponseInterface as Response; 

 $app->get('/auth', '\Controller\AuthController:authorization')->setName('Authorization');

