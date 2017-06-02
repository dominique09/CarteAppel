<?php
/**
 * Created by PhpStorm.
 * Users: domin
 * Date: 2017-05-18
 * Time: 22:04
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    protected $fillable = [
        'nom',
        'emplacement',
        'dateDebut',
        'dateFin',
        'actif'
    ];

    public function users(){
        return $this->hasMany('App\Models\User');
    }

    public function services(){
        return $this->hasMany('App\Models\Service');
    }

    public function serviceActif(){
        return $this->services->where('actif', '=', 1);
    }
}