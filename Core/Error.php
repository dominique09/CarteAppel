<?php
/**
 * Created by PhpStorm.
 * Users: domin
 * Date: 2017-05-12
 * Time: 22:40
 */

namespace Core;


class Error
{
    public static function errorHandler($level, $message, $file, $line){
        if(error_reporting() !== 0){
            throw new \ErrorException($message, 0, $level, $file, $line);
        }
    }

    public static function exceptionHandler($exception){
        $code = $exception->getCode();
        if($code != 404)
            $code = 500;

        http_response_code($code);

        if(\App\Config::SHOW_ERRORS) {
            echo "<h1>Fatal error</h1>";
            echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
            echo "<p>Message '" . $exception->getMessage() . "'</p>";
            echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
            echo "<p>Throw in: '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
        } else {
            $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
            ini_set('error_log', $log);

            $message =  "Uncaught exception: '" . get_class($exception) . "'";
            $message .=  "\n Message '" . $exception->getMessage() . "'";
            $message .=  "\n Stack trace:<pre>" . $exception->getTraceAsString();
            $message .=  "\n Throw in: '" . $exception->getFile() . "' on line " . $exception->getLine();

            error_log($message);

            View::renderTemplate("$code.html");
        }
    }
}