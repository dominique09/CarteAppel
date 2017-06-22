<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Benevole extends Model
{
    protected $fillable = [
        'firstname',
        'lastname',
        'telephone_1',
        'telephone_2',
        'email',
        'actif'
    ];

    public function formation(){
        return $this->belongsTo('App\Models\Formation');
    }

    public function division(){
        return $this->belongsTo('App\Models\Division');
    }

    public function equipes(){
        return $this->belongsToMany('App\Models\Equipe');
    }

    public function isAssigned(){
       return $this->equipes()->where('actif', true)->whereHas('service', function($s){
            $s->where('actif', true);
        })->count() > 0;
    }

    public static function disponibles(){
        return self::where('actif', true)->whereDoesntHave('equipes', function($e){
            $e->where('actif', true)->whereHas('service', function($s){
                $s->where('actif', true);
            });
        });
    }
}