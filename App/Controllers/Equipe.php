<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-06-02
 * Time: 19:03
 */

namespace App\Controllers;


use App\Helpers\Authentication;
use App\Helpers\Token;
use App\Helpers\Validator;
use App\Models\Assignation;
use Core\Controller;
use App\Models\Equipe As E;
use App\Models\Benevole As Ben;
use Core\View;

class Equipe extends Controller
{
    protected $_event;
    protected $_service;

    public function before()
    {
        parent::before();

        $this->_event = Authentication::Auth()->evenement;
        $this->_service = $this->_event->serviceActif()->first();

        if(is_null($this->_event) || is_null($this->_service)){
            self::addFlashMessage('error', 'Ooooppsss', 'Une erreur est survenue !');
            self::redirect('/home');
        }
    }

    public function indexAction(){
        $args['equipes'] = $this->_service->equipes->all();

        View::renderTemplate('Equipe/index.html', $args);
    }

    public function editAction(){
        if(!Authentication::Auth()->hasPermission('gerer_equipe') && !is_null(Authentication::Auth()->evenement())){
            self::redirect('/home');
        }

        $e = E::find($this->route_params['id']);
        if(!$e){
            self::addFlashMessage('error', 'Oooppss', 'Une erreur est survenue !');
            self::redirect('/equipe');
        }

        if(!$e->isEditable()){
            self::addFlashMessage('error', 'Oooppss', 'Impossible de modifier l\'équipe si elle a déjà eu des appels. !');
            self::redirect('/equipe');
        }

        $args['old_data'] = $e;

        if($_POST && Token::check($_POST['token']))
            $args = $this->editService($_POST, $e);

        $args['lstBenevoles'] = Ben::where('actif', true)->get();

        $args['token'] = Token::generate();
        View::renderTemplate('Equipe/edit.html', $args);
    }

    private function editService($request, E $e){
        $v = new Validator($this->errHandler);
        $v->check($request, [
            'numero' => ['required' => true,],
            'emplacement' => ['required' => true,]
        ]);

        if($e->numero !== $request['numero'] && $this->_service->equipes->where('numero', $e->numero)->count() > 0)
            $this->errHandler->addError('Ce numéro d\'équipe existe déjà sur ce service.', 'numero');

        $e->numero = $request['numero'];
        $e->emplacement = $request['emplacement'];

        if($v->passes()){

            $e->save();

            self::addFlashMessage('success', 'Equipe modifiée !', 'L\'équipe a bien été modifiée.');
            self::redirect("/equipe/edit/{$e->id}");
        }

        return [
            'old_data' => $e,
            'errors' => $v->errors()->all()
        ];
    }

    public function benevoles($assignations){
        $benevoles = null;
        foreach ($assignations as $benevole_id){
            $b = Ben::find($benevole_id);
            if(!$b || !$b->actif)
                $this->errHandler("Le bénévole est invalide.", 'assignations');

            if($b->isAssigned())
                $this->errHandler("Le bénévole est déjà assigné.");

            $benevoles[] = $b;
        }

        return $benevoles;
    }

    public function createAction(){
        if(!Authentication::Auth()->hasPermission('gerer_equipe') && !is_null(Authentication::Auth()->evenement())){
            self::redirect('/home');
        }

        if($_POST && Token::check($_POST['token']))
            $args = $this->createEquipe($_POST);

        $args['lstBenevoles'] = \App\Models\Benevole::where('actif', true)->get();
        $args['lstSites'] = $this->_event->sites->where('actif', true);
        $args['token'] = Token::generate();
        View::renderTemplate('Equipe/create.html', $args);
    }

    private function createEquipe($request){
        $v = new Validator($this->errHandler);
        $v->check($request, [
            'numero' => ['required' => true,],
            'emplacement' => ['required' => true,]
        ]);

        $e = new E();
        $e->numero = $request['numero'];
        $e->emplacement = $request['emplacement'];
        $e->actif = true;
        $e->service()->associate($this->_service->id);
        $e->site()->associate(\App\Models\Site::find($request['site']));
        $e->type_equipe = $request['type'];

        if($this->_service->equipes->where('numero', $e->numero)->count() > 0)
        {
            $this->errHandler->addError('Ce numéro d\'équipe existe déjà sur ce service.', 'numero');
        }

        if($v->passes()){
            $e->save();
            $e->benevoles()->attach($request['benevoles']);

            self::addFlashMessage('success', 'Equipe Ajourée !', "L'équipe a bien été ajoutée !");
            self::redirect("/equipe");
        }

        return [
            'old_data' => $e,
            'errors' => $v->errors()->all()
        ];
    }
}