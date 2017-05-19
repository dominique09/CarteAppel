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

}