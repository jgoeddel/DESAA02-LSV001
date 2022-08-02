<?php
/** (c) Joachim Göddel . RLMS */

namespace App\Pages\Home;

use App\App\AbstractMVC\AbstractDatabase;
use PDO;

class IndexDatabase extends AbstractDatabase
{

    function getTable($table)
    {
        return $table;
    }



    # Sprache ermitteln
    public static function getBrowserLanguage(): string
    {
        $l = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $l = substr($l,0,2);
        return $l;
    }


    # Statische Methoden
    # Datenbankzugriff für Admintabellen
    public static function dbadmin(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_admin";
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

    # Sprache in Session schreiben
    public static function setLanguageSession(string $lang, string $table): array
    {
        $lang = strtolower($lang);
        $_SESSION['lang'] = $lang;
        $sql = "SELECT keyword, de, if(en != '', en, de) AS en FROM $table";
        $_SESSION['text'] = array();
        $a = self::run($sql)->fetchAll(\PDO::FETCH_OBJ);
        foreach($a AS $row){
            $_SESSION['text']['' . $row->keyword . ''] = $row->$lang;
        }
        return $_SESSION['text'];
    }

    # Berechtigungsebene
    public function getEbene($seite)
    {
        $sql = "SELECT id FROM b_seiten WHERE link = ?";
        return self::run($sql,[$seite])->fetchColumn();
    }

    # Übersetzungen
    public function i18n()
    {
        $lang = (isset($_POST['lang'])) ? $_POST['lang'] : 'de';
        $i18n = self::setLanguageSession($lang, 'a_i18n');
    }

    # CHANGE MANAGEMENT
    # Alle Citycodes abrufen, die für das Change Management freigeschaltet sind
    public static function getCMCitycode(): bool|array
    {
        $sql = "SELECT * FROM b_citycode WHERE cm = '1' ORDER BY citycode";
        return self::run($sql)->fetchAll(\PDO::FETCH_OBJ);
    }
    # Berechtigung Citycode
    public static function checkRechteCitycode($citycode)
    {
        $c = strtolower($citycode);
        $sql = "SELECT id FROM b_seiten WHERE i18n = 'c_$c'";
        $id = self::run($sql)->fetchColumn();
        $sql = "SELECT id FROM c_mitarbeiter2bereich WHERE mid = '{$_SESSION['user']['id']}' AND bid = '$id'";
        return self::run($sql)->fetchColumn();
    }
    # Ausgabe Standortdetails
    public static function dspLocationAddress($citycode)
    {
        $sql = "SELECT * FROM b_citycode WHERE citycode = ?";
        $a = self::run($sql, [$citycode])->fetch(\PDO::FETCH_OBJ);
        echo "<p>$a->firma<br>$a->citycode<br>$a->strasse $a->nummer<br>$a->plz $a->ort</p>";
    }
    # Ausgabe Telefon
    public static function dspPhoneUser($id,$nummer)
    {
        $sql = "SELECT * FROM c_mitarbeiter2phone WHERE mid = ?";
        $a = self::run($sql, [$id])->fetch(\PDO::FETCH_OBJ);
        echo (!empty($a)) ? $a->country. " " .$a->$nummer : '';
    }
    # Ausgabe E-Mail
    public static function dspMailUser($id)
    {
        $sql = "SELECT email FROM c_mitarbeiter2mail WHERE mid = ?";
        echo self::run($sql, [$id])->fetchColumn();
    }
    # Ausgabe aller Mitarbeiter, außer dem aktuell angemeldeten
    public static function selectMaWork($mid): bool|array
    {
        $sql = "SELECT * FROM b_mitarbeiter WHERE id != ? AND id != '{$_SESSION['user']['id']}' ORDER BY name,vorname";
        return self::run($sql, [$mid])->fetchAll(\PDO::FETCH_OBJ);
    }

    # Ausgabe aller Mitarbeiter eines Standortes
    public static function selectMaCC($citycode): bool|array
    {
        $sql = "SELECT * FROM b_mitarbeiter WHERE citycode = ? ORDER BY name,vorname";
        return self::run($sql, [$citycode])->fetchAll(\PDO::FETCH_OBJ);
    }

    # Ausgabe aller Mitarbeiter
    public static function selectMa(): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%d.%m.%Y %H:%i') AS login FROM b_mitarbeiter ORDER BY name,vorname";
        return self::run($sql)->fetchAll(\PDO::FETCH_OBJ);
    }

    # Details zu User abrufen
    public static function getUserInfo($id)
    {
        $sql = "SELECT * FROM b_mitarbeiter WHERE id = ?";
        return self::run($sql, [$id])->fetch(PDO::FETCH_OBJ);
    }

    # Alle Seiten für die Administration abrufen
    public static function getAllPages($lvl, $dsp, $pid = 0)
    {
        $sql = "SELECT * FROM b_seiten WHERE level = ? ";
        $sql .= "AND dsp = ? ";
        $sql .= "AND pid = ? ORDER BY sort";
        return self::run($sql, [$lvl,$dsp,$pid])->fetchAll(PDO::FETCH_OBJ);
    }

}