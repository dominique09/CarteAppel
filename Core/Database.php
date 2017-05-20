<?php
/**
 * Created by PhpStorm.
 * Users: domin
 * Date: 2017-05-15
 * Time: 19:22
 */

namespace Core;

use PDO;
use App\Config;

class Database
{
    private static $_Instance;

    public $pdo;
    protected $table;
    protected $stmt;

    public function __construct()
    {
        $dsn = "mysql:host=". Config::DB_HOST .";dbname=". Config::DB_NAME .";charset=utf8";
        $this->pdo = new PDO($dsn, Config::DB_USER, Config::DB_PASS);

        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance(){
        if(is_null(self::$_Instance))
            self::$_Instance = new Database();

        return self::$_Instance;
    }

    public function table($table)
    {
        $this->table = $table;
        return $this;
    }

    public function exists($data)
    {
        $field = array_keys($data)[0];
        return $this->where($field, '=', $data[$field])->count() ? true : false;
    }

    public function where($field, $operator, $value)
    {
        $sql = "SELECT * FROM $this->table WHERE $field $operator ?";

        $this->stmt = $this->pdo->prepare($sql);
        $this->stmt->execute([$value]);

        return $this;
    }

    public function count()
    {
        return $this->stmt->rowCount();
    }

    public function getAll()
    {
        $sql = "SELECT * FROM $this->table";
        $this->stmt = $this->pdo->query($sql);

        return $this;
    }

    public function insert($data){
        foreach (array_keys($data) as $key => $value)
        {
            $fields[] = $value;
            $values[] = ':'.$value;
        }

        $sql = "INSERT INTO $this->table (".implode(', ', $fields).") VALUES (".implode(', ', $values).")";

        $this->stmt = $this->pdo->prepare($sql);

        foreach ($data as $k => $val)
        {
            $this->stmt->bindValue(":$k", $val);
        }

        $this->stmt->execute();
    }

    public function update($conds, $data){
        foreach (array_keys($data) as $key => $value){
            $modifs[] = $value." = :".$value;
        }
        foreach ($conds as $key => $value){
            $cond[] = $key." = :c_".$key;
        }

        $sql = "UPDATE $this->table SET ".implode(', ', $modifs)." WHERE ".implode(' AND ', $cond).";";
        $this->stmt = $this->pdo->prepare($sql);

        foreach ($data as $k => $v)
            $this->stmt->bindValue(":$k", $v);

        foreach ($conds as $k => $v)
            $this->stmt->bindValue(":c_$k", $v);

        $this->stmt->execute();
    }

    public function fetchAll(){
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function first(){
        return $this->stmt->fetch()[0];
    }
}