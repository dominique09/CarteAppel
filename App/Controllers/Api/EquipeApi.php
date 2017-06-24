<?php

namespace App\Controllers\Api;

use App\Helpers\Authentication;
use App\Models\Carte;
use App\Models\Equipe;
use App\Models\Site;

class EquipeApi extends \Core\ApiController
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



    public function disponiblesAction(){
        $sites = [];

        if(array_key_exists('id', $this->route_params)) {
            if($this->route_params['id'] > 0)
                $sites[] = Site::find($this->route_params['id']);
            else
                $sites = $this->_event->sites;
        }

        $equipes = [];
        foreach ($sites as $site) {

            $equipes[] = ['site_nom' => $site->nom];
            foreach ($site->equipesOpened()->orderBy('site_id')->orderBy('statut')->orderBy('type_equipe')->orderBy('numero')->get() as $e) {
                $equipe = [];

                $equipe['id'] = $e->id;
                $equipe['numero'] = $e->numero;
                $equipe['secteur'] = $e->emplacement;

                switch ($e->type_equipe) {
                    case 0:
                        $equipe['type'] = "Terrain";
                        $equipe['type_color'] = "success";
                        break;
                    case 1:
                        $equipe['type'] = "Volante";
                        $equipe['type_color'] = "warning";
                        break;
                    case 2:
                        $equipe['type'] = "Soutien";
                        $equipe['type_color'] = "info";
                        break;
                }

                switch ($e->statut) {
                    case 0: //Disponible
                        $equipe['status'] = 'bg-success';
                        break;
                    case 1: //Réparti
                        $equipe['status'] = 'bg-warning';
                        break;
                    case 2: //En Route
                        $equipe['status'] = 'bg-info';
                        break;
                    case 3: //Sur les lieux
                        $equipe['status'] = 'bg-danger';
                        break;
                    case 4: //En transport
                        $equipe['status'] = 'bg-warning';
                        break;
                    case 5: //Tente
                        $equipe['status'] = 'bg-danger';
                        break;
                }

                $equipes[] = $equipe;
            }
        }

            echo (json_encode($equipes,  JSON_UNESCAPED_UNICODE));
    }
}