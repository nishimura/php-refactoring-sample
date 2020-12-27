<?php

namespace Bbs\Session;

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
    }

    public static function get($key)
    {
        self::start();

        return $_SESSION[$key] ?? null;
    }

    public static function pop($key)
    {
        self::start();
        $ret = self::get($key);
        if ($ret !== null)
            self::remove($key);
        return $ret;
    }

    public static function add($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function remove($key)
    {
        self::start();
        unset($_SESSION[$key]);
    }
}
