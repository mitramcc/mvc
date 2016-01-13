<?php
class class Log_acesso extends Log{

    const TBL_LOG = "log_acesso";

    public $ip;
    public $browser;

    public function show()
    {
        return  parent::show();
    }

    public function save($data = [], $method = "file"){
        return parent::save($data, self::TBL_LOG);
    }
}
?>