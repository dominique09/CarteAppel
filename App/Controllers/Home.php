<?php

namespace App\Controllers;

use App\Mail\Mailer;
use Core\Controller;
use Core\View;

class Home extends Controller
{

    public function indexAction(){
        View::renderTemplate('Home/index.html', [
            'colours' => ['red', 'blue', 'greed']
        ]);
    }

    public function testmailAction(){
        $mailer = new Mailer();
        $mailer->send('test.html', ['bobby' => 'bobbybob'], function($message){
            $message->to('dominique.septembre@gmail.com');
            $message->subject('Test DS');
        });
    }
}