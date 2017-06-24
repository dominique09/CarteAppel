<?php

use \Core\Migration;

class CarteMigration extends Migration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $this->table('cartes')
        ->addTimestamps()
        ->addColumn('emplacement', 'string')
        ->addColumn('description', 'string', ['default' => 'N/A'])
        ->addColumn('appelant_id', 'integer')
        ->addColumn('priorite', 'integer', ['default' => '3'])
        ->addColumn('service_id', 'integer')
            ->addForeignKey('service_id', 'services', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
        ->addColumn('site_id', 'integer', ['null' => true])
            ->addForeignKey('site_id', 'sites', 'id', ['delete' => 'SET_NULL', 'update' => 'SET_NULL'])
        ->addColumn('code_fermeture', 'integer', ['null'=> true,'default' => null])
        ->addColumn('heure_appel', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
        ->addColumn('heure_fermeture', 'timestamp', ['null' => true, 'default' => null])
        ->create();

        $this->table('carte_equipe')
            ->addTimestamps()
        ->addColumn('carte_id', 'integer')
            ->addForeignKey('carte_id', 'cartes', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
        ->addColumn('equipe_id', 'integer')
            ->addForeignKey('equipe_id', 'equipes', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
        ->addColumn('en_attente', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
        ->addColumn('reparti', 'timestamp', ['default' => '0000-00-00 00:00:00'])
        ->addColumn('sur_les_lieux', 'timestamp', ['default' => '0000-00-00 00:00:00'])
        ->addColumn('en_transport', 'timestamp', ['default' => '0000-00-00 00:00:00'])
        ->addColumn('arrivee_tante', 'timestamp', ['default' => '0000-00-00 00:00:00'])
        ->addColumn('terminee', 'timestamp', ['default' => '0000-00-00 00:00:00'])
        ->addColumn('annulee', 'timestamp', ['default' => '0000-00-00 00:00:00'])
        ->create();
    }
}
