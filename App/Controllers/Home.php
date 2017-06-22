<?php

namespace App\Controllers;

use App\Mail\Mailer;
use App\Models\UserPermission;
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

    public function getUserPermissionsAction(){
        $up = new UserPermission();
        echo "<pre>";
        var_dump(UserPermission::getPermissions());
    }

    public function testAction(){
        var_dump( \App\Models\Benevole::disponibles()->get());
    }

}