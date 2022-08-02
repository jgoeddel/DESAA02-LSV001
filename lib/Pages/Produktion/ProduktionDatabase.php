<?php
/** (c) Joachim Göddel . RLMS */


namespace App\Pages\Produktion;

use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Home\IndexDatabase;
use PDO;
use PDOException;

class ProduktionDatabase extends \App\App\AbstractMVC\AbstractDatabase
{

    function getTable($table)
    {
        return $table;
    }

    # Statische Methoden
    # Datenbankzugriff für Propduktion
    public static function dbprod(): \PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_produktion";
        $pdo = new \PDO($dataSource, $_SESSION['db']['user'], $_SESSION['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }
    public static function run($sql, $bind = NULL): bool|\PDOStatement
    {
        $stmt = self::dbprod()->prepare($sql);
        $stmt->execute($bind);
        return $stmt;
    }
    # Datenbankzugriff für Kalender
    public static function dbk(): \PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_kalender";
        $pdo = new \PDO($dataSource, $_SESSION['db']['user'], $_SESSION['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }
    public static function runk($sql, $bind = NULL): bool|\PDOStatement
    {
        $stmt = self::dbk()->prepare($sql);
        $stmt->execute($bind);
        return $stmt;
    }

    # Letztes Ergebbnis abrufen
    public static function getErgebnis($feld)
    {
        $sql = "SELECT ". $feld ." FROM a_produktion WHERE eintrag != '{$_SESSION['parameter']['heuteSQL']}' ";
        $sql .= "ORDER BY eintrag DESC LIMIT 1";
        return self::run($sql)->fetchColumn();
    }

    # Ergebbnis Tag abrufen
    public static function getErgebnisTag($datum)
    {
        $sql = "SELECT fzg FROM a_produktion WHERE eintrag = '$datum'";
        return self::run($sql)->fetchColumn();
    }
    # Summe Jahr
    public static function getSummeJahr($jahr,$feld)
    {
        $sql = "SELECT SUM(".$feld.") FROM a_produktion WHERE jahr = ?";
        return self::run($sql, [$jahr])->fetchColumn();
    }
    # Verbindung zum SQL Server aufbauen
    public static function connectSQL($srv, $db, $uid, $pw)
    {
        try {
            $conn = new PDO("sqlsrv:server=$srv;Database=$db;TrustServerCertificate=1",$uid, $pw);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e) {
            die("Fehler: ". $e->getMessage());
        }
        return $conn;
    }
    # Vorgabe Tag nach Datum
    public static function getVorgabeTag($datum)
    {
        $sql = "SELECT total FROM a_planung WHERE datum = ?";
        return self::run($sql, [$datum])->fetchColumn();
    }
    # Einen Produktionstag abrufen
    public static function getOneProductionDay($datum)
    {
        $sql = "SELECT fzg FROM a_produktion WHERE eintrag = '$datum'";
        return self::run($sql)->fetchColumn();
    }
    # Anzeige Icon produzierte Fahrzeuge
    public static function getStatusIcon($soll,$ist): string
    {
        $a = str_replace(".", "", $soll);
        $b = str_replace(".", "", $ist);
        $ergebnis = $b - $a;
        if($ergebnis < 0) $icon = '<i class="fas fa-arrow-circle-down text-danger degm45"></i>';
        if($ergebnis > 0) $icon = '<i class="fas fa-arrow-circle-up text-success deg45"></i>';
        if($ergebnis == 0) $icon = '';
        return $icon;
    }
    # Differenz Monat
    public static function getDiffMonat($monat,$jahr)
    {
        $sql = "SELECT SUM(vorgabe) AS summe FROM a_produktion ";
        $sql .= "WHERE monat = ? AND jahr = ?";
        $vorgabe = self::run($sql,[$monat,$jahr])->fetchColumn();
        $sql = "SELECT SUM(fzg) AS fzg FROM a_produktion ";
        $sql .= "WHERE monat = ? AND jahr = ?";
        $fzg = self::run($sql,[$monat,$jahr])->fetchColumn();
        return $fzg - $vorgabe;
    }
    # Produktionstage Monat
    public static function getProdTageMonat($monat,$jahr)
    {
        $sql = "SELECT COUNT(id) AS summe FROM a_produktion ";
        $sql .= "WHERE monat = ? AND jahr = ?";
        return self::run($sql,[$monat,$jahr])->fetchColumn();
    }
    # Summe Monat
    public static function getSummeMonat($monat,$jahr,$feld): string
    {
        $sql = "SELECT SUM(". $feld .") AS summe FROM a_produktion ";
        $sql .= "WHERE monat = ? AND jahr = ?";
        $a = self::run($sql,[$monat,$jahr])->fetchColumn();
        return Functions::germanNumberNoDez($a);
    }
    # Calloffs in temporäre Tabelle schreiben
    public function setCalloffs($srv, $db, $uid, $pw)
    {
        # Tabelle leeren
        self::run("TRUNCATE tmp_varianten");
        # Verbindung aufbauen
        $conn = self::connectSQL($srv, $db, $uid, $pw);
        $i = 0;
        # Aktuelle Produktionsdaten abrufen
        $sql = "SELECT TOP 1000 * FROM CallOffs WHERE LineID = 1 AND StatusCallOff = 1 ORDER BY Id_CallOff DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $query = $stmt->fetchAll(\PDO::FETCH_OBJ);
        foreach($query AS $obj){
            $sql = "SELECT TOP 750 * FROM CallOffParts WHERE ID_CallOff = '$obj->Id_Calloff'";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $query = $stmt->fetchAll(\PDO::FETCH_OBJ);
            foreach($query AS $dsp){
                $splt = explode(" ", $dsp->Materialnumber);
                if(!empty($splt[4])) $strt = substr($splt[4], 0, 4);
                if(!empty($splt[5])) $strt = substr($splt[5], 0, 4);
                if(!empty($splt[6])) $strt = substr($splt[6], 0, 4);
                if($strt == '6007'):
                    self::run("INSERT INTO tmp_varianten SET IndexMaterial = '$dsp->IndexMaterial', Materialnumber = '$dsp->Materialnumber', Description1 = '$dsp->Description1'");
                endif;
            }
        }
        return self::run("SELECT COUNT(id) FROM tmp_varianten")->fetchColumn();
    }
    # Temporäre Tabelle abfragen
    public function getTmpValues(): bool|array
    {
        $sql = "SELECT *, COUNT(id) AS summe FROM tmp_varianten GROUP BY Description1 ORDER BY summe DESC";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Abrufe FAB
    public static function getAbrufeFabStunde($datum,$anfang,$ende)
    {
        $sql = "SELECT count(zeichen) AS total FROM b_fab WHERE datum = '$datum' AND zeit BETWEEN '$anfang' AND '$ende'";
        return self::run($sql)->fetchColumn();
    }
    # Anzahl in einer Stunden
    public static function getSummeStunde($datum,$zeit)
    {
        $sql = "SELECT sum(anzahl) AS summe FROM c_date2zahl WHERE datum = '$datum' AND zeit = '$zeit'";
        return self::run($sql)->fetchColumn();
    }
    # Anzahl in einer Schicht
    public static function getSummeSchicht($datum,$start,$ende)
    {
        $sql = "SELECT sum(anzahl) AS summe FROM c_date2zahl WHERE datum = '$datum' AND zeit BETWEEN '$start' AND '$ende'";
        return self::run($sql)->fetchColumn();
    }
    # Taktzeit Stunde
    public static function getTaktzeit($anfangszeit,$anzahl): float
    {
        $takt = round((60 / $anzahl), 2); // Keine Pause
        if($anfangszeit == '08:00:00' || $anfangszeit == '16:00:00' || $anfangszeit == '00:00:00'): // Frühstückspause
            $takt = round((40 / $anzahl), 2);
        endif;
        if($anfangszeit == '11:00:00' || $anfangszeit == '03:00:00'): // Mittagspause Früh und Nachtschicht
            $takt = round((30 / $anzahl), 2);
        endif;
        if($anfangszeit == '18:00:00'): // Mittagspause Mittagschicht
            $takt = round((45 / $anzahl), 2);
        endif;
        if($anfangszeit == '19:00:00'): // Mittagspause Mittagschicht
            $takt = round((45 / $anzahl), 2);
        endif;
        if($anfangszeit == '12:00:00' || $anfangszeit == '20:00:00' || $anfangszeit == '04:00:00'):
            $takt = round((50 / $anzahl), 2);
        endif;
        return $takt;
    }
    # Produzierte Modelle
    public static function getModelle($datum,$start,$ende): bool|array
    {
        $sql = "SELECT typ FROM c_date2zahl WHERE datum = '$datum' AND zeit BETWEEN '$start' AND '$ende' GROUP BY typ";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Produzierte Modelle
    public static function getAnzahlModelle($datum,$start,$ende,$typ)
    {
        $sql = "SELECT SUM(anzahl) AS anzahl FROM c_date2zahl WHERE datum = '$datum' AND zeit BETWEEN '$start' AND '$ende' AND typ = '$typ'";
        return self::run($sql)->fetchColumn();
    }

    # Bandsicherung: Alle Einträge
    public function getBandsicherung(): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%e. %M %Y') AS tag, ";
        $sql.= "DATE_FORMAT(eintrag, '%W, %e. %M %Y') AS etag, ";
        $sql.= "DATE_FORMAT(eintrag, '%H:%i') AS zeit, ";
        $sql.= "DATE_FORMAT(datum, '%W') AS wota ";
        $sql.= "FROM b_bandsicherung ORDER BY datum DESC";
        return self::runk($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Bandsicherung: letzter Eintrag
    public function getLastRow()
    {
        $sql = "SELECT * FROM b_bandsicherung ORDER BY datum DESC LIMIT 1";
        return self::runk($sql)->fetch(PDO::FETCH_OBJ);
    }
}