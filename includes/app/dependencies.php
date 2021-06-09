<?php
/**
 * Dependencies
 */

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FingersCrossedHandler;

 // Container for the view
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(
        $container['settings']['view']['template_path'],
        $container['settings']['view']['twig'],
        [
            'debug' => true // This line should enable debug mode
        ]
    );

    // Instantiate and add Slim specific extension
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

// Create database wrapper container
$container['databaseWrapper'] = function() {
    // Get class using autoloader
    $databaseWrapper = new \Database\databaseWrapper();
    // Return class
    return $databaseWrapper;
};

// Create database wrapper container
$container['authentication'] = function($container) {
    // Get class using autoloader
    $authentication = new \Validation\Authentication($container['logger']);
    // Return class
    return $authentication;
};

// Create validator container
$container['validator'] = function() {
    // Get class using autoloader
    $validator = new \Validation\Validator();
    // Return class
    return $validator;
};

// Create user model container
$container['userModel'] = function($container) {
    // Get class using autoloader
    $userModel = new \Model\userModel($container['logger']);
    // Return class
    return $userModel;
};

// Create SQL queries container
$container['sqlQueries'] = function() {
    // Get class using autoloader
    $sql_queries = new \Database\sqlQueries();
    // Return class
    return $sql_queries;
};

// Container for the LibSodium Wrapper class
$container['libSodiumWrapper'] = function () {
    $libsodium = new \Encryption\LibSodiumWrapper();
    return $libsodium;
};

// Container for the Base64 Wrapper class
$container['base64Wrapper'] = function () {
    $base64 = new \Encryption\Base64Wrapper();
    return $base64;
};


// Create container for two different kind of loggers
// One logger will handles notices and the other handles warning
$container['logger'] = function() {
    // Instantiate logger
  $logger = new Logger('logger');

  // Notices logger
    // Set notices log path
    $notices_log = LOG_FILE_PATH . 'notices.log';
    // Create stream handler for notices logger
    $stream_notices = new StreamHandler($notices_log, Logger::NOTICE);
    // Push stream handler into logger object
    $logger->pushHandler($stream_notices);

    // Warning logger
    // Set warning log path
    $warning_log = LOG_FILE_PATH . 'warnings.log';
    // Create stream handler for warnings logger
    $stream_warnings = new StreamHandler($warning_log, Logger::WARNING);
    // Push stream handler into logger object
    $logger->pushHandler($stream_warnings);

    $logger->pushProcessor(function ($record) {
        $record['context']['sid'] = session_id();
        return $record;
    });

    // Return looger
    return $logger;
};
