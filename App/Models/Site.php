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

    public function equipesDisponible(){
        return $this->equipes()->has('isDisponible', true);
    }
}