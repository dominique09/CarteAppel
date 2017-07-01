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

    public function detailsAction(){
        $carte = C::find($this->route_params['id']);
        if(!$carte)
            self::redirect('/operation');

        if($carte->code_fermeture == NULL)
            self::redirect("/carte/open/$carte->id");

        $args['old_data'] = $carte;

        $args['lstSites'] = $this->_event->sites->where('actif', true);
        $args['token'] = Token::generate();
        View::renderTemplate('Carte/details.html', $args);
    }

    public function allAction(){
        $cartes = $this->_service->cartes;

        $cs = [];
        foreach ($cartes as $c){
            $carte = [];

            $carte['id'] = $c->id;
            $carte['status'] = $c->status;
            $carte['code_fermeture'] = $c->code_fermeture;

            switch ($c->appelant_id) {
                case 1:
                    $carte['appelant'] = 'Sécurité';
                    break;case 2:
                $carte['appelant'] = 'Bénévoles';
                break;case 3:
                $carte['appelant'] = 'ASJ';
                break;case 4:
                $carte['appelant'] = 'Public';
                break;case 5:
                $carte['appelant'] = '911';
                break;case 6:
                $carte['appelant'] = 'Autre';
                break;
            }
            $carte['emplacement'] = $c->emplacement;
            $carte['description'] = $c->description;
            $carte['priorite'] = $c->priorite - 1;
            $carte['heure_appel'] = $c->heure_appel;
            $carte['heure_fermeture'] = $c->heure_fermeture;

            switch ($c->code_fermeture){
                case 1:
                    $carte['raison_fermeture'] = "<span class='label label-success'>Fermeture normale</span>";
                break;
                case 2:
                    $carte['raison_fermeture'] = "<span class='label label-danger'>Annulation</span>";
                break;
                case 3:
                    $carte['raison_fermeture'] = "<span class='label label-warning'>Non Fondé</span>";
                break;
                case 4:
                    $carte['raison_fermeture'] = "<span class='label label-primary'>Non Localisé</span>";
                break;

            }

            $cs[] = $carte;
        }

        $args['cartes'] = $cs;

        return View::renderTemplate('Carte/all.html', $args);
    }

    public function fermetureAction()
    {
        $carte = C::find($this->route_params['id']);
        if (!$carte)
            self::redirect('/operation');

        $assignations = ($carte->equipes()
                ->wherePivot('terminee', '=', null)
                ->WherePivot('annulee', '=', null)->count() > 0);
        if ($assignations)
        {
            self::addFlashMessage('warning', 'Oooppss', 'Des équipes sont toujours assignés sur la carte, veuiilez les libérer avant de fermer la carte');
            self::redirect("/carte/open/$carte->id");
        }

        $carte->code_fermeture = 1;
        $carte->heure_fermeture = date("Y-m-d H:i:s");

        $carte->save();

        self::addFlashMessage('success', '', 'Carte fermée');
        self::redirect("/operation");
    }

    public function reouvertureAction()
    {
        $carte = C::find($this->route_params['id']);
        if (!$carte)
            self::redirect('/carte/all');

        $raison = "";
        switch ($carte->code_fermeture){
            case 1:
                $raison = "Fermeture Normale";
                break;
            case 2:
                $raison = "Annulation";
                break;
            case 4:
                $raison = "Non Localisé";
                break;
            case 3:
                $raison = "Non Fondé";
                break;
        }

        $carte->description .= "\r\n\r\n-+-+-+-+-\r\n Info Réouverture : $raison à $carte->heure_fermeture\r\n-+-+-+-+-";

        $carte->code_fermeture = NULL;
        $carte->heure_fermeture = NULL;

        $carte->save();

        self::addFlashMessage('success', '', 'Carte fermée');
        self::redirect("/carte/open/$carte->id");
    }

    public function annulationAction(){
        $carte = C::find($this->route_params['id']);
        if (!$carte)
            self::redirect('/operation');

        $assignations = ($carte->equipes()
                ->wherePivot('terminee', '=', null)
                ->WherePivot('annulee', '=', null)->count() > 0);
        if ($assignations)
        {
            self::addFlashMessage('warning', 'Oooppss', 'Des équipes sont toujours assignés sur la carte, veuiilez les libérer avant de fermer la carte');
            self::redirect("/carte/open/$carte->id");
        }

        $carte->code_fermeture = 2;
        $carte->heure_fermeture = date("Y-m-d H:i:s");

        $carte->save();

        self::addFlashMessage('success', '', 'Carte Annulée');
        self::redirect("/operation");
    }

    public function nolocAction(){
        $carte = C::find($this->route_params['id']);
        if (!$carte)
            self::redirect('/operation');

        $assignations = ($carte->equipes()
                ->wherePivot('terminee', '=', null)
                ->WherePivot('annulee', '=', null)->count() > 0);
        if ($assignations)
        {
            self::addFlashMessage('warning', 'Oooppss', 'Des équipes sont toujours assignés sur la carte, veuiilez les libérer avant de fermer la carte');
            self::redirect("/carte/open/$carte->id");
        }

        $carte->code_fermeture = 4;
        $carte->heure_fermeture = date("Y-m-d H:i:s");

        $carte->save();

        self::addFlashMessage('success', '', 'Carte Fermée, Non-Localisé');
        self::redirect("/operation");
    }

    public function nofonderAction(){
        $carte = C::find($this->route_params['id']);
        if (!$carte)
            self::redirect('/operation');

        $assignations = ($carte->equipes()
                ->wherePivot('terminee', '=', null)
                ->WherePivot('annulee', '=', null)->count() > 0);
        if ($assignations)
        {
            self::addFlashMessage('warning', 'Oooppss', 'Des équipes sont toujours assignés sur la carte, veuiilez les libérer avant de fermer la carte');
            self::redirect("/carte/open/$carte->id");
        }

        $carte->code_fermeture = 3;
        $carte->heure_fermeture = date("Y-m-d H:i:s");

        $carte->save();

        self::addFlashMessage('success', '', 'Carte Fermée, Non-Localisé');
        self::redirect("/operation");
    }

    public function openAction(){
        $carte = C::find($this->route_params['id']);
        if(!$carte)
            self::redirect('/operation');

        if($carte->code_fermeture >= 1)
            self::redirect("/carte/details/$carte->id");

        $args['old_data'] = $carte;

        if($_POST && Token::check($_POST['token'])){
            $args = $this->editCarte($carte, $_POST);
        }

        $args['lstSites'] = $this->_event->sites->where('actif', true);
        $args['token'] = Token::generate();
        View::renderTemplate('Carte/open.html', $args);
    }

    public function editCarte(C $c, $request){
        $v = new Validator($this->errHandler);
        $v->check($request,[
            'emplacement'=>[
                'required' => true,
            ],
            'description'=>[
                'required' => true,
            ],
            'appelant_id'=>[
                'required' => true,
            ],
            'priorite' => [
                'required' => true,
            ],
            'site' => ['required' => true]
        ]);

        $c->emplacement = $request['emplacement'];
        $c->description = $request['description'];
        $c->appelant_id = $request['appelant_id'];
        $c->priorite = $request['priorite'];
        $c->site()->associate(\App\Models\Site::find($request['site']));

        if($v->passes()){
            $c->save();

            self::addFlashMessage('success', 'Succès', "La carte bien été sauvegardée)!");
            self::redirect("/carte/open/{$c->id}");
        }

        return [
            'old_data' => $c,
            'errors' => $v->errors()->all()
        ];
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
