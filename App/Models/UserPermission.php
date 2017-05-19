<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-17
 * Time: 20:10
 */

namespace App\Models;


class UserPermission extends \Illuminate\Database\Eloquent\Model
{
    protected $table = "user_permissions";

    protected $fillable = [
        'is_admin',
        'consulter_benevole',
        'gerer_benevole',
        'reactiver_benevole',
    ];

    public static $defaults = [
        'is_admin' => false,
        'consulter_benevole' => true,
        'gerer_benevole' => false,
        'reactiver_benevole' => false,
    ];
}