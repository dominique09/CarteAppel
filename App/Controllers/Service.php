<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-25
 * Time: 20:55
 */

namespace App\Controllers;

use App\Helpers\Authentication;
use App\Helpers\Token;
use App\Helpers\Validator;
use App\Models\Evenement;
use App\Models\Service As S;
use Core\Controller;
use Core\View;

class Service extends Controller
{
    protected $_event;

    public function before()
    {
        parent::before();

        $this->_event = Authentication::Auth()->evenement;

        if(is_null($this->_event)){
            self::addFlashMessage('error', 'Ooooppsss', 'Une erreur est survenue !');
            self::redirect('/home');
        }
    }

    public function indexAction(){
        $args['services'] = Authentication::Auth()->evenement->services;
        View::renderTemplate('service/index.html', $args);
    }

    public function createAction(){
        if(!Authentication::Auth()->hasPermission('gerer_service') && !is_null(Authentication::Auth()->evenement())){
            self::redirect('/home');
        }

        if($_POST && Token::check($_POST['token']))
            $args = $this->createService($_POST);

        $args['token'] = Token::generate();
        View::renderTemplate('Service/create.html', $args);
    }

    private function createService($request){
        $v = new Validator($this->errHandler);
        $v->check($request, [
            'nom' => ['required' => true, 'maxlength' => 255, 'alnum' => true],
            'details' => ['required' => true, 'maxlength' => 255],
            'date_debut' => ['required' => true],
            'heure_debut' => ['timeFormat' => true],
            'date_fin' => ['required' => true, 'olderOrEqualDate' => 'date_debut'],
            'heure_fin' => ['timeFormat' => true],
        ]);

        if($v->passes()){
            if(Authentication::Auth()->evenement->actif){
                $s = new S();
                $s->actif = 0;
                $s->nom = $request['nom'];
                $s->details = $request['details'];
                $s->debut = self::toTimeStamp($request['date_debut'], $request['heure_debut']);
                $s->fin = self::toTimeStamp($request['date_fin'], $request['heure_fin']);
                $s->evenement()->associate(Authentication::Auth()->evenement);
                $s->save();

                self::addFlashMessage('success', 'Succès', 'Le service a bien été ajouté.');
                self::redirect('/service');
            } else {
                self::addFlashMessage('error', 'Ooppss', 'L\'événement dans lequel vous tenter d\'ajouter ce service est présentement inactif !');
            }

        }

        return [
            'old_data' => $request,
            'errors' => $v->errors()->all()
        ];
    }

    private function toTimeStamp($date, $time){
         return \DateTime::createFromFormat("d/m/Y H:i","$date $time");
    }

    public function editAction(){

    }

    public function detailsAction(){

    }
}