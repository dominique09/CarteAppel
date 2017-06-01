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
use App\Models\Evenement as Event;
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

    public function editAction(){
        $u = User::find($this->route_params['id']);
        if(!$u){
            self::addFlashMessage('warning', 'Ooopppsss', 'Une erreur est survenue.');
            self::redirect('admin/users/index');
        }

        $args['old_data'] = $u;

        if($_POST && Token::check($_POST['token']))
            $args = $this->editUser($_POST, $u);

        $args['evenements'] = Event::where('actif', true)->get();
        $args['permission_type'] = UserPermission::getPermissions();
        $args['token'] = Token::generate();
        View::renderTemplate('Admin/Users/edit.html', $args);
    }

    public function detailsAction(){
        $u = User::find($this->route_params['id']);
        if(!$u){
            self::addFlashMessage('warning', 'Ooopppsss', 'Une erreur est survenue.');
            self::redirect('admin/users/index');
        }

        $args['old_data'] = $u;

        $args['permission_type'] = UserPermission::getPermissions();
        View::renderTemplate('Admin/Users/details.html', $args);
    }

    private function editUser($request, User $u){
        $v = new Validator($this->errHandler);
        $check['firstname'] = ['required' => true,'alnum' => true];
        $check['lastname'] = ['required' => true,'alnum' => true];

        if($u->email !== $request['email'])
            $check['email'] = ['required' => true, 'email' => true, 'maxlength' => 255, 'unique' => 'users'];

        if($u->username !== $request['username'])
            $check['username'] = ['required' => true, 'maxlength' => 255, 'unique' => 'users'];

        $val = $v->check($request, $check);

        $u->firstname = $request['firstname'];
        $u->lastname = $request['lastname'];
        $u->username = $request['username'];
        $u->email = $request['email'];

        if(!is_null($request['evenement']))
            $u->evenement()->associate(Event::find($request['evenement']));
        else
            $u->evenement()->dissociate();

        if($val->passes()){

            foreach (UserPermission::getPermissions() as $perm){
                if(isset($request['usr_permissions']) && in_array($perm, $request['usr_permissions']))
                    $u->permissions->{$perm} = true;
                else
                    $u->permissions->{$perm} = false;
            }

            if(isset($request['active']) && $u->active === 0){
                $ident = bin2hex(random_bytes(64));

                $u->active_hash = Hash::hash($ident);

                $mailer = new Mailer();
                $mailer->send('Auth/activate.html', ['user' => $u, 'identifier' => $ident], function($message) use ($u){
                    $message->to($u['email']);
                    $message->subject('Activation de votre compte pour Gestion de Carte d\'appel ASJ');
                });
            } else if(!isset($request['active']) && ($u->active === 1 || $u->active_hash != null)) {
                $u->active = 0;
                $u->active_hash = NULL;
            }

            $u->permissions->save();
            $u->save();

            self::addFlashMessage('success', 'Utilisateur modifié', "L'utilisateur ". $u['username'] ." a bien été modifié.");
            self::redirect('/admin/users/index');
        }

        return [
            'errors' => $val->errors()->all(),
            'old_data' => $u
        ];
    }

    private function createUser($request){
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

            $mailer = new Mailer();
            $mailer->send('Auth/newUser.html', ['user' => $user, 'identifier' => $ident], function($message) use ($user){
                $message->to($user['email']);
                $message->subject('Nouveau compte pour Gestion de Carte d\'appel ASJ');
            });

            $u->save();
            $u->permissions()->create(UserPermission::$defaults)->save();

            self::addFlashMessage('success', 'Utilisateur ajouté', "L'utilisateur ". $user['username'] ." a bien été ajouté.");
            self::redirect('/admin/users/index');
        }

        return [
            'errors' => $val->errors()->all(),
            'old_data' => $request
        ];
    }

}