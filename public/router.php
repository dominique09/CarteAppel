<?php
$router = new Core\Router();

$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('home', ['controller' => 'Home', 'action' => 'index']);
$router->add('admin/{controller}/{action}', ['namespace' => 'admin']);
$router->add('{controller}/{action}');
$router->add('{controller}/{action}/{id:\d+}');

$router->add('auth/activate/{email:\S+}/{ident:\S+}', ['controller' => 'auth', 'action' => 'activate']);
$router->add('auth/password-recover-change/{email:\S+}/{ident:\S+}', ['controller' => 'auth', 'action' => 'passwordRecoverChange']);

$router->dispatch($_SERVER['QUERY_STRING']);