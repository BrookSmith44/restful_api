<?php

// autoload_psr4.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'Validation\\' => array($baseDir . '/app/src/Validation'),
    'Slim\\Views\\' => array($vendorDir . '/slim/twig-view/src'),
    'Slim\\' => array($vendorDir . '/slim/slim/Slim'),
    'Psr\\Log\\' => array($vendorDir . '/psr/log/Psr/Log'),
    'Psr\\Http\\Message\\' => array($vendorDir . '/psr/http-message/src'),
    'Psr\\Container\\' => array($vendorDir . '/psr/container/src'),
    'Monolog\\' => array($vendorDir . '/monolog/monolog/src/Monolog'),
    'Model\\' => array($baseDir . '/app/src/Models'),
    'Firebase\\JWT\\' => array($vendorDir . '/firebase/php-jwt/src'),
    'FastRoute\\' => array($vendorDir . '/nikic/fast-route/src'),
    'Encryption\\' => array($baseDir . '/app/src/Encryption'),
    'Database\\' => array($baseDir . '/app/src/Database'),
);