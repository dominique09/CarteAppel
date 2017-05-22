<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-22
 * Time: 16:34
 */

namespace App\Controllers\Admin;


use App\Helpers\Authentication;
use App\Helpers\Token;
use App\Helpers\Validator;
use App\Models\Division as Div;
use Core\Controller;
use Core\View;

class Division extends Controller
{
    protected function before(){
        parent::before();

        if(!Authentication::Auth()->hasPermission('is_admin'))
            self::redirect('/auth/login');
    }

    public function indexAction(){
        $args['divisions'] = Div::all();
        View::renderTemplate('Admin/Division/index.html', $args);
    }

    public function createAction(){
        if($_POST && Token::check( $_POST['token']))
            $args = $this->createDivision($_POST);

        $args['token'] = Token::generate();
        View::renderTemplate('Admin/Division/create.html', $args);
    }

    private function createDivision($request){
        $v = new Validator($this->errHandler);
        $v->check($request, [
            'nom' => [
                'required' => true,
                'alnum' => true,
                'maxlength' => 50,
            ],
            'numero' => [
                'required' => true,
                'alnum' => true,
                'maxlength' => 5,
                'unique' => 'divisions'
            ],
        ]);

        if($v->passes()){
            $d = new Div();
            $d->nom = $request['nom'];
            $d->numero = strtoupper($request['numero']);

            $d->save();

            self::addFlashMessage('success', 'Succèes', 'La division a bien été créée.');
            self::redirect('/admin/division');
        }

        return [
            'old_data' => $request,
            'errors' => $v->errors()->all()
        ];
    }

    public function editAction(){
        $div = Div::find($this->route_params['id']);
        if(!$div){
            self::addFlashMessage('warning', 'Ooopppsss', 'Une erreur est survenue.');
            self::redirect('admin/division/index');
        }

        $args['old_data'] = $div;

        if($_POST && Token::check($_POST['token']))
            $args = $this->editDivision($_POST, $div);

        if($_POST && Token::check( $_POST['token']))
            $args = $this->createDivision($_POST);

        $args['token'] = Token::generate();
        View::renderTemplate('Admin/Division/edit.html', $args);
    }

    private function editDivision($request, $div){
        $v = new Validator($this->errHandler);
        $check['nom'] = [
            'required' => true,
            'alnum' => true,
            'maxlength' => 50,
        ];

        if($div->numero != $request['numero']) {
            $check['numero'] = [
                'required' => true,
                'alnum' => true,
                'maxlength' => 5,
                'unique' => 'divisions'
            ];
        }

        $v->check($request, $check);

        $div->nom = $request['nom'];
        $div->numero =  strtoupper($request['numero']);
        $div->actif = isset($request['actif']);

        if($v->passes()){

            $div->save();

            self::addFlashMessage('success', 'Succèes', 'La division a bien été modifié.');
            self::redirect('/admin/division');
        }

        return [
            'old_data' => $div,
            'errors' => $v->errors()->all()
        ];
    }
}