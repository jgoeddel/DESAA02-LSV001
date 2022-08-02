<?php
/** (c) Joachim Göddel . RLMS */
namespace App\Connections;

use PDO;

class ConMySQL
{
    public static function cpod($dataSource): PDO
    {
        $pdo = new \PDO($dataSource, $_SESSION['db']['user'], $_SESSION['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }
    # Administration
    public function rhs_admin(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_admin";
        return self::cpod($dataSource);
    }
    # Produktion
    public function rhs_produktion(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_produktion";
        return self::cpod($dataSource);
    }
    # Störungen
    public function rhs_stoerungen(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_stoerungen";
        return self::cpod($dataSource);
    }
    # Logbuch
    public function rhs_logbuch(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_logbuch";
        return self::cpod($dataSource);
    }
    # Kundenänderungen
    public function rhs_kundenaenderungen(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_kundenaenderungen";
        return self::cpod($dataSource);
    }
    # Servicedesk
    public function rhs_servicedesk(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_servicedesk";
        return self::cpod($dataSource);
    }
    # Schulungen
    public function rhs_schulungen(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_schulungen";
        return self::cpod($dataSource);
    }
    # Rotationsplan
    public function rhs_rotationsplan(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_rotationsplan";
        return self::cpod($dataSource);
    }
    # Change Management
    public function rhs_cm(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_cm";
        return self::cpod($dataSource);
    }
}