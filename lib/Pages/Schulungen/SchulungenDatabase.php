<?php
/** (c) Joachim Göddel . RLMS */

namespace App\Pages\Schulungen;

use App\App\AbstractMVC\AbstractDatabase;
use App\Pages\Administration\AdministrationDatabase;
use DateTime;
use PDO;

class SchulungenDatabase extends AbstractDatabase
{

    function getTable($table)
    {
        return $table;
    }

    # Statische Methoden
    # Datenbankzugriff für Rotationsplan
    public static function dbs(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_schulungen";
        $pdo = new \PDO($dataSource, $_SESSION['db']['user'], $_SESSION['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }

    public static function run($sql, $bind = NULL): bool|\PDOStatement
    {
        $stmt = self::dbs()->prepare($sql);
        $stmt->execute($bind);
        return $stmt;
    }

    # Alle Räume aus vorhandenen Schulungen gruppiert zurückgeben
    public function getRooms()
    {
        $sql = "SELECT raum FROM b_schulung GROUP BY raum ORDER BY raum";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Alle Schulungsarten abrufen
    public function getArt()
    {
        $sql = "SELECT * FROM b_art ORDER BY art";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    # Neue Schulung speichern
    public function insertSchulung($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }

        # Ende der Schulung ermitteln
        $a = new DateTime($datum." ".$start.":00");
        $ende = $a->modify("+".$dauer." minutes");
        $ende = $ende->format('H:i:s');

        # Prüfen, ob bereits eine Schulung eingetragen ist (Datum,Uhrzeit,Raum)
        $b = $this->checkSchulung($datum,$raum,$start,$ende);
        if(empty($b)){
            # Basisdaten eintragen
            # Inhalt anpassen
            $inhalt = str_replace("<br>","",$inhalt);
            $sql = "INSERT INTO b_schulung SET datum = '$datum', abteilung = '$abteilung', schicht = '$schicht', raum = '$raum',";
            $sql.= "start = '$start', ende = '$ende', dauer = '$dauer', art = '$art', thema = '$thema', inhalt = '$inhalt',";
            $sql.= "teilnehmer = '$teilnehmer', status = '0', ersteller = '{$_SESSION['user']['dbname']}', erstelldatum = now()";
            self::run($sql);
            $id = self::run("SELECT MAX(id) FROM b_schulung")->fetchColumn();
            echo "1|".$_SESSION['text']['h_schulungGespeichert']."";


            # Schulungsleiter
            foreach($name AS $row){
                # Name teilen
                $x = explode(" ", $row);
                # Mitarbeiter ID ermitteln
                $mid = AdministrationDatabase::getUserID($x[0], $x[1]);
                self::run("INSERT INTO c_schulung2leiter SET sid = '$id', mid = '$mid', mitarbeiter = '{$_SESSION['user']['dbname']}', datum = now()");
            }

        } else {
            echo "0|".$_SESSION['text']['h_schulungNichtGespeichert']."|".$_SESSION['text']['i_schulungNichtGespeichert']."";
        }

    }

    # Prüfen, ob bereits eine Schulung eingetragen ist
    public function checkSchulung($datum,$raum,$start,$ende)
    {
        $sql = "SELECT id FROM b_schulung WHERE datum = '$datum' AND raum = '$raum' AND start BETWEEN '$start' AND '$ende'";
        return self::run($sql)->fetchColumn();
    }
}