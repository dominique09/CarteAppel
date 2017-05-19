<?php

namespace App\Helpers;

use App\Helpers\ErrorHandler;
use Core\Database;
use Illuminate\Database\Capsule\Manager as Capsule;

class Validator
{
    protected $errorHandler;
    protected $items;

    protected $rules = ['required', 'maxlength', 'minlength', 'email', 'alnum', 'matches', 'notMatches', 'unique', 'matchesCurrentPassword', 'ddSelected'];

    public $messages = [
        'required' => 'Ce champs est requis.',
        'email' => 'Le format de l\'adresse courriel est invalide.',
        'minlength' => 'Ce champs doit avoir minimalement :satisifer caractères.',
        'maxlength' => 'Ce champs doit avoir un maximum de :satisifer caractères.',
        'alnum' => 'Ce champs ne peut contenir que des chiffres et les lettres.',
        'matches' => 'Ce champs doit correspondre.',
        'unique' => 'La valeur de ce champs est déjà prise.',
        'notMatches' => 'Ce champs ne doit pas correspondre.',
        'matchesCurrentPassword' => 'Votre mot de passe actuel ne correspond pas.',
        'ddSelected' => 'Une valeur doit être sélectionnée.'
    ];

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function fails(){
        return $this->errorHandler->hasErrors();
    }

    public function passes(){
        return !$this->fails();
    }

    public function errors(){
        return $this->errorHandler;
    }

    public function check($items, $rules)
    {
        $this->items = $items;
        foreach ($items as $item => $value) {
            if(in_array($item, array_keys($rules))) {
                $this->validate([
                    'field' => $item,
                    'value' => $value,
                    'rules' => $rules[$item]
                ]);
            }
        }

        return $this;
    }

    protected function validate($item)
    {
        $field = $item['field'];

        foreach ($item['rules'] as $rule => $satisifer) {
            if (in_array($rule, $this->rules)) {
                if(!call_user_func_array([$this, $rule], [$field, $item['value'], $satisifer]))
                {
                    $this->errorHandler->addError(
                        str_replace([':field', ':satisifer'], [$field, $satisifer], $this->messages[$rule]),
                        $field
                    );
                }
            }
        }
    }

    protected function required($field, $value, $satisifer)
    {
        return !empty(trim($value));
    }

    protected function minlength($field, $value, $satisifer)
    {
        return mb_strlen($value) >= $satisifer;
    }

    protected function maxlength($field, $value, $satisifer)
    {
        return mb_strlen($value) <= $satisifer;
    }

    protected function email($field, $value, $satisifer)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) || empty($value);
    }

    protected function alnum($field, $value, $satisifer)
    {
        $regex = "/[\s\d\w-]+/";
        return preg_match($regex, $value);
    }

    protected function matches($field, $value, $satisifer)
    {
        return $value === $this->items[$satisifer];
    }

    protected function notMatches($field, $value, $satisifer){
        return !$this->matches($field, $value, $satisifer);
    }

    protected function unique($field, $value, $satisifer)
    {
        return !Capsule::table($satisifer)->where($field, '=', $value)->exists();
    }

    protected function matchesCurrentPassword($field, $value, $satisifer){
        return (Authentication::Auth() && Hash::password_check($value, Authentication::Auth()->password));
    }

    protected function ddSelected($field, $value, $satisifer){
        return ($value > 0);
    }
}