<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-06-22
 * Time: 19:14
 */

namespace App\Controllers;

use App\Helpers\Authentication;
use App\Helpers\Token;
use App\Helpers\Validator;
use App\Models\Carte as C;
use Core\Controller;
use Core\View;

class Carte extends Controller
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

    public function openAction(){
        $carte = \App\Models\Carte::find($this->route_params['id']);
        if(!$carte)
            self::redirect('/operation');

        $args['old_data'] = $carte;

        $args['lstSites'] = $this->_event->sites->where('actif', true);
        $args['token'] = Token::generate();
        View::renderTemplate('Carte/open.html', $args);
    }

    public function createAction(){


        if($_POST && Token::check($_POST['token'])){
            $args = $this->createCarte($_POST);
        }

        $args['lstSites'] = $this->_event->sites->where('actif', true);
        $args['token'] = Token::generate();
        View::renderTemplate('Carte/create.html', $args);
    }

    private function createCarte($request){
        $v = new Validator($this->errHandler);
        $v->check($request,[
            'emplacement'=>[
                'required' => true,
                ],
            'description'=>[
                'required' => true,
            ],
            'appelant'=>[
                'required' => true,
            ],
            'priorite' => [
                'required' => true,
            ],
            'site' => ['required' => true]
        ]);

        if($v->passes()){
            $c = new C();
            $c->emplacement = $request['emplacement'];
            $c->description = $request['description'];
            $c->appelant_id = ($request['appelant'] === "")?0:$request['appelant'];
            $c->priorite = $request['priorite'];
            $c->service()->associate($this->_service);
            $c->site()->associate(\App\Models\Site::find($request['site']));
            $ouverture = date("Y-m-d H:i:s");
            $c->heure_appel = $ouverture;
            $c->status = 0;
            $c->save();

            self::addFlashMessage('success', 'Succès', "La carte est maintenant ouverte ({$ouverture})!");
            self::redirect("/operation/{$request['site']}");
        }
        return [
            'old_data' => $request,
            'errors' => $v->errors()->all()
        ];
    }

    private function toTimeStamp($date, $time){
        return \DateTime::createFromFormat("d/m/Y H:i","$date $time");
    }
}
