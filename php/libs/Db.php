<?php

class Db
{
    private $connection;

    public function __construct()
    {
        $dbhost = "127.0.0.1";
        $port = "3306";
        $dbName = "project_db";
        $userName = "my_test_admin";
        $userPassword = "testing";

        $this->connection = new PDO("mysql:host=$dbhost; port=$port; dbname=$dbName", $userName, $userPassword,
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
    }

    public function getConnection()
    {
        return $this->connection;
    }
}