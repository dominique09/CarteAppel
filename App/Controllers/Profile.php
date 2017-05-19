<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-17
 * Time: 21:08
 */

namespace App\Controllers;


use App\Helpers\Authentication;
use App\Helpers\Hash;
use App\Helpers\Token;
use App\Helpers\Validator;
use App\Mail\Mailer;
use Core\Controller;
use Core\View;

class Profile extends Controller
{
    public function changePasswordAction(){
        if($_POST && Token::check($_POST['token'])){
            $args = $this->changePassword($_POST);
        }

        $args['token'] = Token::generate();
        View::renderTemplate('Profile/changePassword.html', $args);
    }

    public function indexAction(){

    }

    public function editAction(){
        $args['old_data'] = Authentication::Auth();

        if($_POST && Token::check($_POST['token'])){
            $args = $this->editAccount($_POST);
        }

        $args['token'] = Token::generate();
        View::renderTemplate('Profile/edit.html', $args);
    }

    private function editAccount($request){
        $v = new Validator($this->errHandler);
        $v->check($request, [
            'email' => [
                'required' => true,
                'email' => true,
                'maxlength' => 255,
                'unique' => 'users',
            ]
        ]);

        if($v->passes()){
            $u = Authentication::Auth();
            $u->update(['email' => $request['email']]);

            self::addFlashMessage('success', 'Courriel corrigé.', 'Votre adresse courriel a bien été corrigé.');
            self::redirect('/profile/index');
        }

        return [
            'errors' => $v->errors()->all(),
            'old_data' => $request
        ];
    }

    private function changePassword($request){
        $v = new Validator($this->errHandler);
        $v->check($request, [
            'old_password' => [
                'required' => true,
                'matchesCurrentPassword' => true,
                'notMatches' => 'password',
            ],
            'password' => [
                'required' => true,
                'minlength' => 6,
                'notMatches' => 'old_password'
            ],
            'password_confirm' => [
                'matches' => 'password'
            ]
        ]);

        if($v->passes()){
            $u = Authentication::Auth();
            $u->update([
                'password' => Hash::password($_POST['password'])
            ]);

            $mailer = new Mailer();
            $mailer->send('/Auth/passwordChange.html', ['auth' => $u], function($message) use ($u){
                $message->to($u->email);
                $message->subject('Modification de votre mot de passe.');
            });

            $this->addFlashMessage('success', 'Modification du mot de passe.', 'Votre mot de passe a bien été changé.');
            self::redirect('/');
        }

        return [
            'errors' => $v->errors()->all(),
            'old_data' => $request
        ];
    }
}