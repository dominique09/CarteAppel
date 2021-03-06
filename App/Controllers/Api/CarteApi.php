<?php

namespace App\Controllers\Api;

use App\Helpers\Authentication;
use App\Models\Carte;
use App\Models\Equipe;
use App\Models\Service;
use App\Models\Site;

class CarteApi extends \Core\ApiController
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

    public function emplacementsAction(){

        $cartes = $this->_event->cartes()->select('emplacement')->distinct()->orderBy('emplacement')->get();

        $empl = [];
        foreach ($cartes as $c){
            $empl[] = ['id'=> $c->emplacement, 'name' => $c->emplacement];
        }

        return json_encode($empl, JSON_UNESCAPED_UNICODE);
    }

    public function assignerAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = Equipe::find($this->route_params['equipe']);


            if(!($carte->equipes->contains($equipe))) {
                $carte->equipes()->attach($equipe);
                $carte->save();

                if($carte->status < 1)
                    $carte->status = 0;
                if($equipe->type_equipe == 1){
                    $equipe->emplacement = "-";
                }
                $equipe->statut = 1;
                $carte->save();
                $equipe->save();

                return "assignee";
            }

            return "deja-assignee";
        }

        return "non-assignee";
    }

    public function repartirAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = $carte->equipes()
                ->where('equipe_id', $this->route_params['equipe'])
                ->wherePivot('terminee','=',null)
                ->WherePivot('annulee','=',null)->first();

            $equipe->pivot->reparti = date("Y-m-d H:i:s");
            $equipe->pivot->save();

            if($carte->status < 1)
                $carte->status = 1;

            $equipe->statut = 1;

            $carte->save();
            $equipe->save();

            return "reparti";

        }

        return "non-reparti";
    }

    public function repartirBackAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = $carte->equipes()
                ->where('equipe_id', $this->route_params['equipe'])
                ->wherePivot('terminee','=',null)
                ->WherePivot('annulee','=',null)->first();

            $equipe->pivot->reparti = null;
            $equipe->pivot->save();

                $carte->status = 0;

            $equipe->statut = 0;

            $carte->save();
            $equipe->save();

            return "reparti back";

        }

        return "non-reparti back";
    }

    public function directionAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = $carte->equipes()
                ->where('equipe_id', $this->route_params['equipe'])
                ->wherePivot('terminee','=',null)
                ->WherePivot('annulee','=',null)->first();

            $equipe->pivot->en_direction = date("Y-m-d H:i:s");
            $equipe->pivot->save();

            if($carte->status < 2)
                $carte->status = 2;

            $equipe->statut = 2;

            $carte->save();
            $equipe->save();

            return "direction";

        }

        return "non-direction";
    }

    public function directionBackAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = $carte->equipes()
                ->where('equipe_id', $this->route_params['equipe'])
                ->wherePivot('terminee','=',null)
                ->WherePivot('annulee','=',null)->first();

            $equipe->pivot->en_direction = null;
            $equipe->pivot->save();

                $carte->status = 1;

            $equipe->statut = 1;

            $carte->save();
            $equipe->save();

            return "direction Back";

        }

        return "non-direction Back";
    }

    public function patientAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = $carte->equipes()
                ->where('equipe_id', $this->route_params['equipe'])
                ->wherePivot('terminee','=',null)
                ->WherePivot('annulee','=',null)->first();

            $equipe->pivot->sur_les_lieux = date("Y-m-d H:i:s");
            $equipe->pivot->save();

            if($carte->status < 3)
                $carte->status = 3;

            $equipe->statut = 3;

            $carte->save();
            $equipe->save();

            return "patient";

        }

        return "non-patient";
    }

    public function patientBackAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = $carte->equipes()
                ->where('equipe_id', $this->route_params['equipe'])
                ->wherePivot('terminee','=',null)
                ->WherePivot('annulee','=',null)->first();

            $equipe->pivot->sur_les_lieux = null;
            $equipe->pivot->save();

                $carte->status = 2;

            $equipe->statut = 2;

            $carte->save();
            $equipe->save();

            return "patient BaCK";

        }

        return "non-patient Back";
    }

    public function transportAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = $carte->equipes()
                ->where('equipe_id', $this->route_params['equipe'])
                ->wherePivot('terminee','=',null)
                ->WherePivot('annulee','=',null)->first();

            $equipe->pivot->en_transport = date("Y-m-d H:i:s");
            $equipe->pivot->save();

            if($carte->status < 4)
                $carte->status = 4;

            $equipe->statut = 4;

            $carte->save();
            $equipe->save();

            return "transport";

        }

        return "non-transport";
    }

    public function transportBackAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = $carte->equipes()
                ->where('equipe_id', $this->route_params['equipe'])
                ->wherePivot('terminee','=',null)
                ->WherePivot('annulee','=',null)->first();

            $equipe->pivot->en_transport = null;
            $equipe->pivot->save();

                $carte->status = 3;

            $equipe->statut = 3;

            $carte->save();
            $equipe->save();

            return "transport Back";

        }

        return "non-transport Back";
    }

    public function tenteAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = $carte->equipes()
                ->where('equipe_id', $this->route_params['equipe'])
                ->wherePivot('terminee','=',null)
                ->WherePivot('annulee','=',null)->first();

            $equipe->pivot->arrivee_tante = date("Y-m-d H:i:s");
            $equipe->pivot->save();

            if($carte->status < 5)
                $carte->status = 5;

            $equipe->statut = 5;

            $carte->save();
            $equipe->save();

            return "tente";

        }

        return "non-tente";
    }

    public function tenteBackAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = $carte->equipes()
                ->where('equipe_id', $this->route_params['equipe'])
                ->wherePivot('terminee','=',null)
                ->WherePivot('annulee','=',null)->first();

            $equipe->pivot->arrivee_tante = null;
            $equipe->pivot->save();

                $carte->status = 4;

            $equipe->statut = 4;

            $carte->save();
            $equipe->save();

            return "tente back";

        }

        return "non-tente back";
    }

    public function termineeAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = $carte->equipes()
                ->where('equipe_id', $this->route_params['equipe'])
                ->wherePivot('terminee','=',null)
                ->WherePivot('annulee','=',null)->first();
            $equipe->pivot->terminee = date("Y-m-d H:i:s");
            $equipe->pivot->save();

            $autre_equipe = ($carte->equipes()
                ->where('equipe_id', '!=', $this->route_params['equipe'])
                ->wherePivot('terminee','=',null)
                ->WherePivot('annulee','=',null)->count() > 0);

            if(!$autre_equipe){
                $carte->status = 0;
            }

            $equipe->statut = 0;

            $carte->save();
            $equipe->save();

            return "terminee";

        }

        return "non-terminee";
    }

    public function termineeBackAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = $carte->equipes()
                ->where('equipe_id', $this->route_params['equipe'])
                ->wherePivot('terminee','!=',null)
                ->WherePivot('annulee','=',null)->first();
            $equipe->pivot->terminee = null;
            $equipe->pivot->save();

            $autre_equipe = ($carte->equipes()
                    ->where('equipe_id', '!=', $this->route_params['equipe'])
                    ->wherePivot('terminee','=',null)
                    ->WherePivot('annulee','=',null)->count() > 0);

            if(!$autre_equipe) {
                $carte->status = 1;
            }

            $equipe->statut = 10;

            $carte->save();
            $equipe->save();

            return "terminee back";

        }

        return "non-terminee back";
    }


    public function annuleeAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = $carte->equipes()
                ->where('equipe_id', $this->route_params['equipe'])
                ->wherePivot('terminee','=',null)
                ->WherePivot('annulee','=',null)->first();

            $equipe->pivot->annulee = date("Y-m-d H:i:s");
            $equipe->pivot->save();

            $autre_equipe = ($carte->equipes()
                    ->where('equipe_id', '!=', $this->route_params['equipe'])
                    ->wherePivot('terminee','=',null)
                    ->WherePivot('annulee','=',null)->count() > 0);

            if(!$autre_equipe){
                $carte->status = 0;
            }

            $equipe->statut = 0;

            $carte->save();
            $equipe->save();

            return "annulee";

        }

        return "non-annulee";
    }

    public function annuleeBackAction(){
        if(array_key_exists('equipe', $this->route_params) && array_key_exists('carte', $this->route_params)) {
            $carte = Carte::find($this->route_params['carte']);
            $equipe = $carte->equipes()
                ->where('equipe_id', $this->route_params['equipe'])
                ->wherePivot('terminee','=',null)
                ->WherePivot('annulee','!=',null)->first();

            $equipe->pivot->annulee = null;
            $equipe->pivot->save();

            $autre_equipe = ($carte->equipes()
                    ->where('equipe_id', '!=', $this->route_params['equipe'])
                    ->wherePivot('terminee','=',null)
                    ->WherePivot('annulee','=',null)->count() > 0);

            if(!$autre_equipe){
                $carte->status = 1;
            }

            $equipe->statut = 10;

            $carte->save();
            $equipe->save();

            return "annulee back";

        }

        return "non-annulee back";
    }

    public function assignationsAction(){
        $carte = Carte::find($this->route_params['id']);
        if($carte){
            $assignations = $carte->equipes()->withPivot(['en_attente', 'reparti', 'sur_les_lieux', 'en_transport', 'arrivee_tante', 'terminee', 'annulee', 'en_direction']);

           $r_lsta = [];
           foreach ($assignations->get() as $a) {
               $r_a['enabled'] = ($a->pivot->annulee == null && $a->pivot->terminee == null);
               $r_a['equipe_id'] = $a->id;
               $r_a['no_equipe'] = $a->numero;
               $r_a['assignee'] = $a->pivot->en_attente;
               $r_a['reparti'] = $a->pivot->reparti;
               $r_a['en_direction'] = $a->pivot->en_direction;
               $r_a['sur_les_lieux'] = $a->pivot->sur_les_lieux;
               $r_a['en_transport'] = $a->pivot->en_transport;
               $r_a['arrivee_tante'] = $a->pivot->arrivee_tante;
               $r_a['terminee'] = $a->pivot->terminee;
               $r_a['annulee'] = $a->pivot->annulee;

               switch ($a->statut) {
                   case 0: //Disponible
                       $r_a['status_color'] = 'bg-success';
                       break;
                   case 1: //Réparti
                       if($carte->status == 0)
                       $r_a['status_color'] = 'bg-success';
                        else
                       $r_a['status_color'] = 'bg-warning';
                       break;
                   case 2: //En Route
                       $r_a['status_color'] = 'bg-info';
                       break;
                   case 3: //Sur les lieux
                       $r_a['status_color'] = 'bg-danger';
                       break;
                   case 4: //En transport
                       $r_a['status_color'] = 'bg-warning';
                       break;
                   case 5: //Tente
                       $r_a['status_color'] = 'bg-warning';
                       break;
               }


               $r_lsta[] = $r_a;
           }

           return json_encode($r_lsta, JSON_UNESCAPED_UNICODE);
        }
    }

    public function ouverteAction(){
        $sites = [];

        if(array_key_exists('id', $this->route_params)) {
            if($this->route_params['id'] > 0)
                $sites[] = Site::find($this->route_params['id']);
            else
                $sites = $this->_event->sites;
        }

        $cartes = [];
        foreach ($sites as $site) {
            $cartes[] = ['site_nom' => $site->nom];
            foreach ($site->cartesOuvertes()->orderBy('priorite')->get() as $c) {
                $carte = [];

                $carte['id'] = $c->id;
                $carte['emplacement'] = $c->emplacement;
                $carte['description'] = $c->description;

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

                switch ($c->status){
                    case 0: //En attente
                        $carte['status'] = 'bg-success';
                        break;case 1: //Réparti
                        $carte['status'] = 'bg-warning';
                        break;case 2: //EnRoute
                        $carte['status'] = 'bg-info';
                        break;case 3: //Sur les lieux
                        $carte['status'] = 'bg-danger';
                        break;case 4: //En transport
                        $carte['status'] = 'warning';
                        break;case 5: //Arrivée
                        $carte['status'] = 'warning';
                        break;
                }

                $carte['priorite'] = $c->priorite - 1;

                $now   = new \DateTime(date("Y-m-d H:i:s"));
                $ouverture = new \DateTime($c->heure_appel);
                $diff  = $now->diff($ouverture);
                $carte['attente'] = $diff->format("%H:%I:%S");

                $carte['equipes'] = [];
                $carte['equipes_color'] = [];
                foreach ($c->equipesAssignees()->get() as $e){
                    $carte['equipes'][] = $e->numero;
                    switch ($e->type_equipe){
                        case 0: //Terrain
                            $carte['equipes_color'][] = 'success';
                        break;
                        case 1: //Volante
                            $carte['equipes_color'][] = 'warning';
                        break;
                        case 2: //Soutien
                            $carte['equipes_color'][] = 'info';
                        break;
                    }
                }

                $cartes[] = $carte;
            }
        }

        echo json_encode($cartes,  JSON_UNESCAPED_UNICODE);
    }

    public function checkNewAction(){
        $times = $this->route_params['id'];
        $cartes = $this->_service->cartes()->where('status', 0)->get();

        $data['nouveau'] = false;

        foreach ($cartes as $carte){
            if(strtotime($carte->heure_appel)*1000 >= $times){
                $data['nouveau'] = true;
                $data['carte_id'] = $carte->id;
                $data['carte_emplacement'] = $carte->emplacement;
                $data['carte_site'] = $carte->site->nom;
                $data['carte_priorite'] = $carte->priorite -1;
            }
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public function carteStatusAction(){
        $c = Carte::find($this->route_params['id']);
        if($c){
            switch ($c->status){
                case 0: //En attente
                    $carte['status_color'] = 'success';
                    $carte['status_text'] = 'En Attente';
                break;
                    case 1: //Réparti
                $carte['status_color'] = 'warning';
                $carte['status_text'] = 'Réparti';
                break;
                case 2: //EnRoute
                $carte['status_color'] = 'info';
                $carte['status_text'] = 'En Route';
                break;
                case 3: //Sur les lieux
                $carte['status_color'] = 'danger';
                $carte['status_text'] = 'Sur les lieux';
                break;
                case 4: //En transport
                $carte['status_color'] = 'warning';
                $carte['status_text'] = 'En Transport';
                break;
                case 5: //Arrivée
                $carte['status_color'] = 'warning';
                $carte['status_text'] = 'Arrivée';
                break;
            }

            $carte['carte_fermee'] = ($c->heure_fermeture != NULL && $c->code_fermeture != NULL);

            return json_encode($carte, JSON_UNESCAPED_UNICODE);
        }
    }

    public function descriptionAction(){
        $c = Carte::find($this->route_params['id']);

        return json_encode(['description' => $c->description], JSON_UNESCAPED_UNICODE);
    }

    public function carteStatusFermetureAction(){
        $c = Carte::find($this->route_params['id']);
        if($c){
            switch ($c->code_fermeture) {
                case 1: //Réparti
                    $carte['status_color'] = 'success';
                    $carte['status_text'] = 'Fermeture Normale';
                    break;
                case 2: //EnRoute
                    $carte['status_color'] = 'danger';
                    $carte['status_text'] = 'Annulation';
                    break;
                case 3: //Non-Fondé
                    $carte['status_color'] = 'warning';
                    $carte['status_text'] = 'Non Fondé';
                    break;
                case 4: //Non-Localisé
                    $carte['status_color'] = 'primary';
                    $carte['status_text'] = 'Non Localisé';
                    break;
            }

            return json_encode($carte, JSON_UNESCAPED_UNICODE);
        }
    }
}
