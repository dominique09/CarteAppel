<?php
session_cache_limiter(false);
session_start();

require_once dirname(__DIR__) . '/vendor/autoload.php';

error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

use App\Helpers\Authentication;

use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;


$capsule = new Capsule();
$capsule->addConnection([
    'driver' => \App\Config::DB_DRIVER,
    'host' => \App\Config::DB_HOST,
    'database' => \App\Config::DB_NAME,
    'username' => \App\Config::DB_USER,
    'password' => \App\Config::DB_PASS,
    'charset' => \App\Config::DB_CHARSET,
    'collation' => \App\Config::DB_COLL,
    'prefix' => \App\Config::DB_PREFIX,
]);
// Set the event dispatcher used by Eloquent models... (optional)

$capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

Authentication::checkRememberMe();

require_once 'router.php';