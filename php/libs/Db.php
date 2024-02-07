<?php

require_once "configuration.php";

class Db
{
    private $connection;

    public function __construct()
    {
        $dbhost = DBHOST;
        $port = DBPORT;
        $dbName = DBNAME;
        $userName = USERNAME;
        $userPassword = USERPASSWORD;

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