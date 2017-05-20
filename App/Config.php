<?php
/**
 * Created by PhpStorm.
 * Users: domin
 * Date: 2017-05-12
 * Time: 22:32
 */

namespace App;


class Config
{
    ///Const
    const BASE_URL = '65.93.181.211';


    /// Database
    const DB_DRIVER = 'mysql';
    const DB_HOST = '127.0.0.1';
    const DB_PORT = 3306;
    const DB_NAME = 'carteappel-dev';
    const DB_USER = 'root';
    const DB_PASS = '';
    const DB_CHARSET = 'utf8';
    const DB_COLL = 'utf8_unicode_ci';
    const DB_PREFIX = '';

    const DB_MIGRATION_USER = 'root';
    const DB_MIGRATION_PASS = '';

    /// Errors
    const SHOW_ERRORS = true;

    ///Hashing
    const HASH_ALGO = PASSWORD_BCRYPT;
    const HASH_COST = 10;

    ///Auth
    const AUTH_SESSION = 'user_id';
    const AUTH_REMEMBER = 'user_r';
    const AUTH_COOKIE_SECURE = false;

    ///Mail
    const MAIL_FROM = 'no-reply@carteappelasj';
    const MAIL_DOMAIN = 'sandbox90013959593043e1b31d7986dc95dcb7.mailgun.org';
    const MAIL_SECRET = 'key-b265620fa3edb4c83dc27e13c4dd5365';

    ///Gravatar
    const GRAVATAR_BASE_USR = 'http://www.gravatar.com/avatar/';
    const GRAVATAR_DEFAULT = 'identicon';
}