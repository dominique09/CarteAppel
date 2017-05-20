<?php

namespace App\Models;

use App\Config;

class User extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'users';
    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        'password',
        'active',
        'active_hash',
        'recover_hash',
        'remember_identifier',
        'remember_token'
    ];

    public function permissions(){
        return $this->hasOne('App\Models\UserPermission', 'user_id');
    }

    public function listPermissions(){
        $perms = [];

        foreach (UserPermission::getPermissions() as $permission){
            if($this->hasPermission($permission))
                $perms[] = $permission;
        }
        return $perms;
    }

    public function hasPermission($permission){
        return (bool) $this->permissions->{$permission};
    }

    public function isAdmin(){
        return $this->hasPermission('is_admin');
    }

    public function activateAccount($hashPass){
        $this->update([
            'active' => true,
            'active_hash' => null,
            'password' => $hashPass
        ]);
    }

    public function getAvatarUrl(){
        return Config::GRAVATAR_BASE_USR."". md5($this->email)."?d=". Config::GRAVATAR_DEFAULT ."&r=g";
    }

    public function updateRememberCredentials($ident, $token){
        $this->update([
            'remember_identifier' => $ident,
            'remember_token' => $token,
        ]);
    }

    public function removeRememberCredentials(){
        $this->updateRememberCredentials(null, null);
    }
}