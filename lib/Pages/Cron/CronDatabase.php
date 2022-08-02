<?php
/** (c) Joachim Göddel . RLMS */
namespace App\Pages\Cron;

use App\App\AbstractMVC\AbstractDatabase;
use PDO;

class CronDatabase extends AbstractDatabase
{

    function getTable($table)
    {
        return $table;
    }
    # Datenbankzugriff für Admintabellen
    public static function dba($db): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=$db";
        $pdo = new \PDO($dataSource, $_SESSION['db']['user'], $_SESSION['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }
    # Kurzform Abfrage
    public static function run($sql, $db, $bind = NULL): bool|\PDOStatement
    {
        $stmt = self::dba($db)->prepare($sql);
        $stmt->execute($bind);
        return $stmt;
    }

    # Change Management

    # Anzahl offener Anfragen nach Standort
    public static function getOpenCM($citycode): bool|array
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM base WHERE location = ? AND status < 6";
        return self::run($sql,'rhs_cm',[$citycode])->fetchColumn();
    }
}
