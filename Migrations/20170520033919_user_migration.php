<?php

use \Core\Migration;
use \Phinx\Db\Adapter\MysqlAdapter;

class UserMigration extends Migration
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
        $table = $this->table('users');
        $table->addColumn('firstname', 'string', ['limit' => MysqlAdapter::TEXT_SMALL])
            ->addColumn('lastname', 'string', ['limit' => MysqlAdapter::TEXT_SMALL])
            ->addColumn('username', 'string', ['limit' => MysqlAdapter::TEXT_SMALL])->addIndex(['username'], ['unique' => true, 'name' => 'un_users_email'])
            ->addColumn('email', 'string', ['limit' => MysqlAdapter::TEXT_SMALL])
            ->addColumn('password', 'string', ['limit' => MysqlAdapter::TEXT_SMALL])
            ->addColumn('active', 'boolean', ['default' => 0])
            ->addColumn('active_hash', 'string', ['limit' => MysqlAdapter::TEXT_SMALL])
            ->addColumn('recover_hash', 'string', ['limit' => MysqlAdapter::TEXT_SMALL])
            ->addColumn('remember_identifier', 'string', ['limit' => MysqlAdapter::TEXT_SMALL])
            ->addColumn('remember_token', 'string', ['limit' => MysqlAdapter::TEXT_SMALL])
            ->addTimestamps()
            ->create();
    }
}
