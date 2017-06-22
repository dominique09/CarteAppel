<?php

use \Core\Migration;

class SiteMigration extends Migration
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
        $site = $this->table('sites')->addTimestamps();
            $site->addColumn('nom', "string")
                ->addColumn('evenement_id', 'integer')
                ->addForeignKey('evenement_id', 'evenements', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addColumn('actif', 'boolean', ['default'=>1])
            ->create();

        $equipe = $this->table('equipes');
            $equipe->addColumn('site_id', 'integer')
                    ->addForeignKey('site_id', 'sites', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
                ->addColumn('type_equipe', 'integer', ['default' => 0])
            ->update();
    }
}
