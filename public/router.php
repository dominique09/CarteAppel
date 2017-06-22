<?php
$router = new Core\Router();

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('home', ['controller' => 'Home', 'action' => 'index']);

$router->add('admin/{controller}', ['namespace' => 'Admin', 'action' => 'index']);
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);
$router->add('admin/{controller}/{action}/{id:\d+}', ['namespace' => 'Admin']);

$router->add('{controller}/{action}');
$router->add('{controller}', ['action' => 'index']);
$router->add('{controller}/{action}/{id:\d+}');

$router->add('auth/activate/{email:\S+}/{ident:\S+}', ['controller' => 'auth', 'action' => 'activate']);
$router->add('auth/password-recover-change/{email:\S+}/{ident:\S+}', ['controller' => 'auth', 'action' => 'passwordRecoverChange']);

$router->add('api/equipe/{action}', ['namespace' => 'Api', 'controller' => 'EquipeApi']);
$router->add('api/equipe/{action}/{id:\d+}', ['namespace' => 'Api', 'controller' => 'EquipeApi']);

$router->add('api/assignation/{action}', ['namespace' => 'Api', 'controller' => 'AssignationApi']);
$router->add('api/assignation/{action}/{equipeid:\d+}', ['namespace' => 'Api', 'controller' => 'AssignationApi']);

$router->dispatch($_SERVER['QUERY_STRING']);