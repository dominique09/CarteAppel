<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-06-02
 * Time: 18:47
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Equipe extends Model
{
    protected $fillable = [
        'numero',
        'emplacement',
        'actif',
        'type_equipe',
    ];

    public function service(){
        return $this->belongsTo('App\Models\Service');
    }

    public function benevoles(){
        return $this->belongsToMany('App\Models\Benevole');
    }

    public function site(){
        return $this->belongsTo('App\Models\Site');
    }

    public function isEditable(){
        return true;
    }

    public function isDissociable(){
        return true;
    }

    public function isDisponible(){
        return true;
    }

    public function type(){
        switch ($this->type_equipe){
            case 0:
                return "Équipe Terrain";
                break;
            case 1:
                return "Équipe Volante";
                break;
            case 2:
                return "Soutien";
                break;
        }
    }

    public function statut(){
        return "A Determiner";
    }
}