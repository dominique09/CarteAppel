<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-06-04
 * Time: 14:21
 */

namespace App\Controllers\Api;

use App\Controllers\Equipe;
use App\Helpers\Authentication;
use App\Helpers\Token;
use App\Models\Assignation;
use App\Models\Benevole;
use Core\Controller;
use App\Models\Equipe As E;
use Core\View;

class EquipeApi extends Controller
{
    public function before(){
        if(!Authentication::Auth()){
            self::redirect('home');
        }
    }

    public function listBenevolesDisponiblesAction(){
        $ben = Benevole::disponibles();
        $data = null;
        foreach ($ben as $b){
            $data[] = [
                'id' => $b->id,
                'prenom' => $b->prenom,
                'nom' => $b->nom,
                'formation' => $b->formation->accronyme,
                'division' => $b->division->numero,
                ];
        }

        echo json_encode(['data' => $data]);
    }

    public function listBenevolesEquipe(){
        $e = E::find($this->route_params['id']);

        echo json_encode(['data' => null]);
    }

    public function assignerBenevoleAction(){
        if(Authentication::Auth()->hasPermission('gerer_equipe'))
            self::redirect('home');

        if(!$_POST)
            self::redirect('home');

        $b = Benevole::find($_POST['benevole_id']);
        $e = E::find($_POST['equipe_id']);

        if(!$e OR !$b)
            self::redirect('/equipe');

        $a = new Assignation();
        $a->benevole = $b;
        $a->equipe = $e;
        $a->save();

        echo json_encode(['data' => $a]);
    }
}