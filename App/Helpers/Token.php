<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-15
 * Time: 21:48
 */

namespace App\Helpers;


class Token
{
    public static function generate(){
        return $_SESSION['token'] = bin2hex(random_bytes(32));
    }

    public static function check($token){
        if(isset($_SESSION['token']) && $token === $_SESSION['token']){
            unset($_SESSION['token']);
            return true;
        }

        return false;
    }
}