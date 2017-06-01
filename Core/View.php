<?php
/**
 * Created by PhpStorm.
 * Users: domin
 * Date: 2017-05-12
 * Time: 20:43
 */

namespace Core;


use App\Config;
use App\Helpers\Authentication;
use App\Helpers\Token;

class View
{
    public static function render($view, $args = []){
        extract($args, EXTR_SKIP);

        $file = "../App/Views/$view";

        if(is_readable($file)){
            require $file;
        } else {
            throw new \Exception( "$file not found");
        }
    }

    public static function renderMail($template, $args = []){
        static $twig = null;

        if($twig === null){
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views/Mail');
            $twig = new \Twig_Environment($loader);
        }

        $args['baseUrl'] = Config::BASE_URL;

        return $twig->render($template, $args);
    }

    public static function renderTemplate($template, $args = []){
        static $twig = null;

        if($twig === null){
            $loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
            $twig = new \Twig_Environment($loader);
        }

        $args['auth'] = Authentication::Auth();

        if(isset($_SESSION['flash'])) {
            $args['flash'] = $_SESSION['flash'];
            $_SESSION['flash'] = null;
        } else {
            $args['flash'] = null;
        }

        if(isset($_SESSION['route_params']))
            $args['route_params'] = $_SESSION['route_params'];

        $args['baseUrl'] = Config::BASE_URL;

        if(file_exists("../public/assets/images/logo/". Authentication::Auth()->evenement->id .".png")){
            $args['pathLogo'] = "/assets/images/logo/". Authentication::Auth()->evenement->id .".png";
        } else {
            $args['pathLogo'] = "/assets/images/logo/logo.png";
        }

        echo $twig->render($template, $args);

    }


}