<?php
/**
 * Homepage Route
 */

 // Get Request and Response
 use \Psr\Http\Message\ServerRequestInterface as Request;
 use \Psr\Http\Message\ResponseInterface as Response; 

 $app->get('/', function(Request $request, Response $response) use ($app) {

   return $this->view->render($response,
   'homepage.html.twig',
   [
      'title' => 'Intouch Assessment',
      'css_path' => CSS_PATH,
      'js_path' => JS_PATH,
      'validate' => VALIDATE_PATH,
      'action' => 'api/users/insert'
   ]);
})->setName('Homepage');


 // Functions
 // Set containers and container properties 
 function setContainers($app) {
    // get containers
    $db = $app->getContainer()->get('databaseWrapper');
    $user_model = $app->getContainer()->get('userModel');
    $settings = $app->getContainer()->get('settings');
    $pdo_settings = $settings['pdo_settings'];
    $sql_queries = $app->getContainer()->get('sqlQueries');
    $libsodium = $app->getContainer()->get('libSodiumWrapper');
    $base64 = $app->getContainer()->get('base64Wrapper');
    $authentication = $app->getContainer()->get('authentication');
    $logger = $app->getContainer()->get('logger');

    // Set db Properties
    $user_model->setDbWrapper($db);
    $user_model->setSQLQueries($sql_queries);
    $user_model->setLibsodium($libsodium);
    $user_model->setBase64($base64);
    $user_model->setAuthentication($authentication);
    $user_model->setConnectionSettings($pdo_settings);
    $user_model->setLogger($logger);

    return $user_model;
 }
