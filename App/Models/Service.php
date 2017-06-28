<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-25
 * Time: 20:52
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'nom',
        'details',
        'debut',
        'fin',
        'actif'
    ];

    public function evenement(){
        return $this->belongsTo('App\Models\Evenement');
    }

    public function equipes(){
        return $this->hasMany('App\Models\Equipe');
    }

    public function cartes(){
        return $this->hasMany('App\Models\Carte');
    }
}