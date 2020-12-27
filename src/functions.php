<?php

/** @param ?string $a */
function h($a):string
{
    return htmlspecialchars($a, ENT_QUOTES, 'UTF-8');
}

/** @param string */
function br($a): string
{
    return nl2br(h($a));
}

/** @param string|array<mixed,mixed>|null $a */
function maybe_string(&$a):?string
{
    if ($a === null)
        return null;
    $s = filter_var($a);
    if ($s === false || $s === '')
        return null;
    return $s;
}

/** @param string|array<mixed,mixed>|null $a */
function maybe_int(&$a): ?int
{
    if ($a === null)
        return null;

    $i = filter_var($a, FILTER_VALIDATE_INT);
    if ($i === false)
        return null;

    return (int)$i;
}

function getPdo(): PDO
{
    $data = dirname(__DIR__) . '/data/db.sqlite';
    $pdo = new PDO("sqlite:$data");

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $pdo;
}
