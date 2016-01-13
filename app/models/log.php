<?php
abstract class Log extends Model{

    public $user;
    public $data;

    public function show()
    {
        return $this->user . ", " . $this->data;
    }

    public function save_to_db($data = [], $table){
        if (empty($data)) {
            return false;
        }
        $query = "INSERT INTO {$table} ".array_keys($data). " VALUES (".str_repeat("?", count($data)).")";

        return $this->execQuery($query, array_values($data), "exec");
    }

    public function save_to_file($data = [], $table){
        if (empty($data)) {
            return false;
        }
        $query = "INSERT INTO {$table} ".array_keys($data). " VALUES (".str_repeat("?", count($data)).")";

        return $this->execQuery($query, array_values($data), "exec");
    }
}
?>