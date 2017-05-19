<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-12
 * Time: 22:21
 */

namespace App\Controllers;


use App\Helpers\Authentication;
use App\Helpers\Token;
use App\Helpers\Validator;
use App\Models\Division;
use App\Models\Formation;
use Core\Controller;
use Core\View;
use App\Models\Benevole as Ben;


class Benevole extends Controller
{
    public function before()
    {
        parent::before();

        if(!Authentication::Auth())
            self::redirect('/auth/login');

        if(!(Authentication::Auth()->hasPermission('consulter_benevole')) && !(Authentication::Auth()->hasPermission('gerer_benevole')))
            self::redirect('/');
    }

    public function indexAction(){
        $benevoles = Ben::all();

        View::renderTemplate('Benevole/index.html', ['benevoles' => $benevoles]);
    }

    public function detailsAction(){
        $ben = Ben::find($this->route_params['id']);
        if(!$ben){
            self::addFlashMessage('warning', 'Ooopppsss', 'Une erreur est survenue.');
            self::redirect('/benevole/index');
        }

        if (!($ben->actif OR (!$ben->actif AND Authentication::Auth()->hasPermission('reactiver_benevole'))))
            self::redirect('/benevole/index');

        $args['old_data'] = $ben;
        View::renderTemplate('Benevole/details.html', $args);
    }

    public function editAction(){
        if(!Authentication::Auth()->hasPermission('gerer_benevole'))
            self::redirect('/benevole/index/');

        $ben = Ben::find($this->route_params['id']);
        if(!$ben){
            self::addFlashMessage('warning', 'Ooopppsss', 'Une erreur est survenue.');
            self::redirect('/benevole/index');
        }

        if (!($ben->actif OR (!$ben->actif AND Authentication::Auth()->hasPermission('reactiver_benevole'))))
            self::redirect('/benevole/index');

        $args['old_data'] = $ben;

        if($_POST && Token::check($_POST['token']))
            $args = $this->editBenevole($_POST, $ben);

        $args['formations'] = Formation::all();
        $args['divisions'] = Division::where('actif', true)->get();
        $args['token'] = Token::generate();
        View::renderTemplate('Benevole/edit.html', $args);
    }

    public function createAction(){
        if(!Authentication::Auth()->hasPermission('gerer_benevole'))
            self::redirect('/benevole/index/');

        if($_POST && Token::check( $_POST['token']))
            $args = $this->createBenevole($_POST);

        $args['formations'] = Formation::all();
        $args['divisions'] = Division::where('actif', true)->get();
        $args['token'] = Token::generate();
        View::renderTemplate('Benevole/create.html', $args);
    }

    private function editBenevole($request, $ben){
        $v = new Validator($this->errHandler);
        $v->check($request, [
            'prenom' => ['required' => true, 'alnum' => true],
            'nom' => ['required' => true, 'alnum' => true],
            'formation' => ['required' => true, 'ddSelected' => true],
            'division' => ['required' => true, 'ddSelected' => true],
            'phoneOne' => [],
            'phoneTwo' => [],
            'email' => ['email' => true],
        ]);

        $ben->prenom = $request['prenom'];
        $ben->nom = $request['nom'];
        $ben->telephone_1 = $request['phoneOne'];
        $ben->telephone_2 = $request['phoneTwo'];
        $ben->email = $request['email'];
        $ben->formation()->associate(Formation::find($request['formation']));
        $ben->division()->associate(Division::find($request['division']));

        if(Authentication::Auth()->hasPermission('reactiver_benevole'))
            $ben->actif = isset($request['actif']);

        if($v->passes()){

            $ben->save();

            self::addFlashMessage('success', 'Succèes', 'Le bénévole a bien été modifié.');
            self::redirect("/benevole/details/{$ben->id}");
        }

        return [
            'old_data' => $ben,
            'errors' => $v->errors()->all()
        ];
    }

    private function createBenevole($request){
        $v = new Validator($this->errHandler);
        $v->check($request, [
            'prenom' => ['required' => true, 'alnum' => true],
            'nom' => ['required' => true, 'alnum' => true],
            'formation' => ['required' => true, 'ddSelected' => true],
            'division' => ['required' => true, 'ddSelected' => true],
            'phoneOne' => [],
            'phoneTwo' => [],
            'email' => ['email' => true],
        ]);

        if($v->passes()){
            $ben = new Ben();
            $ben->prenom = $request['prenom'];
            $ben->nom = $request['nom'];
            $ben->telephone_1 = $request['phoneOne'];
            $ben->telephone_2 = $request['phoneTwo'];
            $ben->email = $request['email'];
            $ben->formation()->associate(Formation::find($request['formation']));
            $ben->division()->associate(Division::find($request['division']));
            $ben->actif = true;

            $ben->save();

            self::addFlashMessage('success', 'Succèes', 'Le bénévole a bien été créé.');
            self::redirect('/benevole/index');
        }

        return [
            'old_data' => $request,
            'errors' => $v->errors()->all()
        ];
    }
}