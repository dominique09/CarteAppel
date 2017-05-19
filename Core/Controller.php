<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-12
 * Time: 20:19
 */

namespace Core;


use App\Controllers\Auth;
use App\Helpers\Authentication;
use App\Helpers\ErrorHandler;

abstract class Controller
{
    protected $route_params = [];
    protected $flash;

    protected $db;
    protected $errHandler;

    public function __construct($route_params)
    {
        $this->route_params = $route_params;
        $this->db = new Database();
        $this->errHandler = new ErrorHandler();

        $_SESSION['route_params'] = $route_params;
    }

    public function __destruct()
    {
    }

    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if(method_exists($this, $method)){
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    protected function before()
    {
    }

    protected function after()
    {
    }

    protected function addFlashMessage($type, $title, $message){
        $this->flash[$type] = ['title' => $title, 'message' => $message];
        $_SESSION['flash'] = $this->flash;
    }

    public static function redirect($path){
        header("Location: $path");
        exit();
    }
}