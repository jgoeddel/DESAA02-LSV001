<?php
/** (c) Joachim Göddel . RLMS */


namespace App\Pages\Servicedesk;

use App\App\AbstractMVC\AbstractDatabase;
use App\Functions\Functions;
use PDO;
use PDOStatement;

class ServicedeskDatabase extends AbstractDatabase
{
    function getTable($table)
    {
        return $table;
    }

    # Statische Methoden
    # Datenbankzugriff für Servicedesk
    public static function dbadmin(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_servicedesk";
        $pdo = new \PDO($dataSource, $_SESSION['db']['user'], $_SESSION['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }
    public static function run($sql, $bind = NULL): bool|PDOStatement
    {
        $stmt = self::dbadmin()->prepare($sql);
        $stmt->execute($bind);
        return $stmt;
    }

    # Kalendereinträge abrufen
    public static function getKalenderEintrag($datum): bool|array
    {
        $sql = "SELECT * FROM b_serviceauftrag WHERE datum LIKE '$datum%' ORDER BY id DESC LIMIT 10";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    # Neuen Serviceauftrag eintragen
    public function insert($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        $sql = "INSERT INTO b_serviceauftrag SET aid = '$abteilung', ";
        $sql.= "bid = '$bereich', ";
        $sql.= "status = '1', ";
        $sql.= "titel = '$titel', ";
        $sql.= "beschreibung = '$beschreibung', ";
        $sql.= "user = '{$_SESSION['user']['dbname']}', ";
        $sql.= "datum = now()";
        self::run($sql);
    }

    # offene Einträge (eigene)
    public function getOpenEntryUser(): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%W, %d. %M %Y, %H:%i') AS eintrag ";
        $sql.= "FROM b_serviceauftrag WHERE user = '{$_SESSION['user']['dbname']}' ";
        $sql.= "AND status < 6 ORDER BY datum DESC";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # offene Einträge (Admin)
    public function getOpenEntry()
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%W, %d. %M %Y, %H:%i') AS eintrag ";
        $sql.= "FROM b_serviceauftrag WHERE user != '{$_SESSION['user']['dbname']}' ";
        $sql.= "AND status < 6 ORDER BY datum DESC";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # beendete Einträge (Admin)
    public function getCloseEntry()
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%W, %d. %M %Y, %H:%i') AS eintrag ";
        $sql.= "FROM b_serviceauftrag WHERE user != '{$_SESSION['user']['dbname']}' ";
        $sql.= "AND status = 9 ORDER BY datum DESC";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    # einen offenen Eintrag
    public function getOneEntry($id)
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%W, %d. %M %Y, %H:%i') AS eintrag ";
        $sql.= "FROM b_serviceauftrag WHERE id = ?";
        return self::run($sql,[$id])->fetch(PDO::FETCH_OBJ);
    }
    # Bearbeiter ermitteln
    public static function getBearbeiter($id)
    {
        $sql = "SELECT uid FROM c_user2service WHERE sid = '$id' AND aktiv = '1'";
        return self::run($sql)->fetchColumn();
    }
    # Startdatum ermitteln
    public static function getStartDate($id)
    {
        $sql = "SELECT DATE_FORMAT(datum, '%Y-%m-%dT%H:%i:%s') AS tag FROM b_log WHERE sid = '$id' AND eintrag = 'Bearbeitung gestartet'";
        return self::run($sql)->fetchColumn();
    }
    # Datum ermitteln
    public static function getDateQuery($id,$eintrag,$art)
    {
        $sql = "SELECT datum FROM b_log WHERE sid = '$id' AND eintrag = '$eintrag' ORDER BY datum ".$art." LIMIT 1";
        return self::run($sql)->fetchColumn();
    }
    # Anzahl Aufgaben Servicedesk je User ermitteln
    public static function countAnzahl($uid)
    {
        $sql = "SELECT COUNT(id) FROM c_user2service WHERE uid = '$uid' AND status < '5' AND aktiv = '1'";
        return self::run($sql)->fetchColumn();
    }
    # Bearbeitunsinformationen abrufen
    public static function getRowBearbeiten($id)
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%W, %d. %M %Y, %H:%i') AS eintrag ";
        $sql.= "FROM c_user2service WHERE sid = ? ";
        $sql.= "ORDER BY datum DESC LIMIT 1";
        return self::run($sql, [$id])->fetch(PDO::FETCH_OBJ);
    }
    # Detailinformationen abrufen
    public static function getInfo($id,$text,$status=1)
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%W, %e. %M %Y, %H:%i') AS tag FROM b_log WHERE sid = '$id' ";
        $sql.= "AND eintrag = '$text' AND status = '$status' ORDER BY id DESC LIMIT 1";
        $a = self::run($sql)->fetch(PDO::FETCH_OBJ);
        return empty($a) ? '-' : $a;
    }
    # Prüfen, ob der Eintrag n.i.O. ist
    public function nio($id)
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%W') AS wota, ";
        $sql.= "DATE_FORMAT(datum, '%W, %e. %M %Y, %H:%i') AS datum ";
        $sql.= "FROM b_nio WHERE sid = '$id' AND status = '1'";
        return self::run($sql)->fetch(PDO::FETCH_OBJ);
    }
    # Mein Auftrag
    public function auftragUebernehmen($id)
    {
        self::run("UPDATE b_log SET status = '0' WHERE sid = '$id'"); # Status auf 0 setzen
        self::run("UPDATE c_user2service SET aktiv = '0' WHERE sid = '$id'"); # Bearbeiter löschen
        self::run("INSERT INTO c_user2service SET uid = '{$_SESSION['user']['id']}', sid = '$id', status = '2', user = '{$_SESSION['user']['dbname']}', datum = now(), aktiv = '1'"); # Neuen Bearbeiter setzen
        self::run("UPDATE b_serviceauftrag SET status = '2' WHERE id = '$id' LIMIT 1"); # Status ändern
        self::run("INSERT INTO b_log SET sid = '$id', eintrag = '{$_SESSION['text']['t_bearbeitungUebernommen']}', user = '{$_SESSION['user']['dbname']}', uid = '{$_SESSION['user']['id']}', datum = now(), status = '1'");
    }
    # Auftrag starten
    public function auftragStart($id)
    {
        self::run("UPDATE b_serviceauftrag SET status = '3' WHERE id = '$id' LIMIT 1"); # Status ändern
        self::run("INSERT INTO b_log SET sid = '$id', eintrag = '{$_SESSION['text']['t_bearbeitungGestartet']}', user = '{$_SESSION['user']['dbname']}', uid = '{$_SESSION['user']['id']}', datum = now(), status = '1'");
        self::run("UPDATE b_nio SET status = '0' WHERE sid = '$id'"); # Status auf 0 setzen
    }
    # Auftrag Pause
    public function auftragPause($id)
    {
        self::run("UPDATE b_serviceauftrag SET status = '4' WHERE id = '$id' LIMIT 1"); # Status ändern
        self::run("INSERT INTO b_log SET sid = '$id', eintrag = '{$_SESSION['text']['t_bearbeitungPausiert']}', user = '{$_SESSION['user']['dbname']}', uid = '{$_SESSION['user']['id']}', datum = now(), status = '1'");
    }
    # Auftrag weiter
    public function auftragWeiter($id)
    {
        self::run("UPDATE b_serviceauftrag SET status = '3' WHERE id = '$id' LIMIT 1"); # Status ändern
        self::run("UPDATE b_log SET status = '0' WHERE sid = '$id' AND eintrag = '{$_SESSION['text']['t_bearbeitungPausiert']}' AND status = '1'"); # Status ändern
        self::run("INSERT INTO b_log SET sid = '$id', eintrag = '{$_SESSION['text']['t_bearbeitungFortgesetzt']}', user = '{$_SESSION['user']['dbname']}', uid = '{$_SESSION['user']['id']}', datum = now(), status = '1'");
    }
    # Auftrag beenden
    public function auftragEnde($id)
    {
        self::run("UPDATE b_serviceauftrag SET status = '5' WHERE id = '$id' LIMIT 1"); # Status ändern
        self::run("UPDATE b_nio SET status = '0' WHERE sid = '$id'"); # Status auf 0 setzen
        self::run("INSERT INTO b_log SET sid = '$id', eintrag = '{$_SESSION['text']['t_bearbeitungBeendet']}', user = '{$_SESSION['user']['dbname']}', uid = '{$_SESSION['user']['id']}', datum = now(), status = '1'");
    }
    # Auftrag nio
    public function auftragNIO($id)
    {
        self::run("UPDATE b_serviceauftrag SET status = '1' WHERE id = '$id' LIMIT 1"); # Status ändern
        self::run("UPDATE b_log SET status = '0' WHERE sid = '$id'"); # Status ändern
        self::run("INSERT INTO b_nio SET sid = '$id', status = '1', anmerkung = '{$_SESSION['text']['t_bearbeitungNIO']}', datum = now(), user = '{$_SESSION['user']['dbname']}'"); # Status auf 1 setzen
        self::run("INSERT INTO b_log SET sid = '$id', eintrag = '{$_SESSION['text']['t_bearbeitungNIO']}', user = '{$_SESSION['user']['dbname']}', uid = '{$_SESSION['user']['id']}', datum = now(), status = '1'");
        self::run("UPDATE c_user2service SET aktiv = '0' WHERE sid = '$id'");
    }
    # Auftrag abschliesseb
    public function auftragAbschluss($id)
    {
        self::run("UPDATE b_serviceauftrag SET status = '9' WHERE id = '$id' LIMIT 1"); # Status ändern
        self::run("UPDATE c_user2service SET status = '9' WHERE sid = '$id'"); # Status ändern
        self::run("UPDATE b_nio SET status = '0' WHERE sid = '$id'"); # Status auf 0setzen
        self::run("INSERT INTO b_log SET sid = '$id', eintrag = '{$_SESSION['text']['t_serviceauftragAbgeschlossen']}', user = '{$_SESSION['user']['dbname']}', uid = '{$_SESSION['user']['id']}', datum = now(), status = '1'");
    }
    # Kommentar speichern
    public function insertKommentar($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        $kommentar = str_replace("<br></p>","", $kommentar);
        $kommentar = str_replace("<p>","", $kommentar);
        self::run("INSERT INTO b_anmerkung SET sid = '$id', anmerkung = '$kommentar', datum = now(), user = '{$_SESSION['user']['dbname']}'");
        self::run("INSERT INTO b_log SET sid = '$id', eintrag = '{$_SESSION['text']['i_kommentarGespeichert']}', user = '{$_SESSION['user']['dbname']}', uid = '{$_SESSION['user']['id']}', datum = now(), status = '1'");
    }
    # Kommentare zählen
    public function getAnzahlKommentare($id)
    {
        return self::run("SELECT COUNT(id) FROM b_anmerkung WHERE sid = '$id'")->fetchColumn();
    }
    # Dateien zählen
    public function getAnzahlFiles($id)
    {
        return self::run("SELECT COUNT(id) FROM b_files WHERE sid = '$id'")->fetchColumn();
    }
    # Alle Kommentare abrufen
    public function getAlleKommentare($id)
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%W') AS wota, ";
        $sql.= "DATE_FORMAT(datum, '%W, %d. %M %Y, %H:%i') AS datum ";
        $sql.= "FROM b_anmerkung WHERE sid = '$id'";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Alle Dateien abrufen
    public function getAllFiles($id)
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%W') AS wota, ";
        $sql.= "DATE_FORMAT(datum, '%W, %d. %M %Y, %H:%i') AS datum ";
        $sql.= "FROM b_files WHERE sid = '$id'";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Datei löschen
    public function deleteFile($id,$datei)
    {
        unlink($_SERVER['DOCUMENT_ROOT'] . "/lib/Pages/Servicedesk/MVC/View/files/$datei");
        unlink($_SERVER['DOCUMENT_ROOT'] . "/lib/Pages/Servicedesk/MVC/View/files/small_$datei");
        $sql = "DELETE FROM b_files WHERE id = '$id' LIMIT 1";
        self::run($sql);
    }
}