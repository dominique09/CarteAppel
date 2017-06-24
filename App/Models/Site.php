<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-06-20
 * Time: 18:37
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    protected $fillable = [
        'nom'
    ];

    public function equipes(){
        return $this->hasMany('App\Models\Equipe');
    }

    public function evenement(){
        return $this->belongsTo('App\Models\Evenement');
    }

    public function equipesOpened(){
        return $this->equipes()->where('opened_at', '!=', null)->where('closed_at', null);
    }

    public function cartesOuvertes(){
        return $this->cartes()->where('code_fermeture', null)->where('heure_fermeture', null)->where('heure_appel', '!=', null);
    }

    public function equipesDisponible(){
        return $this->equipes()->has('isDisponible', true);
    }

    public function cartes(){
        return $this->hasMany('App\Models\Carte');
    }
}