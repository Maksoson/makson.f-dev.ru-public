<?php
class DB
{
    public static function getDb()
    {
        $config = include $_SERVER["DOCUMENT_ROOT"]."/config/dbconfig.php";
        $dsn = "mysql:host={$config['host']}; dbname={$config['dbname']}";
        try {
            $db = new \PDO($dsn, $config['user'], $config['password']);
            $db->exec("set names utf8");
            return $db;
        }catch (\PDOException $e){
            echo $e -> getMessage();
            return null;
        }
    }
}