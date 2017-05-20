<?php

use \Core\Migration;

class UserPermissionMigration extends Migration
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
        $table = $this->table('user_permissions');
        $table->addColumn('user_id', 'integer')
                ->addForeignKey('user_id', 'users', 'id', ['update' => 'CASCADE', 'delete' => 'CASCADE'])
            ->addColumn('is_admin', 'boolean')
            ->addColumn('consulter_benevole', 'boolean')
            ->addColumn('gerer_benevole', 'boolean')
            ->addColumn('reactiver_benevole', 'boolean')
            ->addTimestamps()
            ->create();
    }
}
