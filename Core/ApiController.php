<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-06-09
 * Time: 18:02
 */

namespace Core;


class ApiController extends Controller{

    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if(method_exists($this, $method)){
            if ($this->before() !== false) {
                echo call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

}