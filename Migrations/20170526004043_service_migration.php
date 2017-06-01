<?php

use \Core\Migration;

class ServiceMigration extends Migration
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
        $table = $this->table('services');
        $table->addTimestamps()
            ->addColumn('nom', 'string', ['limit' => 255])
            ->addColumn('details', 'string', ['limit' => 255])
            ->addColumn('debut', 'datetime')
            ->addColumn('fin', 'datetime')
            ->addColumn('evenement_id', 'integer')
                ->addForeignKey('evenement_id', 'evenements', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
            ->addColumn('actif', 'boolean', ['default' => 0])
            ->create();
    }
}
