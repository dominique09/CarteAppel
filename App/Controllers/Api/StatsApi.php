<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-06-27
 * Time: 20:38
 */

namespace App\Controllers\Api;


use App\Models\Evenement;
use Core\ApiController;

class StatsApi extends ApiController
{
    public function eventAction(){
        $event_id = $this->route_params['id'];

        $event = Evenement::find($event_id);
        $cartes = $event->cartes()->get();

        foreach ($cartes as $carte){

        }
    }
}