<?php
/**
 * Created by PhpStorm.
 * User: domin
 * Date: 2017-05-19
 * Time: 23:13
 */

namespace Core;


use Phinx\Migration\AbstractMigration;
use Illuminate\Database\Capsule\Manager as Capsule;
use \Phinx\Db\Adapter\MysqlAdapter;

class Migration extends AbstractMigration
{
    public $capsule;
    public $schema;

    public function init(){
        $this->capsule = new Capsule();
        $this->capsule->addConnection([
            'driver' => \App\Config::DB_DRIVER,
            'host' => \App\Config::DB_HOST,
            'port' => \App\Config::DB_PORT,
            'database' => \App\Config::DB_NAME,
            'username' => \App\Config::DB_MIGRATION_USER,
            'password' => \App\Config::DB_MIGRATION_PASS,
            'charset' => \App\Config::DB_CHARSET,
            'collation' => \App\Config::DB_COLL,
            'prefix' => \App\Config::DB_PREFIX,
        ]);

        $this->capsule->bootEloquent();
        $this->capsule->setAsGlobal();
        $this->schema = $this->capsule->schema();

    }
}