<?php
/** (c) Joachim Göddel . RLMS */


namespace App\Pages\Administration;

use PDO;

class AdministrationDatabase extends \App\App\AbstractMVC\AbstractDatabase
{

    function getTable($table)
    {
        return $table;
    }

    # Datenbankzugriff für Admintabellen
    public static function dba(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_admin";
        $pdo = new \PDO($dataSource, $_SESSION['db']['user'], $_SESSION['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }
    # Kurzform Abfrage
    public static function run($sql, $bind = NULL): bool|\PDOStatement
    {
        $stmt = self::dba()->prepare($sql);
        $stmt->execute($bind);
        return $stmt;
    }

    # Alle Seiten (nach Parameter) für das Menü abrufen
    public static function getAllPages($lvl, $dsp, $pid = 0): bool|array
    {
        $sql = "SELECT * FROM b_seiten WHERE level = '$lvl' ";
        $sql .= "AND dsp = '$dsp' ";
        $sql .= "AND pid = '$pid' ORDER BY sort";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Alle Seiten für die Administration abrufen
    public static function getAllAdminPages(): bool|array
    {
        $sql = "SELECT * FROM b_seiten WHERE adm = '1'  AND pid = '0' ORDER BY i18n";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Alle Unterseiten für einen Bereich
    public static function getAllSubPages($sub): bool|array
    {
        $sql = "SELECT * FROM b_seiten WHERE sub = '$sub' ORDER BY i18n";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Rechte auf Seite bezogen
    public static function checkRechtePage($mid,$bid)
    {
        $sql = "SELECT COUNT(id) FROM c_mitarbeiter2part WHERE mid = ? AND bid = ?";
        return self::run($sql,[$mid,$bid])->fetchColumn();
    }
    # Rechte auf Rechte bezogen
    public static function checkRechtePageDetail($mid,$bid,$pid)
    {
        $sql = "SELECT COUNT(id) FROM c_mitarbeiter2part WHERE mid = ? AND bid = ? AND pid = ?";
        return self::run($sql,[$mid,$bid,$pid])->fetchColumn();
    }
    # Aushang Startseite
    public static function getAushangIndex(): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(start, '%d.%m.%Y') AS anzeige ";
        $sql .= "FROM a_aushang ";
        $sql .= "WHERE ende > '{$_SESSION['parameter']['heuteSQL']}' ";
        $sql .= "ORDER BY start DESC, datum DESC LIMIT 6";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    # Einen User abrufen
    public function getUser($id,$username)
    {
        $sql = "SELECT * FROM b_mitarbeiter WHERE id = ? OR username = ?";
        return self::run($sql,[$id,$username])->fetch(PDO::FETCH_OBJ);
    }
    # Userid anhand Namen ermitteln
    public static function getUserID($vorname,$name)
    {
        $sql = "SELECT id FROM b_mitarbeiter WHERE vorname = '$vorname' AND name = '$name'";
        return self::run($sql)->fetchColumn();
    }
    # Rechte setzen
    public function setUserRights($id)
    {
        // Login speichern
        $sql = "UPDATE b_mitarbeiter SET datum = now() WHERE id = '$id'";
        self::run($sql);
        // Berechtigung
        $sql = "SELECT * FROM c_mitarbeiter2bereich WHERE mid = '$id'";
        $sql = self::run($sql)->fetchAll(PDO::FETCH_OBJ);
        foreach ($sql as $usr) {
            // Berechtigung für Bereich
            $sql = "SELECT COUNT(id) AS anzahl FROM c_mitarbeiter2part ";
            $sql .= "WHERE bid = '" . $usr->bid . "' AND mid = '$id'";
            $br = self::run($sql)->fetchColumn();
            // Berechtigung in Session schreiben
            if ($br > 0) $_SESSION['rechte']['' . $usr->bid . ''] = 1;
            $sql = "SELECT * FROM c_mitarbeiter2part WHERE bid = '" . $usr->bid . "' AND mid = '$id'";
            $prt = self::run($sql)->fetchAll(PDO::FETCH_OBJ);
            foreach ($prt as $part) {
                $_SESSION['rechte']['' . $usr->bid . '.' . $part->pid . ''] = 1;
            }
        }
    }

    # Details zu User abrufen
    public static function getUserInfo($id)
    {
        $sql = "SELECT * FROM b_mitarbeiter WHERE id = ?";
        return self::run($sql, [$id])->fetch(PDO::FETCH_OBJ);
    }

    # Namen anhand ID formatiert ausgeben (Produktion)
    public static function getNameMAFormat($id): string
    {
        $sql = "SELECT vorname,name FROM b_mitarbeiter WHERE id = ?";
        $a = self::run($sql,[$id])->fetch(\PDO::FETCH_OBJ);
        return (isset($a)) ? "<b>".$a->name."</b>, ".$a->vorname : "N.N.";
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
        $sql = "SELECT email FROM b_mitarbeiter WHERE id = ?";
        echo self::run($sql, [$id])->fetchColumn();
    }
    # Abfrage E-Mail
    public static function getMaiAddresslUser($id)
    {
        $sql = "SELECT email FROM b_mitarbeiter WHERE id = ?";
        return self::run($sql, [$id])->fetchColumn();
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
        $sql = "SELECT * FROM b_mitarbeiter ORDER BY name,vorname";
        return self::run($sql)->fetchAll(\PDO::FETCH_OBJ);
    }

    # Prüfen ob Mitarbeiter mit dem entsprechenden Anfangsbuchstaben vorhanden sind
    public static function checkLetter($letter)
    {
        $sql = "SELECT COUNT(id) FROM b_mitarbeiter WHERE name LIKE '$letter%'";
        return self::run($sql)->fetchColumn();
    }
    # Mitarbeiter bereits vorhanden
    public static function checkInsertMa($vorname,$name,$abteilung,$citycode)
    {
        $sql = "SELECT COUNT(id) FROM b_mitarbeiter WHERE vorname = ? AND name = ? AND abteilung = ? AND citycode = ?";
        return self::run($sql, [$vorname,$name,$abteilung,$citycode])->fetchColumn();
    }
    # Neuen Mitarbeiter speichern
    public function setMitarbeiter($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        # Mitarbeiter bereits eingetragen?
        if(isset($id) && $id > 0){
            if(isset($password) && $password != ''){
                $pepper = '%Wa5manhalts01nd13Dat3nbank5chr31bt?';
                $hash = password_hash($password . $pepper, PASSWORD_BCRYPT, array('cost' => 12));
            }
            $lang = substr($citycode, 0, 2);
            $sql = "UPDATE b_mitarbeiter SET vorname = ?, ";
            $sql.= "name = ?, ";
            $sql.= "abteilung = ?, ";
            $sql.= "citycode = ?, ";
            $sql.= "username = ?, ";
            $sql.= "email = ?, ";
            $sql.= "wrk_schicht = ?, ";
            $sql.= "wrk_abteilung = ?, ";
            if(isset($password) && $password != '') {
                $sql .= "password = '$hash', ";
            }
            $sql.= "status = '$status', ";
            $sql.= "lang = '$lang' ";
            $sql.= "WHERE id = '$id' LIMIT 1";
            self::run($sql, [$vorname,$name,$abteilung,$citycode,$username,$email,$wrk_schicht,$wrk_abteilung]);
        } else {
            # Passwort verschlüsseln
            $pepper = '%Wa5manhalts01nd13Dat3nbank5chr31bt?';
            $hash = password_hash($password . $pepper, PASSWORD_BCRYPT, array('cost' => 12));
            $lang = substr($citycode, 0, 2);
            $sql = "INSERT INTO b_mitarbeiter SET vorname = ?, ";
            $sql.= "name = ?, ";
            $sql.= "abteilung = ?, ";
            $sql.= "citycode = ?, ";
            $sql.= "username = ?, ";
            $sql.= "email = ?, ";
            $sql.= "wrk_schicht = ?, ";
            $sql.= "wrk_abteilung = ?, ";
            $sql.= "password = '$hash', ";
            $sql.= "status = '1', ";
            $sql.= "lang = '$lang'";
            self::run($sql, [$vorname,$name,$abteilung,$citycode,$username,$email,$wrk_schicht,$wrk_abteilung]);
        }
    }
    # Rechte setzen
    public function setRechte($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        $a = str_replace('"','', $id);
        $a = explode(",", $a);
        # Alle Rechte löschen
        $sql = "DELETE FROM c_mitarbeiter2part WHERE mid = '$mid' AND pid = '$rechte'";
        self::run($sql);
        foreach($a as $value){
            $sql = "DELETE FROM c_mitarbeiter2bereich WHERE mid = '$mid' AND bid = '$value'";
            self::run($sql);
            # Prüfen, ob der Bereich bereits eingetragen ist
            $x = self::run("SELECT id FROM c_mitarbeiter2bereich WHERE mid = '$mid' AND bid = '$value'")->fetchColumn();
            if(empty($x)){
                $sql = "INSERT INTO c_mitarbeiter2bereich SET mid = '$mid', bid = '$value'";
                self::run($sql);
            }
            $sql = "INSERT INTO c_mitarbeiter2part SET mid = '$mid', bid = '$value', pid = '$rechte'";
            self::run($sql);
        }
    }
    # Abteilungen abrufen
    public function getAbteilungen(): bool|array
    {
        $sql = "SELECT * FROM b_abteilung ORDER BY abteilung";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Abteilungen Change Managementabrufen
    public function getAbteilungenCM(): bool|array
    {
        $sql = "SELECT * FROM b_abteilung WHERE cm = '1' ORDER BY abteilung";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Abteilungen Rotationsplan
    public function getAbteilungenRP(): bool|array
    {
        $sql = "SELECT * FROM b_abteilung WHERE rotationsplan > '0' ORDER BY rotationsplan";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Prüft Rechte eines Mitarbeiters auf einen Bereich
    public static function checkUserBereich($mid,$bid)
    {
        $sql = "SELECT id FROM c_mitarbeiter2bereich WHERE mid = ? AND bid = ?";
        return self::run($sql, [$mid,$bid])->fetchColumn();
    }
    # Citycodes aus Mitarbeitertabelle
    public static function getCCMA(): bool|array
    {
        $sql = "SELECT citycode FROM b_mitarbeiter GROUP BY citycode ORDER BY citycode";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    # Mailempfänger zusammenstellen
    public static function getEmailUserID($citycode): bool|array
    {
        # Seiten ID für die Berechtigung abrufen
        $id = self::run("SELECT id FROM b_seiten WHERE link = '$citycode'")->fetchColumn();
        # Alle User ID's abrufen, die den Citycode und CM dürfen
        return self::run("SELECT mid FROM c_mitarbeiter2bereich GROUP BY mid HAVING max(bid = '$id') = 1 AND max(bid = '36') = 1")->fetchAll(PDO::FETCH_OBJ);
    }

    # Alle Abteilungen nach vorgegebenem Parameter
    public static function getAllAbt($table,$key)
    {
        $sql = "SELECT * FROM ". $table ." WHERE ". $key ." = '1' ORDER BY abteilung";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Eine Abteilung nach vorgegebenem Parameter
    public static function getOneAbt($table,$id)
    {
        $sql = "SELECT abteilung FROM ". $table ." WHERE id = '$id'";
        return self::run($sql)->fetchColumn();
    }
    # Alle Bereiche nach vorgegebenem Parameter
    public static function getAllBereich($table)
    {
        $sql = "SELECT * FROM ". $table ." ORDER BY bereich";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Einen Bereich nach vorgegebenem Parameter
    public static function getOneBereich($table,$id)
    {
        $sql = "SELECT bereich FROM ". $table ." WHERE id = '$id'";
        return self::run($sql)->fetchColumn();
    }
    # Status Servicedesk
    public static function getStatusBadge($id): string
    {
        $status = self::run("SELECT status FROM b_status WHERE id = '$id'")->fetchColumn();
        switch($id):
            case 1:
                $sts = '<div class="badge badge-warning">'.$_SESSION['text'][''.$status.''].'</div>';
                break;
            case 9:
            case 2:
                $sts = '<div class="badge badge-primary">'.$_SESSION['text'][''.$status.''].'</div>';
                break;
            case 3:
                $sts = '<div class="badge badge-success">'.$_SESSION['text'][''.$status.''].'</div>';
                break;
            case 4:
                $sts = '<div class="badge badge-info">'.$_SESSION['text'][''.$status.''].'</div>';
                break;
            case 5:
                $sts = '<div class="badge badge-danger">'.$_SESSION['text'][''.$status.''].'</div>';
                break;
        endswitch;
        return $sts;
    }
    # Alle Administratoren aus einem Bereich
    public static function getAllAdmin($bereich,$mid=0): bool|array
    {
        $sql = "SELECT mid FROM c_mitarbeiter2part WHERE bid = '$bereich' AND pid = '0' AND mid != '$mid'";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
}