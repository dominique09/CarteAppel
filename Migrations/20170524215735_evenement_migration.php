<?php

use \Core\Migration;

class EvenementMigration extends Migration
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
        $table = $this->table('evenements');
        $table->addColumn('nom', 'string', ['limit' => 255])
            ->addColumn('emplacement', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('date_debut', 'date', ['null' => true])
            ->addColumn('date_fin', 'date', ['null' => true])
            ->addColumn('actif', 'boolean', ['default' => 0])
            ->addTimestamps()
            ->create();

        $this->table('users')
            ->addColumn('evenement_id', 'integer', ['null' => true, 'default' => null])
                ->addForeignKey('evenement_id', 'evenements', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE'])
            ->update();

        $this->table('user_permissions')
            ->addColumn('consulter_evenement', 'boolean', ['default' => 0])
            ->addColumn('gerer_evenement', 'boolean', ['default' => 0])
            ->update();
    }
}
