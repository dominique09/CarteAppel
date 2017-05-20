<?php

use \Core\Migration;

class BenevoleMigration extends Migration
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
        $divTable = $this->table('divisions');
        $divTable->addColumn('nom', 'string', ['limit' => 255])
            ->addColumn('numero', 'string', ['limit' => 5])
            ->addColumn('actif', 'boolean', ['default' => 1])
            ->addTimestamps()
            ->create();

        $formTable = $this->table('formations');
        $formTable->addColumn('nom', 'string', ['limit' => 100])
            ->addColumn('accronyme', 'string', ['limit' => 3])
            ->addTimestamps()
            ->create();

        $table = $this->table('benevoles');
        $table->addColumn('prenom', 'string', ['limit' => 255])
            ->addColumn('nom', 'string', ['limit' => 255])
            ->addColumn('telephone_1', 'string', ['null' => true, 'limit' => 15])
            ->addColumn('telephone_2', 'string', ['null' => true,'limit' => 15])
            ->addColumn('email', 'string', ['null' => true, 'limit' => 255])
            ->addColumn('division_id', 'integer', ['null' => true])
                ->addForeignKey('division_id', 'divisions', 'id', ['update' => 'CASCADE', 'delete' => 'SET_NULL'])
            ->addColumn('formation_id', 'integer', ['null' => true])
                ->addForeignKey('formation_id', 'formations', 'id', ['update' => 'CASCADE', 'delete' => 'SET_NULL'])
            ->addColumn('actif', 'boolean', ['default' => 1])
            ->addTimestamps()
        ->create();
    }
}
