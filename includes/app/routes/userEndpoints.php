<?php

 // Get Request and Response
 use \Psr\Http\Message\ServerRequestInterface as Request;
 use \Psr\Http\Message\ResponseInterface as Response;

 $app->get('/', '\Controller\UserController:index')->setName('Index');

 $app->get('/api/users/fetch[/{search}]', '\Controller\UserController:users')->setName('Users');

 $app->post('/api/users/insert', '\Controller\UserController:createUser')->setName('CreateUsers');

 $app->put('/api/users/update/{id}', '\Controller\UserController:updateUser')->setName('UpdateUsers');
 
 $app->delete('/api/users/delete/{id}', '\Controller\UserController:deleteUser')->setName('DeleteUsers');