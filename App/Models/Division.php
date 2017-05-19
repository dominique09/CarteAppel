<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-18
 * Time: 20:04
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = [
        'nom',
        'numero',
        'actif'
    ];

    public function benevoles(){
        $this->hasMany('App\Models\Benevole');
    }
}