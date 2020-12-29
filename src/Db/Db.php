<?php

namespace Bbs\Db;

use PDO;
use PDOStatement;

class Db
{
    /** @var string */
    private $dsn;
    public function __construct(string $dsn)
    {
        $this->dsn = $dsn;
    }
    private function getPdo(): PDO
    {
        static $pdo;
        if ($pdo !== null)
            return $pdo;
        $pdo = new PDO($this->dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }

    /**
     * @param array<int|string,int|float|string> $params
     * @return PDOStatement<mixed>
     */
    private function query(string $sql, array $params): PDOStatement
    {
        $stmt = $this->getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    /**
     * @template T
     * @param array<int|string,int|float|string> $params
     * @param class-string<T> $cls
     * @return array<int,T>
     */
    public function getAll(string $sql, array $params, string $cls)
    {
        $stmt = $this->query($sql, $params);
        $stmt->setFetchMode(PDO::FETCH_CLASS, $cls, []);
        $ret = $stmt->fetchAll();
        if ($ret === false)
            throw new \Exception('PDO Error');
        return $ret;
    }

    /** 
     * @template T
     * @param array<int|string,int|float|string> $params
     * @param class-string<T> $cls
     * @return PDOStatement<T>
     */
    public function select(string $sql, array $params, string $cls): PDOStatement
    {
        $stmt = $this->query($sql, $params);
        $stmt->setFetchMode(PDO::FETCH_CLASS, $cls, []);
        return $stmt;
    }
    /** 
     * @template T
     * @param array<int|string,int|float|string> $params
     * @param class-string<T> $cls
     * @return ?T
     */
    public function selectOne(string $sql, array $params, string $cls)
    {
        $stmt = $this->select($sql, $params, $cls);
        foreach ($stmt as $row){
            $stmt->closeCursor();
            return $row;
        }
        return null;
    }
    /** 
     * @param array<int|string,int|float|string> $params
     * @return PDOStatement<mixed>
     */
    public function execOne(string $sql, array $params): PDOStatement
    {
        $stmt = $this->query($sql, $params);
        $c = $stmt->rowCount();
        if ($c != 1)
            throw new \Exception('exec count error: ' . $c);
        return $stmt;
    }
    /** 
     * @param array<int|string,int|float|string> $params
     * @return PDOStatement<mixed>
     */
    public function unsafeQuery(string $sql, array $params): PDOStatement
    {
        return $this->query($sql, $params);
    }
    public function lastInsertId(): ?int
    {
        $pdo = $this->getPdo();
        $id = $pdo->lastInsertId();
        return is_numeric($id) ? (int)$id : null;
    }
}
