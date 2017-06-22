<?php
/**
 * Created by PhpStorm.
 * Users: domin
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
        'consulter_evenement',
        'gerer_evenement',
        'gerer_service',
        'gerer_equipe',
        'gerer_site',
    ];

    public static $defaults = [
        'is_admin' => false,
        'consulter_benevole' => true,
        'gerer_benevole' => false,
        'reactiver_benevole' => false,
        'consulter_evenement' => false,
        'gerer_evenement' => false,
        'gerer_service' => false,
        'gerer_equipe' => false,
        'gerer_site' => false,
    ];

    public static function getPermissions(){
        return array_keys(self::$defaults);
    }
}