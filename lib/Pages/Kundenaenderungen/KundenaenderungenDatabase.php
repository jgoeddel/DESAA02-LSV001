<?php
/** (c) Joachim Göddel . RLMS */


namespace App\Pages\Kundenaenderungen;

use PDO;

class KundenaenderungenDatabase extends \App\App\AbstractMVC\AbstractDatabase
{

    function getTable($table)
    {
        return $table;
    }

    # Statische Methoden
    # Datenbankzugriff für Admintabellen
    public static function dbadmin(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_kundenaenderungen";
        $pdo = new \PDO($dataSource, $_SESSION['db']['user'], $_SESSION['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }
    public static function run($sql, $bind = NULL): bool|\PDOStatement
    {
        $stmt = self::dbadmin()->prepare($sql);
        $stmt->execute($bind);
        return $stmt;
    }

    # Kalendereinträge abrufen
    public static function getKalenderEintrag($datum): bool|array
    {
        $sql = "SELECT * FROM b_evkhaupt WHERE job1 = ? ORDER BY id DESC LIMIT 5";
        return self::run($sql, [$datum])->fetchAll(PDO::FETCH_OBJ);
    }
}