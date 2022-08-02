<?php
/** (c) Joachim Göddel . RLMS */

namespace App\Pages\Rotationsplan;

use App\App\AbstractMVC\AbstractDatabase;
use App\Functions\Functions;
use DateTime;
use Exception;
use PDO;

class RotationsplanDatabase extends AbstractDatabase
{
    function getTable($table)
    {
        return $table;
    }

    # Statische Methoden
    # Datenbankzugriff für Rotationsplan
    public static function dbr(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_rotationsplan";
        $pdo = new \PDO($dataSource, $_SESSION['db']['user'], $_SESSION['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }

    public static function run($sql, $bind = NULL): bool|\PDOStatement
    {
        $stmt = self::dbr()->prepare($sql);
        $stmt->execute($bind);
        return $stmt;
    }

    # Datenbankzugriff für Admintabellen
    public static function dba(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_admin";
        $pdo = new \PDO($dataSource, $_SESSION['db']['user'], $_SESSION['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }

    public static function runa($sql, $bind = NULL): bool|\PDOStatement
    {
        $stmt = self::dba()->prepare($sql);
        $stmt->execute($bind);
        return $stmt;
    }

    # Prüfen, ob zu dem Datum ein Plan eingetragen ist
    public function planExistsDate($datum)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM c_person2station WHERE datum = ? ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}'";
        return self::run($sql, [$datum])->fetchColumn();
    }


    # Datum der eingetragenen Pläne für das Submenü
    public function getPlan(): bool|array
    {
        $sql = "SELECT datum, DATE_FORMAT(datum, '%d.%m.%Y') AS tag FROM c_person2station ";
        $sql .= "WHERE datum >= '{$_SESSION['parameter']['heuteSQL']}' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}'";
        $sql .= "GROUP BY datum ORDER BY datum";
        return self::run($sql)->fetchAll(\PDO::FETCH_OBJ);
    }

    # Zählt die Mitarbeiter einer Abteilung auf einer Schicht
    public function getPersonalSchicht()
    {
        $sql = "SELECT count(id) AS anzahl FROM b_mitarbeiter WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND name != 'k.' ";
        $sql .= "AND status = '1'";
        return self::run($sql)->fetchColumn();
    }

    # Ruft die Mitarbeiter aus der Datenbank ab
    public function getPersonalTable($tb): bool|array
    {
        $sql = "SELECT * FROM b_mitarbeiter WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND status = '1' ";
        $sql .= "AND name != 'k.' ";
        $sql .= "ORDER BY name,vorname ";
        $sql .= "LIMIT $tb";
        return self::run($sql)->fetchAll(\PDO::FETCH_OBJ);
    }

    # Zählt die erforderlichen Mitarbeiter
    public function getPersonalAbteilung()
    {
        $sql = "SELECT SUM(mitarbeiter) AS anzahl FROM b_station WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' AND status = '1'";
        return self::run($sql)->fetchColumn();
    }

    # Zählt die anwesenden Mitarbeiter
    public function getPersonalAnwesend()
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM c_anwesenheit WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND datum = '{$_SESSION['wrk']['datum']}'";
        return self::run($sql)->fetchColumn();
    }

    # Zählt die Einsätze des Mitarbeiters gesamt
    public static function getAnzahlEinsaetze($mid)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM c_person2station WHERE uid = ?";
        return self::run($sql, [$mid])->fetchColumn();
    }

    # Zählt die Qualifikationen des Users
    public static function getQualiMitarbeiter($mid): int
    {
        $anz = 0;
        $sql = "SELECT * FROM c_qualifikation WHERE uid = ?";
        $a = self::run($sql, [$mid])->fetchAll(\PDO::FETCH_OBJ);
        if ($a) {
            foreach ($a as $row) {
                if (self::getBidFromMatrix($row->sid) == 3) {
                    $anz++;
                }
            }
        }
        return $anz;
    }

    # Bereichs ID aus Matrix (Abteilung) ermitteln
    public static function getBidFromMatrix($id)
    {
        $sql = "SELECT bid FROM c_matrix2abteilung WHERE id = ?";
        return self::run($sql, [$id])->fetchColumn();
    }

    # Funktionen, die beim anlegen eines neuen Plans benötigt werden
    public function getKA()
    {
        $sql = "SELECT id FROM b_mitarbeiter WHERE schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND vorname = 'A.' ";
        $sql .= "AND name = 'k.'";
        return self::run($sql)->fetchColumn();
    }

    # ID der Station nach Name
    public function getStationID($bezeichnung)
    {
        $sql = "SELECT id FROM b_station WHERE bezeichnung = ? ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}'";
        return self::run($sql, [$bezeichnung])->fetchColumn();
    }

    # Personal einer Schicht / Abteilung abrufen
    public function getMitarbeiterSchicht($limit): bool|array
    {
        $sql = "SELECT * FROM b_mitarbeiter WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND status = '1' ";
        $sql .= "AND name != 'k.' ";
        $sql .= "ORDER BY name,vorname ";
        $sql .= "LIMIT $limit";
        return self::run($sql)->fetchAll(\PDO::FETCH_OBJ);
    }

    # Alle Stationen einer Abteilung abrufen
    public function getStationAbteilung(): bool|array
    {
        $sql = "SELECT * FROM b_station WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND status = '1' ";
        $sql .= "ORDER BY station";
        return self::run($sql)->fetchAll(\PDO::FETCH_OBJ);
    }

    # Mitarbeiter aus einer Zeitschiene Rotationsplan löschen
    public function deleteMitarbeiterPlan($id, $springer)
    {
        self::run("UPDATE c_person2station SET uid = '$springer' WHERE id = '$id' LIMIT 1");
    }

    # Mitarbeiter aus einer Zeitschiene Anwesenheit löschen
    public function deleteMitarbeiterAnwesend($id, $z)
    {
        $tbr = "zs$z";
        self::run("UPDATE c_anwesenheit SET " . $tbr . " = '0' WHERE uid = '$id' AND datum = '{$_SESSION['wrk']['datum']}' LIMIT 1");
    }

    # Mitarbeiter einer Zeitschiene Anwesenheit hinzufügen
    public function setMitarbeiterAnwesend($id, $z)
    {
        $tbr = "zs$z";
        # Mitarbeiter anwesend ?
        if ($this->getAnwesendMa($id) === true) {
            self::run("UPDATE c_anwesenheit SET " . $tbr . " = '1' WHERE uid = '$id' AND datum = '{$_SESSION['wrk']['datum']}' LIMIT 1");
        } else {
            self::run("INSERT INTO c_anwesenheit SET uid = '$id', datum = '{$_SESSION['wrk']['datum']}', abteilung = '{$_SESSION['user']['wrk_abteilung']}', schicht = '{$_SESSION['user']['wrk_schicht']}'");
        }

    }

    # Springer auf Station schreiben
    public function setSpringerStation($springer, $sid, $zeitschiene, $mitarbeiter)
    {
        $sql = "UPDATE c_person2station SET uid = '$springer' ";
        $sql .= "WHERE sid = '$sid' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND datum = '{$_SESSION['wrk']['datum']}' ";
        $sql .= "AND zeitschiene = '$zeitschiene' ";
        $sql .= "AND mitarbeiter = '$mitarbeiter' ";
        $sql .= "LIMIT 1";
        self::run($sql);
    }

    # Mitarbeiter auf Station schreiben
    public function setMaStation($uid, $sid, $zeitschiene, $mitarbeiter)
    {
        $sql = "UPDATE c_person2station SET uid = '$uid' ";
        $sql .= "WHERE sid = '$sid' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND datum = '{$_SESSION['wrk']['datum']}' ";
        $sql .= "AND zeitschiene = '$zeitschiene' ";
        $sql .= "AND mitarbeiter = '$mitarbeiter'";
        self::run($sql);
        $sql = "UPDATE c_qualifikation SET anzahl = anzahl + 1 WHERE uid = '$uid' AND sid = '$sid' LIMIT 1";
        self::run($sql);
    }

    # Rotationsplan updaten
    public function updateRotationsplan($uid, $guid, $sid, $springer, $zeitschiene, $mitarbeiter)
    {
        # Wenn ein Springer in der alten Station ist kann direkt der neue Mitarbeiter eingetragen werden
        if ($guid == $springer) {
            echo "UID: $uid<br>GUID: $guid<br>SID: $sid<br>SPRINGER: $springer<br>ZEITS: $zeitschiene<br>MA: $mitarbeiter";
            $alteStation = $this->getAlteStationMitarbeiter($guid, $zeitschiene);
            $this->setMaStation($springer, $alteStation->sid, $zeitschiene, $alteStation->mitarbeiter);
            $this->updateQualifikation($guid, $alteStation->sid, 'rem');
            $this->setMaStation($guid, $sid, $zeitschiene, $mitarbeiter);
        } else {
            # Alte Station des Mitarbeiters
            $alteStation = $this->getAlteStationMitarbeiter($guid, $zeitschiene);
            echo "Die alte Station, an der der Mitarbeiter eingesetzt war: $alteStation->sid<br>";
            echo "Position, an der der Mitarbeiter eingesetzt war: $alteStation->mitarbeiter<br>";
            # Springer auf die alte Station setzen
            $this->setMaStation($springer, $alteStation->sid, $zeitschiene, $alteStation->mitarbeiter);
            echo "Jetzt sollte der Springer auf die alte Station des Mitarbeiters gesetzt werden.<br>";
            # Die Qualifikation des Mitarbeiters um eins reduzieren
            $this->updateQualifikation($guid, $alteStation->sid, 'rem');
            echo "Qualifikation angepasst<br>";
            # Mitarbeiter an die neue Station schreiben
            $this->setMaStation($guid, $sid, $zeitschiene, $mitarbeiter);
            echo "Neuen Mitarbeiter in die Station geschrieben<br>";
            # Versuchen die alte Station mit einem freien Mitarbeiter zu besetzen
            $anwesend = $this->getAnwesend();
            foreach ($anwesend as $row) {
                # ist der Mitarbeiter in dieser Zeitschiene bereits eingesetzt
                if ($this->getMaAktivZeitschiene($row->uid, $zeitschiene) === false) {
                    echo "Mitarbeiter ist anwesend und nicht aktiv<br>";
                    # Hat der Mitarbeiter die Qualifikation an der Station
                    if ($this->getMaQualiStation($row->uid, $alteStation->sid) === true) {
                        echo "Der MItarbeiter hat die Qualifikation, ist frei und wird auf die Position geschrieben<br>";
                        $this->setMaStation($row->uid, $alteStation->sid, $zeitschiene, $mitarbeiter);
                        echo "Mitarbeiter hat die Qualifikation";
                        break;
                    }
                    echo "Mitarbeiter wurde aus irgendeinem Grund nicht auf die Station geschrieben<br>";
                }
            }
        }
    }

    # An welcher Station hat der Mitarbeiter vorher gearbeitet | Änderung 2022-06-15 06:16
    public function getAlteStationMitarbeiter($guid, $zeitschiene)
    {
        $sql = "SELECT * FROM c_person2station WHERE uid = '$guid' ";
        $sql .= "AND zeitschiene = '$zeitschiene' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND datum = '{$_SESSION['wrk']['datum']}' ";
        $sql .= "LIMIT 1";
        return self::run($sql)->fetch(\PDO::FETCH_OBJ);
    }

    # Qualifikation anpassen
    public function updateQualifikation($uid, $sid, $aktion)
    {
        if ($aktion == 'add') {
            $sql = "UPDATE c_qualifikation SET anzahl = anzahl + 1 ";
            $sql .= "WHERE uid = '$uid' AND sid = '$sid' LIMIT 1";
        } else {
            $sql = "UPDATE c_qualifikation SET anzahl = anzahl - 1 ";
            $sql .= "WHERE uid = '$uid' AND sid = '$sid' LIMIT 1";
        }
        self::run($sql);
    }

    # Anwesende Mitarbeiter Abteilung / Schicht
    public function getAnwesend(): bool|array
    {
        $sql = "SELECT * FROM c_anwesenheit WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND datum = '{$_SESSION['wrk']['datum']}'";
        return self::run($sql)->fetchAll(\PDO::FETCH_OBJ);
    }

    # ist der Mitarbeiter anwesend
    public function getAnwesendMa($uid): bool|array
    {
        $sql = "SELECT id FROM c_anwesenheit WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND datum = '{$_SESSION['wrk']['datum']}'";
        return self::run($sql)->fetch(\PDO::FETCH_OBJ);
    }

    # Mitarbeiter in der ausgewählten Zeitschiene aktiv ?
    public function getMaAktivZeitschiene($uid, $zeitschiene): bool
    {
        $sql = "SELECT id FROM c_person2station WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND datum = '{$_SESSION['wrk']['datum']}' ";
        $sql .= "AND zeitschiene = ? ";
        $sql .= "AND uid = ?";
        $a = self::run($sql, [$zeitschiene, $uid])->fetchColumn();
        if ($a > 0) {
            return true;
        } else {
            return false;
        }
    }

    # Qualifikation prüfen
    public function getMaQualiStation($uid, $sid): bool
    {
        $sql = "SELECT id FROM c_qualifikation WHERE uid = ? ";
        $sql .= "AND sid = ? AND status IS NULL";
        $a = self::run($sql, [$uid, $sid])->fetchColumn();
        if ($a > 0) {
            return true;
        } else {
            return false;
        }
    }

    # Mitarbeiterdetails
    public function getMitarbeiterDetails($id)
    {
        $sql = "SELECT * FROM b_mitarbeiter WHERE id = ?";
        return self::run($sql, [$id])->fetch(\PDO::FETCH_OBJ);
    }

    # Qualifikation temporär entfernen
    public function setTmpQuali($uid, $sid)
    {
        $sql = "UPDATE c_qualifikation SET status = '1' WHERE uid = ? AND sid = ?";
        self::run($sql, [$uid, $sid]);
    }

    # Handicap eintragen
    public function setMitarbeiterHandicap($uid, $sid, $start, $ende)
    {
        $sql = "INSERT INTO c_handicap SET sid = ?, uid = ?, ";
        $sql .= "start = ?, ende = ?, ";
        $sql .= "user = '{$_SESSION['user']['dbname']}', datum = now(), status = '1'";
        self::run($sql, [$sid, $uid, $start, $ende]);
        $this->setTmpQuali($uid, $sid);
    }

    # Abwesend eintragen
    public function setMitarbeiterAbwesend($uid, $start, $ende): bool
    {
        # Mitarbeiter bereits abwesend ?
        $sql = "SELECT id FROM c_abwesend WHERE uid = ? ";
        $sql .= "AND start >= CAST('$start' AS DATE) AND start <= CAST('$ende' AS DATE)";
        $a = self::run($sql, [$uid])->fetchColumn();
        if ($a > 0) {
            return false;
        } else {
            $sql = "INSERT INTO c_abwesend SET uid = ?, ";
            $sql .= "start = '$start', ende = '$ende', ";
            $sql .= "mitarbeiter = '{$_SESSION['user']['dbname']}', datum = now()";
            self::run($sql, [$uid]);
            return true;
        }
    }

    # Neuer Mitarbeiter
    public static function neuerMitarbeiter($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        $sql = "INSERT INTO b_mitarbeiter SET vorname = ?, name = ?, abteilung = '{$_SESSION['user']['wrk_abteilung']}', schicht = '{$_SESSION['user']['wrk_schicht']}', status = '1', funktion = '0'";
        self::run($sql, [$post['vorname'], $post['name']]);
    }

    # Mitarbeiter außer Haus (Details)
    public static function getAbwesendDetails($mid): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(start, '%W %d %M %Y') AS beginn, DATE_FORMAT(ende, '%W %d %M %Y') AS stop ";
        $sql .= " FROM c_abwesend WHERE uid = '$mid' AND ";
        $sql .= "start <= '{$_SESSION['parameter']['heuteSQL']}' AND ende >= '{$_SESSION['parameter']['heuteSQL']}'";
        $a = self::run($sql)->fetchAll(PDO::FETCH_OBJ);
        if (!empty($a)) {
            return $a;
        } else {
            return false;
        }
    }

    # Abwesend löschen
    public function deleteAbwesend($id)
    {
        $sql = "DELETE FROM c_abwesend WHERE id = ? ";
        $sql .= "LIMIT 1";
        self::run($sql, [$id]);
    }

    # Qualifikation löschen
    public function deleteQualiMa($sid, $uid)
    {
        $sql = "UPDATE c_qualifikation SET status = '9' WHERE sid = ? AND uid = ? ";
        $sql .= "LIMIT 1";
        self::run($sql, [$sid, $uid]);
    }

    # Qualifikation speichern
    public function setQualiMa($sid, $uid)
    {
        $sql = "SELECT id FROM c_qualifikation WHERE sid = ? AND uid = ? ";
        $sql .= "AND status != 9";
        $a = self::run($sql, [$sid, $uid])->fetchColumn();
        if ($a > 0) {
            $sql = "UPDATE c_qualifikation SET status = NULL WHERE sid = ? ";
            $sql .= "AND uid = ? LIMIT 1";
        } else {
            $sql = "INSERT INTO c_qualifikation SET sid = ?, uid = ?, ";
            $sql .= "user = '{$_SESSION['user']['dbname']}', datum = now(), ";
            $sql .= "abteilung = '{$_SESSION['user']['wrk_abteilung']}', ";
            $sql .= "schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        }
        self::run($sql, [$sid, $uid]);
    }

    # Training speichern
    public function setTrainingMa($sid, $uid)
    {
        $sql = "INSERT INTO c_matrix_training SET sid = ?, uid = ?, ";
        $sql .= "user = '{$_SESSION['user']['dbname']}', datum = now(), ";
        $sql .= "start = now()";
        self::run($sql, [$sid, $uid]);
        $sql = "INSERT INTO c_qualifikation SET sid = ?, uid = ?, ";
        $sql .= "user = '{$_SESSION['user']['dbname']}', datum = now(), ";
        $sql .= "abteilung = '{$_SESSION['user']['wrk_abteilung']}', anzahl = '0', ";
        $sql .= "schicht = '{$_SESSION['user']['wrk_schicht']}', status = '3'";
        self::run($sql, [$sid, $uid]);
    }

    # Mitarbeiter löschen
    public function deleteMa($id)
    {
        # Status ändern
        $sql = "UPDATE b_mitarbeiter SET status = '0' WHERE id = '$id' LIMIT 1";
        self::run($sql);
        # Qualifikation
        $sql = "UPDATE c_qualifikation SET status = '8' WHERE uid = '$id'";
        self::run($sql);
    }

    # Mitarbeiter Passwort ändern
    public function setMitarbeiterPassword($uid, $password, $rfid)
    {
        # $pepper
        $pepper = '%Wa5manhalts01nd13Dat3nbank5chr31bt?';
        $hash = password_hash($password . $pepper, PASSWORD_BCRYPT, array('cost' => 12));
        $setrfid = md5($rfid);
        # Passwort ändern
        $sql = "UPDATE b_mitarbeiter SET ";
        if ($password) {
            $sql .= "password = '$hash', ";
        }
        if ($rfid) {
            $sql .= "rfid = '$setrfid' ";
        }
        $sql .= "WHERE id = '$uid' LIMIT 1";
        self::run($sql);
    }

    # Station bearbeiten
    public function changeStation($post)
    {
        $sql = "UPDATE b_station SET station = '{$post['station']}', ";
        $sql .= "bezeichnung = '{$post['bezeichnung']}', ";
        $sql .= "mitarbeiter = '{$post['mitarbeiter']}', ";
        $sql .= "ergo = '{$post['ergo']}', ";
        $sql .= "qps = '{$post['qps']}', ";
        $sql .= "status = '{$post['status']}' WHERE id = '{$post['id']}' LIMIT 1";
        self::run($sql);
    }

    # Station bearbeiten
    public function neueStation($post)
    {
        $sql = "INSERT INTO b_station SET station = '{$post['station']}', ";
        $sql .= "bezeichnung = '{$post['bezeichnung']}', ";
        $sql.= "abteilung = '{$_SESSION['user']['wrk_abteilung']}', ";
        $sql.= "ergo = 'success', ";
        $sql.= "mitarbeiter = '1', ";
        $sql.= "status = '1'";
        self::run($sql);
    }

    # Anzahl der Einsätze eines Mitarbeiters gruppiert nach Stationen
    public static function getSumEinsatzMa($uid)
    {
        unset($_SESSION['einsatz']);
        $sql = "SELECT sid, COUNT(id) AS anzahl FROM c_person2station WHERE uid = ? GROUP BY sid";
        $a = self::run($sql, [$uid])->fetchAll(PDO::FETCH_OBJ);
        if(count($a) > 0){
            foreach($a AS $b){
                $_SESSION['einsatz'][] = array("$b->sid",$b->anzahl);
            }
        }
    }

    # Details Statio
    public function getStation($sid)
    {
        $sql = "SELECT * FROM b_station WHERE id = ?";
        return self::run($sql, [$sid])->fetch(PDO::FETCH_OBJ);
    }

    # Qualifikation per Station hinzufügen
    public function setQualiStation($post)
    {
        $training = 0;
        # Mitarbeiter im Training?
        $sql = "SELECT id FROM c_matrix_training ";
        $sql .= "WHERE uid = '{$post['id']}' AND sid = '{$post['sid']}' AND ende IS NULL";
        $a = self::run($sql)->fetchColumn();
        if ($a > 0) {
            $sql = "UPDATE c_matrix_training SET ende = now(), status = '9' ";
            $sql .= "WHERE uid = '{$post['id']}' AND sid = '{$post['sid']}' LIMIT 1";
            self::run($sql);
            # Qualifikation setzen
            $sql = "UPDATE c_qualifikation SET status = NULL WHERE uid = '{$post['id']}' ";
            $sql .= "AND sid = '{$post['sid']}' LIMIT 1";
            self::run($sql);
            $training = 1;
        }
        if ($training == 0) {
            # Mitarbeiter hat die Qualifikation
            $sql = "SELECT id FROM c_qualifikation WHERE uid = '{$post['id']}' ";
            $sql .= "AND sid = '{$post['sid']}'";
            $c = self::run($sql)->fetchColumn();
            var_dump($c);
            if ($c > 0) {
                # Qualifikation löschen
                $aktion = ($post['aktion'] == 'minus') ? '9' : 'NULL';
                $sql = "UPDATE c_qualifikation SET status = $aktion, ";
                $sql.= "user = '{$_SESSION['user']['dbname']}', ";
                $sql.= "datum = now() ";
                $sql.= " WHERE uid = '{$post['id']}' ";
                $sql .= "AND sid = '{$post['sid']}' LIMIT 1";
            } else {
                if($post['aktion'] == 'plus') {
                    # nein > Qualifikation stzen
                    $sql = "INSERT INTO c_qualifikation SET uid = '{$post['id']}', ";
                    $sql .= "sid = '{$post['sid']}', user = '{$_SESSION['user']['dbname']}', ";
                    $sql .= "abteilung = '{$_SESSION['user']['wrk_abteilung']}', ";
                    $sql .= "datum = now(), anzahl = '0', ";
                    $sql .= "schicht = '{$_SESSION['user']['wrk_schicht']}', status = NULL";
                }
            }
            echo $sql;
            self::run($sql);
        }
    }

    # Rotationsplan Mitarbeiter ID abrufen (anhand RFID)
    public function getMitarbeiterID($rfid)
    {
        # RFID verschlüsseln
        $rfidmd5 = md5($rfid);
        # Daten vorhanden ?
        if (isset($_SESSION['user']['wrk_schicht']) && $_SESSION['user']['wrk_schicht'] != '') {
            $sql = "SELECT id FROM b_mitarbeiter WHERE rfid = '$rfidmd5'";
            return self::run($sql)->fetchColumn();
        } else {
            $sql = "SELECT * FROM b_mitarbeiter WHERE rfid = '$rfid'";
            $row = self::run($sql)->fetch(\PDO::FETCH_OBJ);
            $_SESSION['user']['wrk_schicht'] = $row->schicht;
            $_SESSION['user']['wrk_abteilung'] = $row->abteilung;
            return $row->id;
        }
    }

    # Datenbankzugriff für QM
    public static function dbqm(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_dokumente";
        $pdo = new \PDO($dataSource, $_SESSION['db']['user'], $_SESSION['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }

    # Kurzform Abfrage
    public static function runqm($sql, $bind = NULL): bool|\PDOStatement
    {
        $stmt = self::dbqm()->prepare($sql);
        $stmt->execute($bind);
        return $stmt;
    }

    # Alle Dokumente für die Abteilung abrufen
    public static function getDokumente($abteilung): bool|array
    {
        # Tabelle
        $tabelle = array("", "Frontcorner", "Kühler", "Motorband", "AKL");
        $sql = "SELECT * FROM dokumente WHERE abteilung = '{$tabelle[$abteilung]}' AND art = 'QPS' AND frei = '1' ORDER BY nr";
        return self::runqm($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    # Ermitteln, welche Schicht im Moment arbeiten sollte
    public static function getZeitschieneSchicht(): int
    {
        // Kalenderwoche
        $date = new DateTime('' . $_SESSION['wrk']['datum'] . '');
        $kw = $date->format('W');
        // Gerade oder ungerade KW
        if ($kw % 2 == 0) {
            if ($_SESSION['user']['wrk_schicht'] == 1) {
                return 1;
            } else {
                return 2;
            }
        } else {
            if ($_SESSION['user']['wrk_schicht'] == 1) {
                return 2;
            } else {
                return 1;
            }
        }
    }

    # Namen anhand ID formatiert ausgeben (Produktion)
    public static function getNameMAFormat($id): string
    {
        $sql = "SELECT vorname,name FROM b_mitarbeiter WHERE id = ?";
        $a = self::run($sql, [$id])->fetch(\PDO::FETCH_OBJ);
        return (isset($a)) ? "<b>" . $a->name . "</b>, " . $a->vorname : "N.N.";
    }

    # Abteilung ausgeben
    public static function getAbteilungRotationsplan($id)
    {
        $sql = "SELECT abteilung FROM b_abteilung WHERE rotationsplan = ?";
        return self::runa($sql, [$id])->fetchColumn();
    }
    # Alle Abteilung ausgeben
    public static function getAbteilungenRotationsplan(): bool|array
    {
        $sql = "SELECT * FROM b_abteilung WHERE rotationsplan > 0";
        return self::runa($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    public static function badgeStation($id, $color)
    {
        $sql = "SELECT station FROM b_station WHERE id = '$id'";
        $stmt = self::dbr()->prepare($sql);
        $stmt->execute();
        $a = $stmt->fetch(\PDO::FETCH_OBJ)->station;
        echo "<span class='badge badge-$color me-1'>$a</span>";
    }

    # Zeitschiene in Array schreiben
    public static function zs2array()
    {
        $sql = "SELECT DATE_FORMAT(beginn, '%H:%i') AS beginn FROM zeitschiene ORDER BY id";
        $a = self::run($sql)->fetchAll(\PDO::FETCH_OBJ);
        foreach ($a as $name) {
            $_SESSION['beginn'][] = $name->beginn;
        }
        $sql = "SELECT DATE_FORMAT(ende, '%H:%i') AS ende FROM zeitschiene ORDER BY id";
        $b = self::run($sql)->fetchAll(\PDO::FETCH_OBJ);
        foreach ($b as $name) {
            $_SESSION['ende'][] = $name->ende;
        }
    }

    # Stationsid nach Zeitschiene abrufen
    public static function getStationIdPerson($uid, $z)
    {
        $sql = "SELECT sid FROM c_person2station WHERE datum = '{$_SESSION['parameter']['heuteSQL']}' ";
        $sql .= "AND uid = '$uid' AND zeitschiene = '$z'";
        echo $sql;
        $a = self::run($sql)->fetchColumn();
        if (empty($a)) $a = 104;
        return $a;
    }

    # Funktion Personal ausgeben
    public static function getFunktionPersonal($id): string
    {
        return match ($id) {
            default => '&nbsp;',
            1 => 'Teamleiter',
            2 => 'Stelv. Teamleiter',
            3 => 'Qualitätssicherung',
            4 => 'Material',
        };
    }

    # Details zu einer Station abrufen
    public static function getDetailsStation($sid)
    {
        $sql = "SELECT * FROM b_station WHERE id = '$sid'";
        return self::run($sql)->fetch(PDO::FETCH_OBJ);
    }

    # Letzter Einsatz an der Station
    public static function getLastWorkStation($uid, $sid)
    {
        $sql = "SELECT DATE_FORMAT(datum, '%d.%m.%Y') AS tag FROM c_person2station ";
        $sql .= "WHERE datum < '{$_SESSION['parameter']['heuteSQL']}' ";
        $sql .= "AND uid = '$uid' AND ";
        $sql .= "sid = '$sid' ORDER BY datum DESC LIMIT 1";
        return self::run($sql)->fetchColumn();
    }

    # Anzahl der Einsätze des Mitarbeiters an der Station
    public static function countAnzahlEinsatzStation($uid, $sid)
    {
        $sql = "SELECT COUNT(id) FROM c_person2station WHERE uid = '$uid' ";
        $sql .= "AND sid = '$sid'";
        return self::run($sql)->fetchColumn();
    }

    # Anzahl aller Einsätze des Mitarbeiters
    public static function countAnzahlEinsatz($uid)
    {
        $sql = "SELECT COUNT(id) FROM c_person2station WHERE uid = '$uid' ";
        return self::run($sql)->fetchColumn();
    }

    # Zeitraum, über den die Daten erfasst sind (Start/Ende)
    public static function getDatum($order)
    {
        $sql = "SELECT datum FROM c_person2station ORDER BY datum " . $order . " LIMIT 1";
        return self::run($sql)->fetchColumn();
    }

    # Alle Stationen einer Abteilung in zufälliger Reihenfolge
    public static function getStationAbteilungRand()
    {
        $sql = "SELECT * FROM b_station WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND status = '1' ";
        return self::run($sql)->fetchAll(\PDO::FETCH_OBJ);
    }

    # Anzahl Einsätze an der Station im definierten Zeitraum
    public static function countEinsatzStation($sid, $uid, $start = '1970-01-01', $ende = '9999-12-31')
    {
        $sql = "SELECT COUNT(id) FROM c_person2station WHERE sid = '$sid' ";
        $sql .= "AND uid = '$uid' ";
        $sql .= "AND (datum BETWEEN '$start' AND '$ende')";
        return self::run($sql)->fetchColumn();
    }

    # QPS Dokument abrufen
    public static function getQPS($id)
    {
        # Tabelle
        $tabelle = array("", "frontcorner", "kuehler", "motorband", "akl");
        $sql = "SELECT * FROM dokumente WHERE id = '$id'";
        return self::runqm($sql)->fetch(\PDO::FETCH_OBJ);
    }

    # Zeiten in Array schreiben
    public static function zeitschiene2Array()
    {
        $sql = "SELECT DATE_FORMAT(beginn, '%H:%i') AS beginn FROM b_zeitschiene ORDER BY id";
        $stmt = self::dbr()->prepare($sql);
        $stmt->execute();
        $beginn = $stmt->fetchAll(\PDO::FETCH_OBJ);
        foreach ($beginn as $name) {
            $_SESSION['beginn'][] = $name;
        }
        $sql = "SELECT DATE_FORMAT(ende, '%H:%i') AS ende FROM b_zeitschiene ORDER BY id";
        $stmt = self::dbr()->prepare($sql);
        $stmt->execute();
        $beginn = $stmt->fetchAll(\PDO::FETCH_OBJ);
        foreach ($beginn as $name) {
            $_SESSION['ende'][] = $name;
        }
    }

    public static function fillSelectVergleich($ma)
    {
        $e = ''; $f = '';

        if(substr($ma,-1,1) == ","): $a = substr($ma,0, -1); else: $a = $ma; endif;
        $b = explode(",",$a);
        $b = array_unique($b);
        $b = array_slice($b, 0, 3);
        foreach($b as $c):
            $d = self::run("SELECT * FROM b_mitarbeiter WHERE id = '$c'")->fetch(PDO::FETCH_OBJ);
            $e .= $d->name." ".$d->vorname.", ";
            $f .= $d->id.",";
        endforeach;
        $name = substr($e,0, -2);
        $id = substr($f,0, -1);
        echo $name."|".$id;
    }

    public static function getPlanDates($folge)
    {
        $sql = "SELECT datum FROM c_person2station ORDER BY datum ". $folge ." LIMIT 1";
        return self::run($sql)->fetchColumn();
    }

    public static function showWrkParameter()
    {
        return self::getAbteilungRotationsplan($_SESSION['user']['wrk_abteilung'])." ".$_SESSION['text']['h_schicht']." ".$_SESSION['user']['wrk_schicht'];
    }

}