<?php

/** @param ?string $a */
function h($a):string
{
    if ($a === null)
        return '';
    return htmlspecialchars($a, ENT_QUOTES, 'UTF-8');
}

/** @param string $a */
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

function getDb(): Bbs\Db\Db
{
    $data = dirname(__DIR__) . '/data/db.sqlite';
    $db = new Bbs\Db\Db("sqlite:$data");
    return $db;
}

function strtotimeex(string $s): int
{
    $ret = strtotime($s);
    if ($ret === false)
        $ret = time();
    return $ret;
}
