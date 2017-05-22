<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-22
 * Time: 17:22
 */

namespace App\Controllers\Admin;


use App\Helpers\Authentication;
use App\Helpers\Token;
use App\Helpers\Validator;
use Core\Controller;
use App\Models\Formation as Form;
use Core\View;

class Formation extends Controller
{
    protected function before(){
        parent::before();

        if(!Authentication::Auth()->hasPermission('is_admin'))
            self::redirect('/auth/login');
    }

    public function indexAction(){
        $args['formations'] = Form::all();
        View::renderTemplate('Admin/Formation/index.html', $args);
    }

    public function createAction(){
        if($_POST && Token::check( $_POST['token']))
            $args = $this->createFormation($_POST);

        $args['token'] = Token::generate();
        View::renderTemplate('Admin/Formation/create.html', $args);
    }

    public function editAction(){
        $form = Form::find($this->route_params['id']);
        if(!$form){
            self::addFlashMessage('warning', 'Ooopppsss', 'Une erreur est survenue.');
            self::redirect('admin/formation/index');
        }

        $args['old_data'] = $form;

        if($_POST && Token::check( $_POST['token']))
            $args = $this->editFormation($_POST, $form);

        $args['token'] = Token::generate();
        View::renderTemplate('Admin/Formation/edit.html', $args);
    }

    private function createFormation($request){
        $v = new Validator($this->errHandler);
        $v->check($request, [
            'nom' => [
                'required' => true,
                'maxlength' => true,
            ],
            'accronyme' => [
                'required' => true,
                'maxlength' => 3,
                'unique' => 'formations'
            ],
        ]);

        if($v->passes()){
            $form = new Form();
            $form->nom = $request['nom'];
            $form->accronyme = $request['accronyme'];

            $form->save();

            self::addFlashMessage('success', 'Succès', 'La formation a bien été créée.');
            self::redirect('/admin/formation');
        }

        return [
            'old_data' => $request,
            'errors' => $v->errors()->all()
        ];
    }

    private function editFormation($request, $form){
        $v = new Validator($this->errHandler);
        $check['nom'] = [
            'required' => true,
            'maxlength' => true,
        ];

        if($request['accronyme'] !== $form->accronyme){
            $check['accronyme'] = [
                'required' => true,
                'maxlength' => 3,
                'unique' => 'formations'
            ];
        }

        $v->check($request, $check);

        $form->nom = $request['nom'];
        $form->accronyme = $request['accronyme'];

        if($v->passes()){
            $form->save();

            self::addFlashMessage('success', 'Succès', 'La formation a bien été modifiée.');
            self::redirect('/admin/formation');
        }

        return [
            'old_data' => $request,
            'errors' => $v->errors()->all()
        ];
    }
}