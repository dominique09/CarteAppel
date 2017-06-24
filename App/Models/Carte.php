<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-06-24
 * Time: 00:13
 */

namespace App\Models;
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
        return $this->equipes()->wherePivot('terminee','=','0000-00-00 00:00:00')->orWherePivot('annulee','=','0000-00-00 00:00:00');
    }
}