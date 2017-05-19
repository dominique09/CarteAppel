<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-18
 * Time: 17:58
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Formation extends Model
{
    protected $fillable = [
        'nom',
        'accronyme'
    ];

    public function users(){
        return $this->hasMany('App\Model\Benevole');
    }
}