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
        $args['equipes'] = $this->_service->equipes->where('closed_at', null);

        View::renderTemplate('Equipe/index.html', $args);
    }

    public function dissoudreAction(){
        if(!Authentication::Auth()->hasPermission('gerer_equipe') && !is_null(Authentication::Auth()->evenement())){
            self::redirect('/home');
        }

        $e = E::find($this->route_params['id']);

        if(!$e || !$e->isDissociable()){
            self::addFlashMessage('danger', 'Ooopss', 'Impossible de dissoudre cette équipe.');
            self::redirect('/equipe');
        }

        $e->closed_at = date("Y-m-d H:i:s");
        $e->save();

        self::addFlashMessage('success', 'Succès', "L'équipe a bien été dissoute.");
        self::redirect('/equipe');
    }

    public function createAction(){
        if(!Authentication::Auth()->hasPermission('gerer_equipe') && !is_null(Authentication::Auth()->evenement())){
            self::redirect('/home');
        }

        if($_POST && Token::check($_POST['token']))
            $args = $this->createEquipe($_POST);

        $args['lstSites'] = $this->_event->sites->where('actif', true);
        $args['token'] = Token::generate();
        View::renderTemplate('Equipe/create.html', $args);
    }

    private function createEquipe($request){
        $v = new Validator($this->errHandler);
        $v->check($request, [
            'numero' => ['required' => true,],
            'emplacement' => ['required' => true,],
            'benevoles' => ['required' => true],
        ]);

        $e = new E();
        $e->numero = $request['numero'];
        $e->emplacement = $request['emplacement'];
        $e->actif = true;
        $e->service()->associate($this->_service->id);
        $e->site()->associate(\App\Models\Site::find($request['site']));
        $e->opened_at = date("Y-m-d H:i:s");
        $e->closed_at = null;
        $e->benevoles = $request['benevoles'];
        $e->type_equipe = $request['type'];

        if($this->_service->equipes->where('numero', $e->numero)->count() > 0)
        {
            $this->errHandler->addError('Ce numéro d\'équipe existe déjà sur ce service.', 'numero');
        }

        if($v->passes()){
            $e->save();

            self::addFlashMessage('success', 'Equipe Ajourée !', "L'équipe a bien été ajoutée !");
            self::redirect("/equipe");
        }

        return [
            'old_data' => $e,
            'errors' => $v->errors()->all()
        ];
    }
}