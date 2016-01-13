<?php

class Session{

    public function __construct()
    {
    }

    public static function init()
    {
        ini_set('session.gc_maxlifetime', 6*60*60);
        ini_set('session.gc_probability', 1);
        ini_set('session.gc_divisor', 1);
        @session_start();

        return true;
    }

    public static function setCookie($key, $value, $time)
    {
        setcookie($key, $value, time() + (int) $time, '/');
    }

    public static function getCookie($key)
    {
        return isset($_COOKIE[$key]) && !is_null($_COOKIE[$key]) ? $_COOKIE[$key] : [];
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
        return $_SESSION[$key];
    }

    public static function addMessage($msg='', $type="error")
    {
        $_SESSION["messages"][]=["type"=>$type, "text"=>$msg];
    }

    public static function removeMessage($index)
    {
        unset($_SESSION["messages"][$index]);
    }

    public static function is_set($key)
    {
        return isset($_SESSION[$key]);
    }

    public static function destroy()
    {
        session_destroy();
    }
}

?>