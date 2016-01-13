<?php

class Database extends PDO{

    function __construct(){
        parent::__construct("mysql:host=".MYSQL_HOST.";dbname=".MYSQL_NAME, MYSQL_USER, MYSQL_PASS);
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->exec("set names utf8");
    }

    public function newConnection($host, $db, $user, $pw)
    {
        $this->newConnection = new PDO("mysql:host=".$host.";dbname=".$db, $user, $pw);
        $this->newConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->newConnection->exec("set names utf8");

        return $this->newConnection;
    }
}

?>