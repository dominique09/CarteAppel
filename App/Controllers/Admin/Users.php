<?php
/**
 * Created by PhpStorm.
 * Users: domin
 * Date: 2017-05-12
 * Time: 20:36
 */

namespace App\Controllers\Admin;

use App\Helpers\Authentication;
use App\Helpers\Hash;
use App\Helpers\Validator;
use App\Mail\Mailer;
use App\Models\UserPermission;
use Core\Controller;
use Core\View;
use App\Models\User;
use App\Helpers\Token;

class Users extends Controller
{
    protected function before(){
        parent::before();

        if(!Authentication::Auth()->hasPermission('is_admin'))
            self::redirect('/auth/login');
    }

    public function indexAction(){
        $users = User::all();

        View::renderTemplate('Admin/Users/index.html', ['users' => $users]);
    }

    public function createAction($args = []){
        if($_POST && Token::check($_POST['token']))
            $args = $this->createUser($_POST);

        $args['token'] = Token::generate();
        View::renderTemplate('Admin/Users/create.html', $args);
    }

    private function createUser($request){
        $request = $_POST;

        $user['firstname'] = $request['firstname'];
        $user['lastname'] = $request['lastname'];
        $user['email'] = $request['email'];
        $user['username'] = $request['username'];
        $user['password'] = $request['password'];
        $user['password_confirm'] = $request['password_confirm'];

        $v = new Validator($this->errHandler);
        $val = $v->check($user, [
            'firstname' => [
                'required' => true,
                'alnum' => true,
            ],
            'lastname' => [
                'required' => true,
                'alnum' => true,
            ],
            'username' => [
                'required' => true,
                'alnum' => true,
                'unique' => 'users',
            ],
            'email' => [
                'required' => true,
                'email' => true,
                'maxlength' => 255,
                'unique' => 'users',
            ],
            'password' => [
                'required' => true,
                'minlength' => 6,
            ],
            'password_confirm' => [
                'matches' => 'password'
            ],
        ]);

        if($val->passes()){
            $ident = bin2hex(random_bytes(64));

            $u = new User();
            $u->firstname = $user['firstname'];
            $u->lastname = $user['lastname'];
            $u->username = $user['username'];
            $u->email = $user['email'];
            $u->password = Hash::password($user['password']);
            $u->active_hash = Hash::hash($ident);
            $u->save();

            $u->permissions()->create(UserPermission::$defaults);

            $mailer = new Mailer();
            $mailer->send('Auth/newUser.html', ['user' => $user, 'identifier' => $ident], function($message) use ($user){
                $message->to($user['email']);
                $message->subject('Nouveau compte pour Gestion de Carte d\'appel ASJ');
            });

            self::addFlashMessage('success', 'Utilisateur ajouté', "L'utilisateur ". $user['username'] ." a bien été ajouté.");
            self::redirect('/admin/users/index');
        }

        return [
            'errors' => $val->errors()->all(),
            'old_data' => $request
        ];
    }

}