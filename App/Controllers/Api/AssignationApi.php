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
use Core\ApiController;
use App\Models\Equipe As E;
use Core\View;

class AssignationApi extends ApiController
{
    public function ajouterAction(){
        $e = E::find($this->route_params['equipeid']);
        $post = $_POST;
        if(!$e)
            return false;

        $assignations = null;
        foreach ($post['benevoles'] as $ben_id){
            $ben = Benevole::find($ben_id);
            if($ben && !$ben->isAssigned() && ($e->assignations()->whereHas('benevole', function($b) use ($ben){ $b->where('id', $ben->id); })->count == 0)){
                $assignations[] = $ben;

                $a = new Assignation();
                $a->equipe()->associate($e);
                $a->benevole()->associate($ben);
                $a->save();

                $e->actif = true;
                $e->save();
            }
        }

        return json_encode($assignations);
    }

    public function benevolesDisponibleAction(){
        var_dump(Benevole::disponibles()->get());

        return json_encode(utf8_encode(Benevole::disponibles()));
    }
}