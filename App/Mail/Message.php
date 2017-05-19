<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-16
 * Time: 00:03
 */

namespace App\Mail;

class Message
{
    protected $mailer;
    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    public function to($address, $name = ""){
        $this->mailer->addToRecipient($address, $name);
    }

    public function subject($subject){
        $this->mailer->setSubject($subject);
    }

    public function body($body){
        $this->mailer->setHtmlBody($body);
    }
}