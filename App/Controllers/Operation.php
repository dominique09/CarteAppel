<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-06-22
 * Time: 07:52
 */

namespace App\Controllers;

use App\Helpers\Authentication;
use Core\Controller;
use Core\View;

class Operation extends Controller
{
    protected $_event;
    protected $_service;

    public function before()
    {
        parent::before();

        $this->_event = Authentication::Auth()->evenement;
        $this->_service = $this->_event->serviceActif()->first();

        if(is_null($this->_event) || is_null($this->_service)){
            self::addFlashMessage('error', 'Ooooppsss', 'Une erreur est survenue !');
            self::redirect('/home');
        }
    }

    public function indexAction(){
        if(array_key_exists('site', $this->route_params))
            $site = \App\Models\Site::find($this->route_params['site']);
        else
            $site['id'] = 0;

        $args['activeSite'] = $site;
        $args['lstSites'] = $this->_event->sites->where('actif', true);
        View::renderTemplate('Operation/index.html', $args);
    }
}