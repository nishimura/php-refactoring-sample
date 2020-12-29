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

/**
 * @template T
 * @param mixed $fromObj
 * @param class-string<T> $to
 * @return T
 */
function cast($fromObj, $to)
{
    $ref = new \ReflectionClass($to);
    /** @var T $obj */
    $obj = $ref->newInstanceWithoutConstructor();

    foreach (get_object_vars($fromObj) as $k => $v){
        if (property_exists($obj, $k)){
            if ($obj instanceof Bbs\Type\CastProperty)
                $obj->castAssign($k, $v);
            else
                $obj->$k = $v;
        }
    }

    $constructor = $ref->getConstructor();
    if ($constructor)
        $constructor->invoke($obj);

    return $obj;
}

/**
 * @template T
 * @param array<int,mixed> $fromObjs
 * @param class-string<T> $to
 * @return array<int,T>
 */
function castList($fromObjs, $to)
{
    return array_map(function($obj) use ($to) {
        return cast($obj, $to);
    }, $fromObjs);
}
