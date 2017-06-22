<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-06-20
 * Time: 18:48
 */

namespace App\Controllers;


use App\Helpers\Authentication;
use App\Helpers\Token;
use App\Helpers\Validator;
use App\Models\Site as S;
use Core\Controller;
use Core\View;

class Site extends Controller
{
    protected $_event;

    public function before()
    {
        parent::before();

        $this->_event = Authentication::Auth()->evenement;

        if(is_null($this->_event)){
            self::addFlashMessage('error', 'Ooooppsss', 'Une erreur est survenue !');
            self::redirect('/home');
        }
    }
    public function createAction(){
        if(!Authentication::Auth()->hasPermission('gerer_site') && !is_null(Authentication::Auth()->evenement())){
            self::redirect('/home');
        }

        if($_POST && Token::check($_POST['token']))
            $args = $this->createSite($_POST);

        $args['token'] = Token::generate();
        View::renderTemplate('Site/create.html', $args);
    }

    private function createSite($request){
        $v = new Validator($this->errHandler);
        $v->check($request, [
            'nom' => ['required' => true, 'maxlength' => 255, 'alnum' => true],
        ]);

        if($v->passes()){
            if(Authentication::Auth()->evenement->actif){
                $s = new S();
                $s->nom = $request['nom'];
                $s->evenement()->associate(Authentication::Auth()->evenement);
                $s->save();

                self::addFlashMessage('success', 'Succès', 'Le site a bien été ajouté.');
                self::redirect('/site');
            } else {
                self::addFlashMessage('error', 'Ooppss', 'L\'événement dans lequel vous tenter d\'ajouter ce site est présentement inactif !');
            }
        }

        return [
            'old_data' => $request,
            'errors' => $v->errors()->all()
        ];
    }

    public function indexAction(){
        $args['sites'] = Authentication::Auth()->evenement->sites;
        View::renderTemplate('Site/index.html', $args);
    }
}