<?php

use \Core\Migration;

class EquipeMigration extends Migration
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
        $equipe = $this->table('equipes')->addTimestamps();
        $equipe->addColumn('numero', 'string', [])
            ->addColumn('emplacement', 'string', ['null' => true])
            ->addColumn('service_id', 'integer')
                ->addForeignKey('service_id', 'services', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addColumn('actif', 'string', ['default' => 0])
            ->create();

        $benevole_equipe = $this->table('benevole_equipe')->addTimestamps();
        $benevole_equipe->addColumn('benevole_id', 'integer')
                ->addForeignKey('benevole_id', 'benevoles', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addColumn('equipe_id', 'integer')
                ->addForeignKey('equipe_id', 'equipes', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->create();


        $this->table('user_permissions')
            ->addColumn('gerer_equipe', 'boolean', ['default' => 0])
            ->update();
    }
}
