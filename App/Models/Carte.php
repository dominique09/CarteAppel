<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-06-24
 * Time: 00:13
 */

namespace App\Models;
use App\Helpers\Authentication;
use Illuminate\Database\Eloquent\Model;

class Carte extends Model
{
    protected $fillable = [
        'emplacement',
        'description',
        'appelant_id',
        'priorite',
        'code_fermeture',
        'heure_appel',
        'heure_fermeture',
        'status'
    ];

    public function equipes(){
        return $this->belongsToMany('App\Models\Equipe');
    }

    public function service(){
        return $this->belongsTo('App\Models\Service');
    }

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }

    public function equipesAssignees(){
        return $this->equipes()->wherePivot('terminee','=',null)->WherePivot('annulee','=',null);
    }

    public function evenement(){
        return $this->service()->evenement();
    }

    public function addDescription($desc){
        $this->description .= "\r\n---". date("H:i:s") . " : ". Authentication::Auth()->username ." --- \r\n $desc";
    }
}