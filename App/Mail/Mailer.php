<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-15
 * Time: 23:51
 */

namespace App\Mail;


use App\Config;
use Core\View;
use Mailgun\Mailgun;

class Mailer
{
    protected $mailer;

    public function __construct()
    {
        $this->mailer = new Mailgun(Config::MAIL_SECRET);
    }

    public function send($template, $data, $callback)
    {
        $domain = Config::MAIL_DOMAIN;
        $builder = $this->mailer->MessageBuilder();
        $builder->setFromAddress(Config::MAIL_FROM);
        $message = new Message($builder);
        $view = View::renderMail($template, $data);
        $message->body($view);

        call_user_func($callback, $message);
        $this->mailer->post("{$domain}/messages", $builder->getMessage());
    }
}