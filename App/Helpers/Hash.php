<?php

namespace App\Helpers;
use App\Config;

class Hash
{
    public static function password($password)
    {
        return password_hash($password, Config::HASH_ALGO, ['cost' => Config::HASH_COST]);
    }

    public static function password_check($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public static function hash($input)
    {
        return hash('sha256', $input);
    }

    public static function hashCheck($known, $user){
        return hash_equals($known, $user);
    }
}