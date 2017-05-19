<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-15
 * Time: 22:21
 */

namespace App\Controllers;


use App\Config;
use App\Helpers\Authentication;
use App\Helpers\Hash;
use App\Helpers\Token;
use App\Helpers\Validator;
use App\Mail\Mailer;
use App\Models\User;
use Carbon\Carbon;
use Core\Controller;
use Core\View;

class Auth extends Controller
{
    public function loginAction()
    {
        if(Authentication::Auth())
            self::redirect('/');

        if($_POST && Token::check($_POST['token']))
            $args = $this->authenticate($_POST);

        $args['token'] = Token::generate();
        View::renderTemplate("/Auth/login.html", $args);
    }

    public function logoutAction(){
        Authentication::logOut();

        self::addFlashMessage('success', 'Déconnexion réussi !', 'Vous êtes bien déconnecté.');
        self::redirect('/auth/login');
    }

    public function passwordRecoverChangeAction(){
        if($_POST && Token::check($_POST['token'])) {
            $this->route_params['ident'] = $_POST['ident'];
            $this->route_params['email'] = $_POST['email'];

            $args = $this->passwordRecoverChange($_POST);
        }

        $ident = $this->route_params['ident'];
        $email = $this->route_params['email'];

        $hashIdent = Hash::hash($ident);

        $u = User::where('email', $email)->first();

        if(Hash::hashCheck($u->recover_hash, $hashIdent)){
            $args['user'] = $u;
            $args['email'] = $email;
            $args['ident'] = $ident;
            $args['token'] = Token::generate();
            View::renderTemplate('/Auth/password-recover-change.html', $args);
            return;
        }

        self::addFlashMessage('error', 'Une erreur est survenue', 'Une erreur est survenue.');
        self::redirect('/');
    }

    public function passwordRecover(){
        if($_POST && Token::check($_POST['token'])){
            $args = $this->recoverSend($_POST);
        }

        $args['token'] = Token::generate();
        View::renderTemplate('Auth/password-recover.html', $args);
    }

    private function recoverSend($request){
        $v = new Validator($this->errHandler);
        $v->check($request, ['email' => ['required' => true, 'email' => true]]);

        if($v->passes()){
            $email = $request['email'];
            $u = User::where('email', $email)->first();

            if(!$u){
                self::addFlashMessage('error', 'Oops', 'Cet utilisateur n\'existe pas');
                self::redirect('/auth/password-recover');
            } else {
                $ident = bin2hex(random_bytes(64));

                $u->update([
                    'recover_hash' => Hash::hash($ident)
                ]);
                $mailer = new Mailer();
                $mailer->send('/Auth/passwordRecover.html', ['user' => $u, 'identifier' => $ident], function ($message) use ($email){
                    $message->to($email);
                    $message->subject('Retrouver votre mot de passe.');
                });

                self::addFlashMessage('success', 'Réactivation', 'Un courriel vous a été envoyé pour réactiver votre mot de passe.');
                self::redirect('/auth/login');
            }
        }

        return [
            'errors' => $v->errors()->all(),
            'old_data' => $request
        ];
    }

    public function activateAction(){
        if($_POST && Token::check($_POST['token'])) {
            $this->route_params['ident'] = $_POST['ident'];
            $this->route_params['email'] = $_POST['email'];

            $args = $this->activateAccount($_POST);
        }

        $ident = $this->route_params['ident'];
        $email = $this->route_params['email'];

        $hashIdent = Hash::hash($ident);

        $u = User::where('email', $email)->where('active', false)->first();

        if(Hash::hashCheck($u->active_hash, $hashIdent)){
            $args['user'] = $u;
            $args['email'] = $email;
            $args['ident'] = $ident;
            $args['token'] = Token::generate();
            View::renderTemplate('/Auth/activate.html', $args);
            return;
        }

        self::addFlashMessage('error', 'Une erreur est survenue', 'Une erreur est survenue.');
        self::redirect('/');
    }

    private function passwordRecoverChange($request){
        $user_id = $request['user_id'];
        $user['password'] = $request['password'];
        $user['password_confirm'] = $request['password_confirm'];

        $v = new Validator($this->errHandler);
        $v->check($user, [
            'password' => ['required' => true,'minlength' => 6],
            'password_confirm' => ['matches' => 'password'],
        ]);

        if($v->passes()){
            $u = User::where('id', $user_id)->first();
            $u->update([
                'password' => Hash::password($user['password']),
                'recover_hash' => null,
            ]);

            self::addFlashMessage('success', 'Changement de mot de passe', "Votre changement de mot de passe a réussi.");
            self::redirect('/auth/login');
        }

        return [
            'errors' => $v->errors()->all(),
            'old_data' => $request
        ];
    }

    private function activateAccount($request){
        $user_id = $request['user_id'];
        $user['password'] = $request['password'];
        $user['password_confirm'] = $request['password_confirm'];

        $v = new Validator($this->errHandler);
        $v->check($user, [
            'password' => ['required' => true,'minlength' => 6],
            'password_confirm' => ['matches' => 'password'],
        ]);

        if($v->passes()){
            $u = User::where('id', $user_id)->where('active', false)->first();
            $u->activateAccount(Hash::password($user['password']));

            self::addFlashMessage('success', 'Activation réussi', "Votre compte a bien été activé.");
            self::redirect('/auth/login');
        }

        return [
            'errors' => $v->errors()->all(),
            'old_data' => $request
        ];
    }

    private function authenticate($request){

        $user['username'] = $request['username'];
        $user['password'] = $request['password'];
        if(isset($request['remember']))
            $remember = $request['remember'];
        else
            $remember = '';

        $v = new Validator($this->errHandler);
        $v->check($user, [
            'username' => [
                'required' => true
            ],
            'password' => [
                'required' => true
            ]
        ]);

        if($v->passes()){
            if(Authentication::authenticate($user))
            {
                if($remember === 'on'){
                    $user = Authentication::Auth();
                    $rIdent = bin2hex(random_bytes(64));
                    $rToken = bin2hex(random_bytes(64));
                    $user->updateRememberCredentials($rIdent, Hash::hash($rToken));

                    setcookie(
                        Config::AUTH_REMEMBER,
                        "{$rIdent}___{$rToken}",
                        Carbon::parse('+1 week')->timestamp,
                        '/',
                        Config::BASE_URL,
                        Config::AUTH_COOKIE_SECURE
                    );
                }

                self::addFlashMessage('success', 'Connexion réussi', "Vous êtes bien connecté.");
                self::redirect('/admin/users/index');
            } else {
                self::addFlashMessage('error', 'Erreur de connexion', 'Impossible de vous authentifier');
                self::redirect('/auth/login');
            }
        }

        return [
            'errors' => $v->errors()->all(),
            'old_data' => $request
        ];
    }
}