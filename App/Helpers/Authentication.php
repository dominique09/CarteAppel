<?php
/**
 * Created by PhpStorm.
 * Users: domin
 * Date: 2017-05-15
 * Time: 22:59
 */

namespace App\Helpers;


use App\Config;
use App\Models\User;
use Core\Controller;

class Authentication
{
    private static $authUser;

    public static function authenticate($user){

        $u = User::where('username', $user['username'])->first();

        if($u && Hash::password_check($user['password'], $u->password) && $u->active){
            $_SESSION[Config::AUTH_SESSION] = $u->id;
            return true;
        }

         return false;
    }

    public static function logOut(){
        self::Auth()->removeRememberCredentials();

        unset($_SESSION[Config::AUTH_SESSION]);
    }

    public static function Auth(){
        if(isset($_SESSION[Config::AUTH_SESSION]))
            return User::where('id', $_SESSION[Config::AUTH_SESSION])->first();
        else
            return false;
    }

    public static function checkRememberMe()
    {
        if(isset($_COOKIE[Config::AUTH_REMEMBER]) && !self::Auth())
        {
            $data = $_COOKIE[Config::AUTH_REMEMBER];
            $credentials = explode('___', $data);

            if(empty(trim($data)) || count($credentials) !== 2)
            {
                //Do something redirect
            }
            else
            {
                $ident = $credentials[0];
                $token = Hash::hash($credentials[1]);

                $u = User::where('remember_identifier', $ident)->first();

                if($u && $u->remember_token)
                {
                    if(Hash::hashCheck($token, $u->remember_token)) {
                        $_SESSION[Config::AUTH_SESSION] = $u['id'];
                    }
                    else
                        $u->removeRememberCredentials();
                }
            }
        }
    }
}