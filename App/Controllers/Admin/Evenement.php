<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-24
 * Time: 18:51
 */

namespace App\Controllers\Admin;

use App\Helpers\Authentication;
use App\Helpers\Token;
use App\Helpers\Validator;
use Core\Controller;
use App\Models\Evenement as E;
use Core\View;
use DateTime;

class Evenement extends Controller
{
    public function before()
    {
        if(!(Authentication::Auth()->hasPermission('gerer_evenement') OR Authentication::Auth()->hasPermission('consulter_evenement') ))
            self::redirect('home');
    }

    public function indexAction(){
        $args['evenements'] = E::all()->sortByDesc('date_debut')->sortByDesc('date_fin');
        View::renderTemplate('/Admin/Evenement/index.html', $args);
    }

    public function details(){
        $e = E::find($this->route_params['id']);

        if(!$e){
            self::addFlashMessage('error', 'Oooppss', 'Une erreur est survenue !');
            self::redirect('admin/evenement');
        }

        $args['old_data'] = $e;
        View::renderTemplate('admin/evenement/details.html', $args);
    }

    public function createAction(){
        if(!Authentication::Auth()->hasPermission('gerer_evenement'))
            self::redirect('home');

        if($_POST && Token::check($_POST['token']))
            $args = $this->createEvenement($_POST);

        $args['token'] = Token::generate();
        View::renderTemplate('Admin/Evenement/create.html', $args);
    }

    public function editAction(){
        if(!Authentication::Auth()->hasPermission('gerer_evenement'))
            self::redirect('home');

        $e = E::find($this->route_params['id']);
        if(!$e){
            self::addFlashMessage('error', 'Oooppss', 'Une erreur est survenue !');
            self::redirect('admin/evenement');
        }

        $args['old_data'] = $e;

        if($_POST && Token::check($_POST['token']))
            $args = $this->editEvenement($_POST, $e);

        $args['token'] = Token::generate();
        View::renderTemplate('Admin/Evenement/edit.html', $args);

    }

    private function editEvenement($request,E $e){
        $v = new Validator($this->errHandler);
        $v->check($request, [
            'nom' => ['required' => true],
            'emplacement' => ['required' => true],
            'date_debut' => ['required' => true],
            'date_fin' => ['required' => true, 'olderOrEqualDate' => 'date_debut'],
        ]);
        $e->nom = $request['nom'];
        $e->emplacement = $request['emplacement'];
        $e->date_debut = DateTime::createFromFormat('d/m/Y', $request['date_debut']);
        $e->date_fin = DateTime::createFromFormat('d/m/Y', $request['date_fin']);

        ///TODO: Gérer acrif / inactif événement avec validation au niveau des service (si service en cours ne pas pouvoir désactiver)
        //$e->actif = 1;

        if($v->passes()){
            $e->save();

            self::addFlashMessage('success', "Succès", "L'événement {$request['nom']} a bien été sauvegardé.");
            self::redirect('/admin/evenement');
        }

        return [
            'old_data' => $e,
            'errors' => $v->errors()->all(),
        ];
    }

    private function createEvenement($request){
        $v = new Validator($this->errHandler);
        $v->check($request, [
            'nom' => ['required' => true],
            'emplacement' => ['required' => true],
            'date_debut' => ['required' => true],
            'date_fin' => ['required' => true, 'olderOrEqualDate' => 'date_debut'],
        ]);

        if($v->passes()){
            $e = new E;
            $e->nom = $request['nom'];
            $e->emplacement = $request['emplacement'];
            $e->date_debut = DateTime::createFromFormat('d/m/Y', $request['date_debut']);
            $e->date_fin = DateTime::createFromFormat('d/m/Y', $request['date_fin']);
            $e->actif = 1;
            $e->save();

            self::addFlashMessage('success', "Succès", "L'événement {$request['nom']} a bien été ajouté.");
            self::redirect('/admin/evenement');
        }

        return [
            'errors' => $v->errors()->all(),
            'old_data' => $request
        ];
    }
}