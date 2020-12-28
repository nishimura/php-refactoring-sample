<?php

namespace Bbs\Session;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
    }

    public static function get(string $key): ?string
    {
        self::start();

        return $_SESSION[$key] ?? null;
    }

    public static function pop(string $key): ?string
    {
        self::start();
        $ret = self::get($key);
        if ($ret !== null)
            self::remove($key);
        return $ret;
    }

    public static function add(string $key, string $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }
}
