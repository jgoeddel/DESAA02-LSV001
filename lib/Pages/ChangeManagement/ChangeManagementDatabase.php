<?php
/** (c) Joachim Göddel . RLMS */


namespace App\Pages\ChangeManagement;

use App\Formular\Formular;
use App\Functions\Functions;
use App\Pages\Home\IndexDatabase;
use PDO;
use PDOStatement;

class ChangeManagementDatabase extends \App\App\AbstractMVC\AbstractDatabase
{

    function getTable($table)
    {
        return $table;
    }

    # Datenbankzugriff für ChangeManagement Tabelle
    public static function dbcm(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_cm";
        $pdo = new PDO($dataSource, $_SESSION['db']['user'], $_SESSION['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }

    # Datenbankzugriff für Admintabellen
    public static function dba(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_admin";
        $pdo = new PDO($dataSource, $_SESSION['db']['user'], $_SESSION['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }

    # Kurzform Abfrage
    public static function run($sql, $bind = NULL): bool|PDOStatement
    {
        $stmt = self::dbcm()->prepare($sql);
        $stmt->execute($bind);
        return $stmt;
    }

    public static function runa($sql, $bind = NULL): bool|PDOStatement
    {
        $stmt = self::dba()->prepare($sql);
        $stmt->execute($bind);
        return $stmt;
    }


    # Ruft alle Einträge nach Status / Jahr und Location aus der Datenbank ab
    public function getElements($jahr, $status): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%d.%m.%Y') AS datum ";
        $sql .= "FROM base WHERE datum LIKE '$jahr%' ";
        $sql .= "AND status < '$status' ";
        $sql .= "ORDER BY id DESC";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    # Value ändern
    public function changeValue($table, $feld, $value, $bid, $sid)
    {
        if ($sid != '') {
            $sql = "UPDATE " . $table . " SET " . $feld . " = ? WHERE id = '$sid'";
            self::run($sql, [$value]);
        } else {
            $sql = "UPDATE " . $table . " SET " . $feld . " = ? WHERE bid = ?";
            self::run($sql, [$value, $bid]);
        }
        echo $sql;

    }

    # Value ändern
    public function setValue($table, $feld, $value, $bid)
    {
        $sql = "INSERT INTO " . $table . " SET " . $feld . " = ?, bid = ?, name = '{$_SESSION['user']['dbname']}', datum = now()";
        echo $sql;
        self::run($sql, [$value, $bid]);
    }

    # Order ändern
    public function changeOrder($part, $feld, $value, $bid)
    {
        $sql = "SELECT id FROM base2order WHERE bid = ? AND part = ?";
        $a = self::run($sql, [$bid, $part])->fetchColumn();
        if (!empty($a)) {
            $sql = "UPDATE base2order SET " . $feld . " = '$value', mitarbeiter = '{$_SESSION['user']['dbname']}' WHERE bid = ? AND part = ? LIMIT 1";
        } else {
            $sql = "INSERT INTO base2order SET " . $feld . " = '$value', bid = ?, part = ?";
        }
        echo $sql;
        self::run($sql, [$bid, $part]);
    }

    # Verantwortlichen ändern
    public function changeVerantwortung($bid, $uid)
    {
        $sql = "SELECT * FROM b_mitarbeiter WHERE id = ?";
        $a = self::runa($sql, [$uid])->fetch(PDO::FETCH_OBJ);
        $sql = "UPDATE base SET name = '$a->vorname $a->name', mid = ? WHERE id = ? LIMIT 1";
        self::run($sql, [$uid, $bid]);
    }

    # Zieldatum ändern
    public function changeZieldatum($bid, $bemerkung, $zieldatum)
    {
        $sql = "SELECT zieldatum FROM base2info WHERE bid = ?";
        $a = self::run($sql, [$bid])->fetchColumn();
        $sql = "UPDATE base2info SET zieldatum = ?, bemerkung = ? WHERE bid = ? LIMIT 1";
        self::run($sql, [$zieldatum, $bemerkung, $bid]);
        self::setLog('Detailansicht', $bid, '', '0', 'Das Zieldatum wurde von ' . $a . ' auf den ' . $zieldatum . ' eingestellt.<br><b>Begründung:</b> ' . $bemerkung . '', 1);
    }

    # APQP setzen (User)
    public function setAPQP($bid, $apqp, $part, $antwort)
    {
        $status = self::run("SELECT status FROM base WHERE id = '$bid'")->fetchColumn();
        $sql = "SELECT id FROM base2apqp WHERE bid = ? AND apqp = ? AND part = ?";
        $a = self::run($sql, [$bid, $apqp, $part])->fetchColumn();
        if ($antwort == 'nio') $aid = 1;
        if ($antwort == 'no-impact') $aid = 5;
        if ($antwort == 'io') $aid = 9;
        if (!empty($a)) {
            $sql = "UPDATE base2apqp SET name = '{$_SESSION['user']['dbname']}', datum = now(), antwort = ?, aid = '$aid', bemerkung = '' WHERE bid = ? AND apqp = ? AND part = ? LIMIT 1";
        } else {
            $sql = "INSERT INTO base2apqp SET name = '{$_SESSION['user']['dbname']}', datum = now(), antwort = ?, aid = '$aid', bid = ?, apqp = ?, part = ?";
        }
        $prt = ($part == 1) ? 'Evaluation' : 'Tracking';
        self::run($sql, [$antwort, $bid, $apqp, $part]);
        self::setLog('' . $prt . ' bearbeitet', $bid, '' . $part . '', $apqp, '' . $prt . ' dieses Punktes mit ' . $antwort . ' bewertet.', 1);
        # Status ändern, wenn der erste Punkt der Evaluation bearbeitet wurde
        $anz = self::run("SELECT COUNT(id) FROM base2apqp WHERE bid = '$bid' AND aid > 0 AND part = '1'")->fetchColumn();
        if($anz == 1){
            self::run("UPDATE base SET status = '2' WHERE id = '$bid' LIMIT 1");
        }
        # Status ändern, wenn alle Punkte der Evaluation bearbeitet wurden
        if(self::countOpenEvaluation($bid,1) == 0 && $status < 3){
            $sql = "UPDATE base SET status = '3' WHERE id = '$bid' LIMIT 1";
            self::run($sql);
        }
        # Auftrag beenden, wenn alle Punkte erfüllt sind
        if (self::checkAuftrag($bid) == 0) self::finishID($bid);
    }

    # GMAS abrufen
    public function getGMAS($bid): bool|array
    {
        $sql = "SELECT * FROM base2gmas WHERE bid = ?";
        return self::run($sql, [$bid])->fetchAll(PDO::FETCH_OBJ);
    }

    # NAEL abrufen
    public function getNAEL($bid): bool|array
    {
        $sql = "SELECT * FROM base2nael WHERE bid = ?";
        return self::run($sql, [$bid])->fetchAll(PDO::FETCH_OBJ);
    }

    # PART NO abrufen
    public function getPartNo($bid, $part): bool|array
    {
        $sql = "SELECT * FROM base2" . $part . " WHERE bid = ?";
        return self::run($sql, [$bid])->fetchAll(PDO::FETCH_OBJ);
    }

    # Part No eintragen
    public function setPartno($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }

        $sql = "INSERT INTO base2partno SET anlage = '$anlage', ";
        $sql .= "bezeichnung = '$bezeichnung', ";
        $sql .= "alt = '$alt', ";
        $sql .= "neu = '$neu', ";
        $sql .= "doppelsnr = '$doppelsnr', ";
        $sql .= "lid = '$lid', ";
        if ($ziel == '') {
            $sql .= "ziel = NULL, ";
        } else {
            $sql .= "ziel = '$ziel', ";
        }
        $sql .= "ist = NULL, ";
        $sql .= "bid = '$bid', ";
        $sql .= "status = '0', ";
        $sql .= "name = '{$_SESSION['user']['dbname']}', ";
        $sql .= "citycode = '{$_SESSION['wrk']['citycode']}', ";
        $sql .= "datum = now()";

        self::run($sql);
    }

    # Part No ändern
    public function changePartno($id)
    {
        $sql = "UPDATE base2partno SET ist = now(), status = '1' WHERE id = ? LIMIT 1";
        self::run($sql, [$id]);
    }

    # Part No löschen
    public function deletePartno($id)
    {
        $sql = "DELETE FROM base2partno WHERE id = ? LIMIT 1";
        self::run($sql, [$id]);
    }

    # Partno gruppiert
    public function getPartNoGrp($citycode): bool|array
    {
        $sql = "SELECT bezeichnung FROM base2partno WHERE citycode = ? GROUP BY bezeichnung ORDER BY bezeichnung";
        return self::run($sql, [$citycode])->fetchAll(PDO::FETCH_OBJ);
    }

    # Lops abrufen
    public function getLops($id): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%d.%m.%Y') AS eintrag, ";
        $sql .= "DATE_FORMAT(due_date, '%d.%m.%Y') AS due ";
        $sql .= "FROM base2lop WHERE bid = ? ORDER BY id";
        return self::run($sql, [$id])->fetchAll(PDO::FETCH_OBJ);
    }

    # Lop speichern
    public function setLop($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        $sql = "INSERT INTO base2lop SET bid = ?, ";
        $sql .= "datum = ?, ";
        $sql .= "trg = ?, ";
        $sql .= "area = ?, ";
        $sql .= "open_issue = ?, ";
        $sql .= "action = ?, ";
        $sql .= "due_date = ?, ";
        $sql .= "resp = ?, ";
        $sql .= "support = ?, ";
        $sql .= "ersteller = '{$_SESSION['user']['dbname']}', ";
        $sql .= "edatum = now()";
        self::run($sql, [$id, $datum, $trg, $area, $issue, $action, $due_date, $resp, $support]);
    }

    public static function statusLOP($id, $start, $ende, $status)
    {
        global $dbc;
        $sql = "SELECT DATEDIFF('$ende','$start') AS diff FROM base2lop WHERE id = ?";
        $a = self::run($sql, [$id])->fetchColumn();
        if ($status < 9 && $status != 1):
            switch ($a):
                case ($a <= 0):
                    echo '<i class="fa fa-circle text-danger"></i>';
                    break;
                case ($a > 0 && $a < 7):
                    echo '<i class="fa fa-circle text-warning"></i>';
                    break;
                case ($a > 7):
                    echo '<i class="fa fa-circle text-success"></i>';
                    break;
            endswitch;
        endif;
        if ($status == 1):
            echo '<i class="fa fa-times text-muted"></i>';
        endif;
        if ($status == 9):
            echo '<i class="fa fa-lock text-primary"></i>';
        endif;
    }

    # Lop ändern
    public function changeLop($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        if ($status == 5) {
            $sql = "DELETE FROM base2lop WHERE id = '$id' LIMIT 1";
        } else {
            $sql = "UPDATE base2lop SET status = '$status' WHERE id = '$id' LIMIT 1";
        }
        self::run($sql);
    }

    # Meetings abrufen
    public static function getMeetingsGrp($id): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%d.%m.%Y') AS tag FROM meeting WHERE bid = ? GROUP BY datum ORDER BY datum DESC";
        return self::run($sql, [$id])->fetchAll(PDO::FETCH_OBJ);
    }

    # Meetings abrufen
    public static function getMeetings($id, $datum): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%d.%m.%Y') AS tag FROM meeting WHERE bid = ? AND datum = ? ORDER BY datum";
        return self::run($sql, [$id, $datum])->fetchAll(PDO::FETCH_OBJ);
    }

    # Meeting speichern
    public function setMeeting($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        $eintrag = str_replace("<br></p>", "</p>", $eintrag);
        $sql = "INSERT INTO meeting SET bid = '$id', ";
        $sql .= "eintrag = '$eintrag', ";
        $sql .= "name = '{$_SESSION['user']['dbname']}', ";
        $sql .= "datum = now(), zeit = now(), ";
        $sql .= "mid = '{$_SESSION['user']['id']}'";
        self::run($sql);
    }

    # Meeting speichern
    public function setMaMeeting($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        if (empty(self::checkMaMeeting($mid, $uid))) {
            $sql = "INSERT INTO meeting2user SET mid = '$mid', uid = '$uid', name = '{$_SESSION['user']['dbname']}', datum = now()";
        } else {
            $sql = "DELETE FROM meeting2user WHERE mid = '$mid' AND uid = '$uid' LIMIT 1";
        }
        self::run($sql);
    }


    # Mitarbeiter im Meeting
    public static function checkMaMeeting($mid, $uid)
    {
        $sql = "SELECT id FROM meeting2user WHERE mid = ? AND uid = ?";
        return self::run($sql, [$mid, $uid])->fetchColumn();
    }


    # Alle Mitarbeiter im Meeting
    public static function getMaMeeting($mid)
    {
        $sql = "SELECT id,uid FROM meeting2user WHERE mid = ?";
        return self::run($sql, [$mid])->fetchAll(PDO::FETCH_OBJ);
    }



    # ANGEBOTE - - - - - - - - - - - - - - - - - - - - - - - - -
    # Prüfe die Berechtigung des Mitarbeiters
    public static function checkMaAngebot($mid, $bid)
    {
        $sql = "SELECT * FROM base2angebot WHERE mid = '$mid' AND bid = '$bid'";
        return self::run($sql)->fetch(PDO::FETCH_OBJ);
    }
    # Berechtigung für einen Mitarbeiter hinzufügen oder löschen
    public function setMaAngebot($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        if (empty(self::checkMaAngebot($mid, $bid))) {
            $sql = "INSERT INTO base2angebot SET mid = '$mid', ";
            $sql.= "bid = '$bid', ";
            $sql.= "mitarbeiter = '{$_SESSION['user']['dbname']}', ";
            $sql.= "datum = now(), ";
            $sql.= "aread = '1'";
        } else {
            $sql = "DELETE FROM base2angebot WHERE mid = '$mid' AND bid = '$bid' LIMIT 1";
        }
        self::run($sql);
    }
    # Alle Mitarbeiter mit Berechtigungen abrufen
    public function getMaAngebot($bid)
    {
        $sql = "SELECT * FROM base2angebot WHERE bid = '$bid'";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }
    # Alle Dateien zählen (nach Bereich)
    public static function countFiles($bid,$bereich)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM base2files WHERE bid = '$bid' AND part = '$bereich'";
        return self::run($sql)->fetchColumn();
    }



    # Quelle abrufen
    public static function getQuelle(): bool|array
    {
        $sql = "SELECT * FROM quelle ORDER BY id";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    # Change Type abrufen
    public static function getChangeType(): bool|array
    {
        $sql = "SELECT * FROM changetype ORDER BY id";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    # Abweichung abrufen
    public function getDeviation($bid)
    {
        $sql = "SELECT deviation FROM base2deviation WHERE bid = ?";
        return self::run($sql, [$bid])->fetchColumn();
    }

    # Status abrufen
    public function getStatus($bid)
    {
        $sql = "SELECT status FROM base WHERE bid = ?";
        return self::run($sql, [$bid])->fetchColumn();
    }

    # Vereinfachter Durchlauf
    public function simpleChange($bid, $antwort, $password)
    {
        $pepper = '%Wa5manhalts01nd13Dat3nbank5chr31bt?';
        $row = self::runa("SELECT * FROM b_mitarbeiter WHERE username = '{$_SESSION['user']['username']}'")->fetch(PDO::FETCH_OBJ);
        $valid = password_verify($password, $row->password);
        if ($valid === true) {
            # Antwort
            if ($antwort == 'nio') $aid = 1;
            if ($antwort == 'no-impact') $aid = 5;
            if ($antwort == 'io') $aid = 9;
            # Abfrage
            $sql = "SELECT id,antwort,apqp FROM base2apqp WHERE bid = ? AND part = '1'";
            $a = self::run($sql, [$bid])->fetchAll(PDO::FETCH_OBJ);
            foreach ($a as $row) {
                if (empty($row->antwort)) {
                    $sql = "UPDATE base2apqp SET name = '{$_SESSION['user']['dbname']}', datum = now(), antwort = ?, aid = '$aid', bemerkung = '{$_SESSION['text']['h_durchlauf']}' WHERE bid = ? AND part = '1' AND apqp = '$row->apqp' LIMIT 1";
                    self::run($sql, [$antwort, $bid]);
                    self::setLog('Evaluation bearbeitet', $bid, '1', $row->apqp, 'Evaluatiion dieses Punktes mit ' . $antwort . ' bewertet.', 1);
                }
            }
            self::run("UPDATE base SET status = '3' WHERE id = ? LIMIT 1", [$bid]);
            # Auftrag beenden, wenn alle Punkte erfüllt sind
            if (self::checkAuftrag($bid) == 0) self::finishID($bid);
            self::setLog('Evaluation', $bid, '', '0', 'Vereinfachten Durchlauf durchgeführt', 1);
            echo 1;
        }
    }

    # Kommentar speichern
    public function setComAPQP($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        $log = str_replace("<br></p>", "</p>", $log);
        $log = str_replace("<p></p>", "", $log);
        $log = str_replace("&nbsp;", "", $log);
        $log = trim($log);
        $sql = "INSERT INTO kommentar SET bid = '$bid', ";
        $sql .= "part = '$part', ";
        $sql .= "kommentar = '$log', ";
        $sql .= "bereich = '$bereich', ";
        $sql .= "name = '{$_SESSION['user']['dbname']}', ";
        $sql .= "datum = now(), ";
        $sql .= "frage_an = '$frage_an', ";
        $sql .= "mid = '{$_SESSION['user']['id']}'";
        self::run($sql);
    }

    # Antwort Kommentar speichern
    public function setAntwortCom($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        $sql = "INSERT INTO antwort SET bid = '$bid', ";
        $sql .= "fid = '$fid', ";
        $sql .= "kommentar = '$kommentar', ";
        $sql .= "name = '{$_SESSION['user']['dbname']}', ";
        $sql .= "datum = now(), ";
        $sql .= "mid = '{$_SESSION['user']['id']}'";
        self::run($sql);
    }

    # Freigabe speichern
    public function setFreigabe($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        $row = self::runa("SELECT * FROM b_mitarbeiter WHERE username = '{$_SESSION['user']['username']}'")->fetch(PDO::FETCH_OBJ);
        $valid = password_verify($password, $row->password);
        if ($valid === true) {
            $sql = "UPDATE base SET status = ? WHERE id = ? LIMIT 1";
            self::run($sql, [$antwort, $id]);
            $ant = ($antwort == 4) ? 'abgelehnt' : 'freigegeben';
            self::setLog('Aktion', $id, '', '0', 'Die weitere Bearbeitung der Anfrage wurde ' . $ant . '.', 1);
        } else {
            echo "Fehler";
        }
    }

    # Abschliessen
    public function abschliessen($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        $row = self::runa("SELECT * FROM b_mitarbeiter WHERE username = '{$_SESSION['user']['username']}'")->fetch(PDO::FETCH_OBJ);
        $valid = password_verify($password, $row->password);
        if ($valid === true) {
            $sql = "UPDATE base SET status = '7' WHERE id = ? LIMIT 1";
            self::run($sql, [$id]);
            self::setLog('Aktion', $id, '', '0', 'Die Anfrage wurde abgeschlossen', 1);
        } else {
            echo "Fehler";
        }
    }

    # Löschen
    public function delete($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        $row = self::runa("SELECT * FROM b_mitarbeiter WHERE username = '{$_SESSION['user']['username']}'")->fetch(PDO::FETCH_OBJ);
        $valid = password_verify($password, $row->password);
        if ($valid === true) {
            $sql = "UPDATE base SET status = '8' WHERE id = ? LIMIT 1";
            self::run($sql, [$id]);
            self::setLog('Aktion', $id, '', '0', 'Die Anfrage wurde gelöscht', 1);
        } else {
            echo "Fehler";
        }
    }
    # Archivieren
    public function archivieren($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        $row = self::runa("SELECT * FROM b_mitarbeiter WHERE username = '{$_SESSION['user']['username']}'")->fetch(PDO::FETCH_OBJ);
        $valid = password_verify($password, $row->password);
        if ($valid === true) {
            $sql = "UPDATE base SET status = '9' WHERE id = ? LIMIT 1";
            self::run($sql, [$id]);
            self::setLog('Aktion', $id, '', '0', 'Die Anfrage wurde archiviert', 1);
        } else {
            echo "Fehler";
        }
    }

    # Aktionen abrufen
    public function getAktionen($bid): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%d.%m.%Y %H:%i') AS eintrag FROM logfile WHERE bid = ? AND seite = 'Aktion' ORDER BY datum";
        return self::run($sql, [$bid])->fetchAll(PDO::FETCH_OBJ);
    }

    # Nachrichten abrufen
    public function getComBase($bid): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%d.%m.%Y %H:%i') AS am FROM base2log WHERE bid = ? ORDER BY datum";
        return self::run($sql, [$bid])->fetchAll(PDO::FETCH_OBJ);
    }

    # Nachrichten speichern
    public function setNachricht($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        $sql = "INSERT INTO base2log SET bid = ?, ";
        $sql .= "log = ?, ";
        $sql .= "name = '{$_SESSION['user']['dbname']}', ";
        $sql .= "datum = now(), ";
        $sql .= "mid = '{$_SESSION['user']['id']}'";
        self::run($sql, [$bid, $log]);
    }

    # Dateien abrufen
    public function getDateien($bid, $part): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%d.%m.%Y') as tag, DATE_FORMAT(datum, '%H:%i') as zeit FROM base2files WHERE bid = ? AND part = ?";
        return self::run($sql, [$bid, $part])->fetchAll(PDO::FETCH_OBJ);
    }

    # Partno abrufen
    public function getPartNumbers($bid): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(ziel, '%d.%m.%Y') AS zieldatum, DATE_FORMAT(ist, '%d.%m.%Y') AS istdatum FROM base2partno WHERE bid = ?";
        return self::run($sql, [$bid])->fetchAll(PDO::FETCH_OBJ);
    }


    # Einen Eintrag abrufen
    public static function getElement($id)
    {
        return self::run("SELECT *, DATE_FORMAT(datum, '%d.%m.%Y') AS tag FROM base WHERE id = ?", [$id])->fetch(PDO::FETCH_OBJ);
    }

    # Informationen zu ID abrufen
    public static function getInfo($bid)
    {
        $sql = "SELECT *, DATE_FORMAT(zieldatum, '%d.%m.%Y') AS ziel ";
        $sql .= "FROM base2info ";
        $sql .= "WHERE bid = ?";
        return self::run($sql, [$bid])->fetch(PDO::FETCH_OBJ);
    }

    # Informationen zur Location abrufen
    public static function getLocationInfo($citycode)
    {
        $sql = "SELECT location, citycode, cur ";
        $sql .= "FROM location ";
        $sql .= "WHERE citycode = ?";
        return self::run($sql, [$citycode])->fetch(PDO::FETCH_OBJ);
    }

    # Gab es eine Änderung seit dem letzten Besuch
    public static function getCMViewDate($bid)
    {
        $sql = "SELECT DATE_FORMAT(datum, '%Y-%m-%d') AS datum ";
        $sql .= "FROM view ";
        $sql .= "WHERE name = '{$_SESSION['user']['dbname']}' ";
        $sql .= "AND bid = ?";
        return self::run($sql, [$bid])->fetchColumn();
    }
    # Gesamtstatus eines Elementes ermitteln
    # Zugehörige Funktionen
    public static function getCMParts($bid, $part, $status, $nr): array
    {
        $sql = "SELECT " . $part . " FROM base2part WHERE bid = ?";
        $evaluation = self::run($sql, [$bid])->fetchColumn();
        $part = ($part == 'evaluation') ? 1 : 2;
        $sql = "SELECT COUNT(id) AS result FROM base2apqp WHERE bid = ? AND part = ?";
        $a = self::run($sql, [$bid, $nr])->fetchColumn();
        $sql = "SELECT COUNT(id) AS result FROM base2apqp WHERE bid = ? AND part = ? AND antwort != ''";
        $b = self::run($sql, [$bid, $nr])->fetchColumn();
        $sql = "SELECT COUNT(id) AS result FROM base2apqp WHERE bid = ? AND aid = ? AND part = ?";
        $c = self::run($sql, [$bid, $status, $nr])->fetchColumn();
        return array($evaluation, $a, $b, $c);
    }

    # APQP mit nio
    public static function getNIO($bid, $part)
    {
        $sql = "SELECT COUNT(id) FROM base2apqp WHERE bid = ? AND part = ? AND aid = '1'";
        return self::run($sql, [$bid, $part])->fetchColumn();
    }

    public static function dspCMOverStatus($g, $h, $i)
    {
        // Ausgabe
        if ($i > 0):
            echo '<i class="fa fa-frown text-danger"></i>';
        else:
            if ($h == 0):
                echo '<i class="fa fa-circle text-muted"></i>';
            else:
                if ($h == $g):
                    echo '<i class="fa fa-smile text-success"></i>';
                else:
                    echo '<i class="fa fa-meh text-warning"></i>';
                endif;
            endif;
        endif;
    }

    # Änderungsart ausgeben
    public static function dspCMArt($bid)
    {
        $sql = "SELECT art FROM base WHERE id = ?";
        $art = self::run($sql, [$bid])->fetchColumn();
        if ($art == 1):
            echo '<span class="badge badge-warning font-weight-400 oswald me-3">' . strtoupper($_SESSION['text']['h_vorabpruefung']) . '</span>';
        else:
            echo '<span class="badge badge-primary font-weight-400 oswald me-3">' . strtoupper($_SESSION['text']['h_bewertung']) . '</span>';
        endif;
    }

    # Wurde eine GMAS zu der Änderung eingetragen
    public static function base2gmas($bid)
    {
        $sql = "SELECT * FROM base2gmas WHERE bid = ?";
        return self::run($sql, [$bid])->fetch(PDO::FETCH_OBJ);
    }

    # Zählt Einträge im Logbuch
    public static function countCMElements($bid, $table)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM " . $table . " WHERE bid = ?";
        return self::run($sql, [$bid])->fetchColumn();
    }

    # Prüfen, ob die Evaluierung erforderlich ist oder nicht
    public static function checkEvaluation($bid)
    {
        $sql = "SELECT evaluation FROM base2part WHERE bid = ?";
        return self::run($sql, [$bid])->fetchColumn();
    }

    # Evaluation Elemente zählen (Alle)
    public static function countAPQPElements($bid, $table)
    {
        $sql = "SELECT COUNT(id) FROM " . $table . " WHERE bid = ?";
        return self::run($sql, [$bid])->fetchColumn();
    }

    # Prüfen, ob die Evaluierung erforderlich ist oder nicht
    public static function checkTracking($bid)
    {
        $sql = "SELECT tracking FROM base2part WHERE bid = ?";
        return self::run($sql, [$bid])->fetchColumn();
    }

    # Alle erforderlichen Elemente der Evaluation
    public static function pla2evaluation($bid): bool|array
    {
        $sql = "SELECT * FROM pla2evaluation WHERE bid = ?";
        return self::run($sql, [$bid])->fetchAll(PDO::FETCH_OBJ);
    }

    # Alle erforderlichen Elemente des Trackings
    public static function imp2tracking($bid): bool|array
    {
        $sql = "SELECT * FROM imp2tracking WHERE bid = ?";
        return self::run($sql, [$bid])->fetchAll(PDO::FETCH_OBJ);
    }

    # Evaluation Status
    public static function dspEvaluationStatus($bid, $bereich, $part)
    {
        $color = 'text-muted';
        $sql = "SELECT COUNT(id) AS ergebnis FROM base2apqp ";
        $sql .= "WHERE aid IS NOT NULL ";
        $sql .= "AND bid = ? ";
        $sql .= "AND part = ? ";
        $sql .= "AND bereich = ?";
        # Bearbeitung gestartet
        $a = self::run($sql, [$bid, $part, $bereich])->fetchColumn();
        if ($a > 0) $color = 'text-warning';

        $sql = "SELECT COUNT(id) AS ergebnis FROM base2apqp ";
        $sql .= "WHERE bid = ? ";
        $sql .= "AND part = ? ";
        $sql .= "AND bereich = ?";
        # Gesamt
        $b = self::run($sql, [$bid, $part, $bereich])->fetchColumn();
        if ($a == $b && $a > 0) $color = 'text-success';

        $sql = "SELECT COUNT(id) AS ergebnis FROM base2apqp ";
        $sql .= "WHERE aid = '1' ";
        $sql .= "AND bid = ? ";
        $sql .= "AND part = ? ";
        $sql .= "AND bereich = ?";
        # NIO
        $c = self::run($sql, [$bid, $part, $bereich])->fetchColumn();
        if ($c > 0):
            $color = 'text-danger';
            $warning = '<i class="fa fa-exclamation-triangle text-danger ps-2"></i>';
        endif;
        echo '<span class="' . $color . '">' . $bereich . '</span>';
    }


    # Dauer der Änderung
    public static function getWrkTime($bid): array
    {
        # Ausgabe
        $ausgabe = array();
        # Jetzt
        $jetzt = DATE('Y-m-d H:i:s');
        $start = self::run("SELECT datum FROM base2info WHERE bid = ?", [$bid])->fetchColumn();
        $ende = self::run("SELECT ende FROM base2info WHERE bid = ?", [$bid])->fetchColumn();
        # Wenn ein Enddatum gesetzt ist muss das übernommen werden
        if (!empty($ende)) $jetzt = $ende;
        $sql = "SELECT FLOOR(HOUR(TIMEDIFF('$jetzt','$start')) / 24) FROM base2info WHERE bid = ?";
        $tage = self::run($sql, [$bid])->fetchColumn();
        $tage = $tage * 1;
        $sql = "SELECT CONCAT(FLOOR(HOUR(TIMEDIFF('$jetzt','$start')) / 24), ' <small>Tag(e)</small>, ', ";
        $sql .= "MOD(HOUR(TIMEDIFF('$jetzt','$start')), 24), ' <small>Stunde(n)</small>, ', ";
        $sql .= "MINUTE(TIMEDIFF('$jetzt','$start')), ' <small>Minute(n)</small>') AS TimeDiff ";
        $sql .= "FROM base2info WHERE bid = ?";
        $ausgabe[0] = self::run($sql, [$bid])->fetchColumn();
        if (!empty($ende)):
            $ausgabe[1] = '<span class="badge badge-primary font-weight-400 ms-2">' . $_SESSION['text']['h_close'] . '</span>';
        else:
            if ($tage <= 14):
                $ausgabe[1] = '<span class="badge badge-success font-weight-400 ms-2">' . $_SESSION['text']['h_inTime'] . '</span>';
            endif;
            if ($tage > 14 && $tage <= 21):
                $ausgabe[1] = '<span class="badge badge-warning font-weight-400 ms-2">' . $_SESSION['text']['h_critical'] . '</span>';
            endif;
            if ($tage > 21):
                $ausgabe[1] = '<span class="badge badge-danger font-weight-400 ms-2">' . $_SESSION['text']['h_outOfTime'] . '</span>';
            endif;
        endif;
        return $ausgabe;
    }

    # Ersteller / Verantwortlichen eines Eintrages
    public static function getErsteller($bid)
    {
        $sql = "SELECT mid FROM base WHERE id = ?";
        $mid = self::run($sql, [$bid])->fetchColumn();
        $sql = "SELECT * FROM b_mitarbeiter WHERE id = ?";
        return self::runa($sql, [$mid])->fetch(PDO::FETCH_OBJ);
    }

    # Implement Date
    public static function dspImplementDate($bid, $table)
    {
        $sql = "SELECT implement_date FROM " . $table . " WHERE bid = ?";
        $a = self::run($sql, [$bid])->fetchColumn();
        return (empty($a)) ? '-' : $a;
    }

    # Orderfeld
    public static function dspOrderFeld($bid, $part, $feld)
    {
        $sql = "SELECT " . $feld . " FROM base2order WHERE bid = ? AND part = ?";
        $a = self::run($sql, [$bid, $part])->fetchColumn();
        return (empty($a)) ? '-' : $a;
    }

    # Art der Kundenänderung
    public static function dspArt($bid)
    {
        $sql = "SELECT art FROM base WHERE id = ?";
        $a = self::run($sql, [$bid])->fetchColumn();
        if ($a == 1) {
            echo '<span class="badge badge-warning font-weight-400 oswald font-size-20">' . strtoupper($_SESSION['text']['h_vorabpruefung']) . '</span>';
        } else {
            echo '<span class="badge badge-primary font-weight-400 oswald font-size-20">' . strtoupper($_SESSION['text']['h_bewertung']) . '</span>';
        }
    }

    # Status ausgeben
    public static function dspStatus($id)
    {
        # ID
        if ($id == 0) $id = 1;
        $sql = "SELECT status FROM status WHERE id = ?";
        $a = self::run($sql, [$id])->fetchColumn();
        echo $_SESSION['text']['' . $a . ''];
    }

    # Evaluation / Tracking prüfen
    public static function checkPart($bid, $part)
    {
        $sql = "SELECT " . $part . " FROM base2part WHERE bid = ?";
        return self::run($sql, [$bid])->fetchColumn();
    }
    # Evaluation / Tracking zählen (nach Status)

    # Kosten eines Bereichs ermitteln
    public static function summeKosten($bereich, $part, $bid)
    {
        $summe = 0;
        $sql = "SELECT * FROM base2apqp WHERE bid = ? AND bereich = ? AND part = ?";
        $a = self::run($sql, [$bid, $bereich, $part])->fetchAll(PDO::FETCH_OBJ);
        if (!empty($a)) {
            foreach ($a as $b) {
                $sql = "SELECT kosten FROM apqp2cost WHERE base_apqp = '$b->id'";
                $c = self::run($sql)->fetchColumn();
                if (!empty($c)) $summe = $summe + $c;
            }
        }
        return $summe;
    }

    # Anmerkungen in einem Bereich zählen
    public static function summeAnmerkungenAPQP($bereich, $part, $bid)
    {
        $sql = "SELECT COUNT(id) FROM base2apqp WHERE bid = ? AND part = ? AND bemerkung != '' AND bereich = ?";
        $a = self::run($sql, [$bid, $part, $bereich])->fetchColumn();
        $sql = "SELECT COUNT(id) FROM kommentar WHERE bid = ? AND part = ? AND kommentar != '' AND bereich = ?";
        $b = self::run($sql, [$bid, $part, $bereich])->fetchColumn();
        return $a + $b;
    }

    # Anzeige der gesamten und bereits erledigten Punkte
    public static function dspAnzAPQP($part, $bereich, $bid)
    {
        $sql = "SELECT COUNT(id) FROM base2apqp WHERE bid = ? AND part = ? AND bereich = ?";
        $a = self::run($sql, [$bid, $part, $bereich])->fetchColumn();
        $sql = "SELECT COUNT(id) FROM base2apqp WHERE bid = ? AND part = ? AND bereich = ? AND antwort != ''";
        $b = self::run($sql, [$bid, $part, $bereich])->fetchColumn();
        echo $b . " / " . $a;
    }

    # Summe, Erledigt und Fehler eines Bereis (APQP)
    public static function sefAPQP($part, $bereich, $bid, $antwort)
    {
        if ($antwort == '') $sql = "SELECT COUNT(id) FROM base2apqp WHERE bid = ? AND part = ? AND bereich = ?";
        if ($antwort == 0) $sql = "SELECT COUNT(id) FROM base2apqp WHERE bid = ? AND part = ? AND bereich = ? AND antwort != ''";
        if ($antwort == 1) $sql = "SELECT COUNT(id) FROM base2apqp WHERE bid = ? AND part = ? AND bereich = ? AND antwort = 'nio'";
        return self::run($sql, [$bid, $part, $bereich])->fetchColumn();
    }

    # Ausgabe des Status eines Bereichs (APQP)
    public static function dspStatusEvaluation($part, $bid, $bereich)
    {
        $punkte = self::sefAPQP($part, $bereich, $bid, '');
        $erledigt = self::sefAPQP($part, $bereich, $bid, 0);
        $fehler = self::sefAPQP($part, $bereich, $bid, 1);
        if ($punkte == $erledigt && $fehler == 0) echo '<i class="fa fa-smile text-success"></i>';
        if ($punkte == $erledigt && $fehler > 0) echo '<i class="fa fa-frown text-danger"></i>';
        if ($punkte > $erledigt && $fehler > 0) echo '<i class="fa fa-frown text-danger"></i>';
        if ($punkte > $erledigt && $fehler == 0) echo '<i class="fa fa-circle text-muted"></i>';
    }

    # Anzahl noch nicht bearbeiteter Punkte über alle Bereiche
    public static function countOpenEvaluation($bid, $part)
    {
        $sql = "SELECT COUNT(id) FROM base2apqp WHERE bid = '$bid' AND antwort IS NULL AND part = ?";
        return self::run($sql, [$part])->fetchColumn();
    }

    # Alle APQP Elemente einer Anfrage in einem Bereich
    public static function getAllApqpBereich($bid, $part, $bereich): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%d.%m.%Y') as tag, ";
        $sql .= "DATE_FORMAT(datum, '%H:%i:%s') as zeit FROM base2apqp ";
        $sql .= "WHERE bid = ? AND part = ? AND bereich = ?";
        return self::run($sql, [$bid, $part, $bereich])->fetchAll(PDO::FETCH_OBJ);
    }

    # Bereich ausschreiben
    public static function dspBereich($kurz)
    {
        $sql = "SELECT id FROM b_abteilung WHERE kurz = ?";
        $id = self::runa($sql, [$kurz])->fetchColumn();
        echo $_SESSION['text']['abt_' . $id . ''];
    }

    # Antwort APQP
    public static function dspAntwortAPQP($antwort,$part)
    {
        # Antworten umschreiben
        if($part == 2){
            $tio = $_SESSION['text']['ja'];
            $tnio = $_SESSION['text']['nein'];
            $tnoimpact = $_SESSION['text']['t_nichtErforderlich'];
        } else {
            $tio = $_SESSION['text']['io'];
            $tnio = $_SESSION['text']['nio'];
            $tnoimpact = $_SESSION['text']['t_noImpact'];
        }
        switch ($antwort) {
            case "nio":
                echo '<span class="badge  badge-danger">' . $tnio . '</span>';
                break;
            case "io":
                echo '<span class="badge  badge-info">' . $tio . '</span>';
                break;
            case "no-impact":
                echo '<span class="badge  badge-info">' . $tnoimpact . '</span>';
                break;
        }
    }

    # Antwort APQP
    public static function antwortAPQP($apqp, $bid, $part)
    {
        $sql = "SELECT * FROM base2apqp WHERE apqp = ? AND bid = ? AND part = ?";
        return self::run($sql, [$apqp, $bid, $part])->fetch(PDO::FETCH_OBJ);
    }

    # Reset APQP
    public static function resetAPQP($base_apqp)
    {
        $sql = "UPDATE base2apqp SET name = NULL, ";
        $sql .= "datum = NULL, ";
        $sql .= "aid = NULL, ";
        $sql .= "antwort = NULL, ";
        $sql .= "bemerkung = NULL ";
        $sql .= "WHERE id = ? LIMIT 1";
        self::run($sql, [$base_apqp]);
        $sql = "DELETE FROM apqp2cost WHERE base_apqp = ? LIMIT 1";
        self::run($sql, [$base_apqp]);
    }

    # APQP Kosten
    public static function checkAPQPKosten($base_apqp)
    {
        $sql = "SELECT id FROM apqp2cost WHERE base_apqp = ?";
        return self::run($sql, [$base_apqp])->fetchColumn();
    }

    # Reset APQP
    public static function changeAPQP($base_apqp, $bemerkung, $kosten, $anmerkung, $bid)
    {
        # Bemerkung
        if ($bemerkung != '') {
            $bemerkung = str_replace("<p>", "", $bemerkung);
            $bemerkung = str_replace("</p>", "", $bemerkung);
            $bemerkung = trim($bemerkung);
            $anmerkung = trim($anmerkung);
            $bemerkung = "<p>" . $bemerkung . "</p>";
            // Basiseintrag ändern
            $sql = "UPDATE base2apqp SET name = '{$_SESSION['user']['dbname']}', ";
            $sql .= "datum = now(), ";
            $sql .= "bemerkung = ? ";
            $sql .= "WHERE apqp = ? AND bid = ? LIMIT 1";
            self::run($sql, [$bemerkung, $base_apqp, $bid]);
        }
        // Kosten eintragen oder ändern
        if (!empty($kosten) && $kosten > 0):
            $iskosten = self::checkAPQPKosten($base_apqp);
            if (!empty($iskosten)):
                $sql = "UPDATE apqp2cost SET kosten = ?, ";
                $sql .= "anmerkung = ?, ";
                $sql .= "name = '{$_SESSION['user']['dbname']}', ";
                $sql .= "datum = now() ";
                $sql .= "WHERE base_apqp = ? AND bid = ? LIMIT 1";
                self::run($sql, [$kosten, $anmerkung, $base_apqp, $bid]);
            else:
                $sql = "INSERT INTO apqp2cost SET kosten = ?,";
                $sql .= "base_apqp = ?, ";
                $sql .= "anmerkung = ?, ";
                $sql .= "bid = ?, ";
                $sql .= "name = '{$_SESSION['user']['dbname']}', ";
                $sql .= "datum = now() ";
                self::run($sql, [$kosten, $base_apqp, $anmerkung, $bid]);
            endif;
        endif;
    }

    # ID APQP
    public static function idAPQP($apqp, $bid, $part)
    {
        $sql = "SELECT id FROM base2apqp WHERE apqp = ? AND bid = ? AND part = ?";
        return self::run($sql, [$apqp, $bid, $part])->fetchColumn();
    }

    # Anmerkung zu APQP schreiben
    public function setBemerkungAPQP($bemerkung, $apqpid)
    {
        $sql = "UPDATE base2apqp SET bemerkung = ? WHERE id = '$apqpid' LIMIT 1";
        self::run($sql, [$bemerkung]);
    }

    # Kosten APQP
    public static function costAPQP($base_apqp,$bid)
    {
        $sql = "SELECT * FROM apqp2cost WHERE base_apqp = ? AND bid = ?";
        return self::run($sql, [$base_apqp,$bid])->fetch(PDO::FETCH_OBJ);
    }

    # Status prüfen
    public static function checkAuftrag($id)
    {
        # Parameter setzen
        $openEvaluation = 0;
        $openTracking = 0;
        # Evaluation erforderlich ?
        $isEvaluation = self::checkPart($id, 'evaluation');
        if ($isEvaluation):
            # Evaluation: alle Punkte erfüllt ?
            $openEvaluation = self::countOpenEvaluation($id, 1);
        endif;
        # Tracking erforderlich ?
        $isTracking = self::checkPart($id, 'tracking');
        # Tracking: alle Punkte erfüllt ?
        if ($isTracking):
            # Evaluation: alle Punkte erfüllt ?
            $openTracking = self::countOpenEvaluation($id, 2);
        endif;
        return $openTracking + $openEvaluation;
    }

    # Status nach Tabelle prüfen
    public static function checkAuftragBereich($id, $bereich)
    {
        # Parameter setzen
        $open = 0;
        # Part
        $part = ($bereich == 'evaluation') ? 1 : 2;
        # Evaluation erforderlich ?
        $is = self::checkPart($id, '' . $bereich . '');
        if ($is):
            # Evaluation: alle Punkte erfüllt ?
            $open = self::countOpenEvaluation($id, $part);
        endif;
        return $open;
    }

    # Status ändern (Base)
    public static function setStatusBase($id, $status)
    {
        self::run("UPDATE base SET status = $status WHERE id = ? LIMIT 1", [$id]);
    }

    # Vorgang beenden, wenn keine Punkte mehr offen sind
    public static function finishID($id)
    {
        # Parameter setzen
        $openEvaluation = 0;
        $openTracking = 0;
        # Evaluation erforderlich ?
        $isEvaluation = self::checkPart($id, 'evaluation');
        if ($isEvaluation):
            # Evaluation: alle Punkte erfüllt ?
            $openEvaluation = self::countOpenEvaluation($id, 1);
        endif;
        # Tracking erforderlich ?
        $isTracking = self::checkPart($id, 'tracking');
        # Tracking: alle Punkte erfüllt ?
        if ($isTracking):
            # Evaluation: alle Punkte erfüllt ?
            $openTracking = self::countOpenEvaluation($id, 2);
        endif;
        # Vorgang beenden, wenn alles erfüllt ist
        if ($openEvaluation == 0 && $openTracking == 0):
            # Vorgang beenden
            self::run("UPDATE base SET status = '6' WHERE id = ? LIMIT 1", [$id]);
            self::run("UPDATE base2info SET ende = now() WHERE bid = ? LIMIT 1", [$id]);
            self::setLog('Details', $id, '', '0', 'Status auf Beendet gesetzt', 1);
        endif;
    }

    # Alle Elemente (APQP) abrufen
    public static function getAllAPQP($part): bool|array
    {
        $sql = "SELECT * FROM apqp WHERE " . $part . " IS NOT NULL";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    # Alle Elemente (APQP) eines Standortes abrufen
    public static function getAllAPQPCitycode($part, $citycode): bool|array
    {
        $sql = "SELECT * FROM apqp WHERE " . $part . " IS NOT NULL AND bemerkung = '$citycode' AND status = '1' ORDER BY abteilung, sort";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    # APQP Element in Citycode vorhanden ?
    public static function checkAPQPCitycode($apqp, $citycode)
    {
        $sql = "SELECT id FROM apqp2citycode WHERE apqp = ? AND citycode = ?";
        return self::run($sql, [$apqp, $citycode])->fetchColumn();
    }

    public static function createNumber(): string
    {
        $jahr = DATE('Y');
        $n = self::run("SELECT nr FROM base WHERE nr LIKE '$jahr%' ORDER BY nr DESC LIMIT 1")->fetchColumn();
        if (!empty($n)):
            $zahl = explode(".", $n);
            $zahl = $zahl[1] * 1;
            $zahl++;
            $zahl = str_pad($zahl, 4, "0", STR_PAD_LEFT);
            $nr = $jahr . "." . $zahl;
        else:
            $nr = $jahr . ".0001";
        endif;
        return $nr;
    }

    # Neuen Auftrag in der Datenbank speichern
    public static function neu($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        # Zieldatum anpassen (muss wenigstens 14 Tage in der Zukunft liegen
        $zieldatum = date("Y-m-d", strtotime($start . '+ 14 days'));
        if (!$ziel || $ziel < $zieldatum) $ziel = $zieldatum;
        # Nummer erstellen
        $nr = self::createNumber();
        echo "$nr|$part_description";
        # Base speichern
        $sql = "INSERT INTO base SET location = '$citycode', ";
        $sql .= "name = '{$_SESSION['user']['dbname']}', ";
        $sql .= "status = '0', ";
        $sql .= "datum = ?, ";
        $sql .= "art = '$pruefung', ";
        $sql .= "mid = '{$_SESSION['user']['id']}', ";
        $sql .= "nr = '$nr'";
        self::run($sql, [$start]);
        $baseid = self::run("SELECT id FROM base WHERE nr = '$nr'")->fetchColumn();
        # Schreibrechte Angebote speichern
        self::run("INSERT INTO base2angebot SET mid = '{$_SESSION['user']['id']}', bid = '$baseid', awrite = '1', mitarbeiter = '{$_SESSION['user']['dbname']}', datum = now()");

        # Parameter setzen und speichern
        $pgmas = (!isset($gmas)) ? 0 : 1;
        $pnael = (!isset($nael)) ? 0 : 1;
        $ev = (!isset($evaluation_erforderlich)) ? 0 : 1;
        $tr = (!isset($tracking_erforderlich)) ? 0 : 1;
        $intern = ($quelle == 1) ? 1 : 0;
        # Zuordnung speichern
        $sql = "INSERT INTO base2part SET bid = '$baseid', ";
        $sql .= "gmas = ?, ";
        $sql .= "nael = ?, ";
        $sql .= "intern = '$intern', ";
        $sql .= "evaluation = '$ev', ";
        $sql .= "partno = '$partno', ";
        $sql .= "tracking = '$tr'";
        self::run($sql, [$pgmas, $pnael]);

        $sql = "INSERT INTO base2info SET bid = '$baseid', ";
        $sql .= "part_description = ?, ";
        $sql .= "change_description = ?, ";
        $sql .= "change_type = ?, ";
        $sql .= "quelle = ?, ";
        $sql .= "zieldatum = ?, ";
        $sql .= "name = '{$_SESSION['user']['dbname']}', ";
        $sql .= "datum = now()";
        self::run($sql, [$part_description, $change_description, $changetype, $quelle, $ziel]);

        if (!empty($gmas)) {
            $pos = strpos($gmas, ',');
            if ($pos === false) {
                $sql = "INSERT INTO base2gmas SET bid = '$baseid',";
                $sql .= "gmas = ?, ";
                $sql .= "name = '{$_SESSION['user']['dbname']}',";
                $sql .= "datum = now()";
                self::run($sql, [$gmas]);
            } else {
                $a = explode(",", $gmas);
                $len = count($a);
                for ($i = 0; $i < $len; $i++) {
                    $a[$i] = trim($a[$i]);
                    $sql = "INSERT INTO base2gmas SET bid = '$baseid',";
                    $sql .= "gmas = '$a[$i]', ";
                    $sql .= "name = '{$_SESSION['user']['dbname']}',";
                    $sql .= "datum = now()";
                    self::run($sql);
                }
            }
        }
        if (!empty($nael)) {
            $pos = strpos($nael, ',');
            if ($pos === false) {
                $sql = "INSERT INTO base2nael SET bid = '$baseid',";
                $sql .= "nael = ?, ";
                $sql .= "name = '{$_SESSION['user']['dbname']}',";
                $sql .= "datum = now()";
                self::run($sql, [$nael]);
            } else {
                $a = explode(",", $nael);
                $len = count($a);
                for ($i = 0; $i < $len; $i++) {
                    $a[$i] = trim($a[$i]);
                    $sql = "INSERT INTO base2nael SET bid = '$baseid',";
                    $sql .= "nael = '$a[$i]', ";
                    $sql .= "name = '{$_SESSION['user']['dbname']}',";
                    $sql .= "datum = now()";
                    self::run($sql);
                }
            }
        }
        if (!empty($deviation)) {
            $pos = strpos($deviation, ',');
            if ($pos === false) {
                $sql = "INSERT INTO base2deviation SET bid = '$baseid',";
                $sql .= "deviation = ?, ";
                $sql .= "name = '{$_SESSION['user']['dbname']}',";
                $sql .= "datum = now()";
                self::run($sql, [$deviation]);
            } else {
                $a = explode(",", $deviation);
                $len = count($a);
                for ($i = 0; $i < $len; $i++) {
                    $a[$i] = trim($a[$i]);
                    $sql = "INSERT INTO base2deviation SET bid = '$baseid',";
                    $sql .= "deviation = '$a[$i]', ";
                    $sql .= "name = '{$_SESSION['user']['dbname']}',";
                    $sql .= "datum = now()";
                    self::run($sql);
                }
            }
        }

        # Evaluation eintragen
        if (isset($evaluation_erforderlich) && $evaluation_erforderlich == 1) {
            $ae = count($evaluation);
            if ($ae > 0) {
                for ($i = 0; $i < $ae; $i++) {
                    $bereich = self::run("SELECT evaluation FROM apqp WHERE id = '$evaluation[$i]'")->fetchColumn();
                    self::run("INSERT INTO base2apqp SET bid = '$baseid', apqp = '$evaluation[$i]', part = '1', bereich = '$bereich'");
                }
            }
        }
        $a = self::run("SELECT bereich FROM base2apqp WHERE bid = '$baseid' AND part = '1' GROUP BY bereich")->fetchAll(PDO::FETCH_OBJ);
        foreach ($a as $b) {
            self::run("INSERT INTO pla2evaluation SET bid = '$baseid', bereich = '$b->bereich', status = '0'");
        }
        # Tracking eintragen
        if (isset($tracking_erforderlich) && $tracking_erforderlich == 1) {
            $ae = count($tracking);
            if ($ae > 0) {
                for ($i = 0; $i < $ae; $i++) {
                    $bereich = self::run("SELECT tracking FROM apqp WHERE id = '$tracking[$i]'")->fetchColumn();
                    self::run("INSERT INTO base2apqp SET bid = '$baseid', apqp = '$tracking[$i]', part = '2', bereich = '$bereich'");
                }
            }
        }
        $a = self::run("SELECT bereich FROM base2apqp WHERE bid = '$baseid' AND part = '2' GROUP BY bereich")->fetchAll(PDO::FETCH_OBJ);
        foreach ($a as $b) {
            self::run("INSERT INTO imp2tracking SET bid = '$baseid', bereich = '$b->bereich', status = '0'");
        }
        self::run("INSERT INTO base2pla SET bid = '$baseid'");
        self::run("INSERT INTO base2imp SET bid = '$baseid'");

        # Lieferant eintragen
        # Prüfen, ob der übertragene Lieferant bereits in der Datenbank steht
        $id = self::checkLieferant($lieferant);
        if ($id !== false) {
            $sql = "INSERT INTO base2lieferant SET bid = '$baseid', lid = '$id'";
            $ll = self::run("SELECT id FROM location2lieferant WHERE id = '$id' AND citycode = '$citycode'")->fetchColumn();
            if ($ll === false) {
                self::run("INSERT INTO location2lieferant SET lid = '$id', citycode = '$citycode'");
            }
        } else {
            $sql = "INSERT INTO b_lieferanten SET lieferant = ?, mitarbeiter = '{$_SESSION['user']['dbname']}'";
            self::run($sql, [$lieferant]);
            $lid = self::run("SELECT id FROM b_lieferanten WHERE lieferant = ?", [$lieferant])->fetchColumn();
            self::run("INSERT INTO location2lieferant SET lid = '$lid', citycode = '$citycode'");
            $sql = "INSERT INTO base2lieferant SET bid = '$baseid', lid = '$lid'";
        }
        self::run($sql);
    }

    # Alte Teile
    public function getOldPart($bid)
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%d.%m.%Y') AS tag FROM base2oldpart WHERE bid = ?";
        return self::run($sql, [$bid])->fetch(PDO::FETCH_OBJ);
    }
    public static function isOldPart($bid): int
    {
        $sql = "SELECT name FROM base2oldpart WHERE bid = ?";
        $a = self::run($sql, [$bid])->fetchColumn();
        return (empty($a)) ? 0 : 1;
    }

    public function alteTeile($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        if($aktion == 1) {
            $sql = "INSERT INTO base2oldpart SET bid = '$id', aktion = '$aktion', ziel = '$ziel'";
        } else {
            $sql = "INSERT INTO base2oldpart SET bid = '$id', aktion = '$aktion'";
        }
        echo $sql;
        self::run($sql);
    }

    public function endeAlteTeile($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        $sql = "UPDATE base2oldpart SET name = '{$_SESSION['user']['dbname']}', datum = now() WHERE bid = '$id' LIMIT 1";
        self::run($sql);
        self::setLog('Aktion', $id, '', '0', 'Das Verschrotten der alten Teile wurde dokumentiert', 1);
    }
    # Lieferant vorhanden ?
    public static function checkLieferant($lieferant)
    {
        $sql = "SELECT id FROM b_lieferanten WHERE id = ?";
        return self::run($sql, [$lieferant])->fetchColumn();
    }


    # Teilenummer eingetragen ?
    public static function checkPartNo($bid, $alt, $neu, $bezeichnung)
    {
        $sql = "SELECT id FROM base2partno WHERE bid = ? AND alt = ? AND neu = ? AND bezeichnung = ?";
        return self::run($sql, [$bid, $alt, $neu, $bezeichnung])->fetchColumn();
    }

    # Teilenummern zählen
    public static function countPartNo($bid)
    {
        $sql = "SELECT COUNT(id) FROM base2partno WHERE bid = ?";
        return self::run($sql, [$bid])->fetchColumn();
    }

    # Teilenummern zählen
    public static function countPartNoOpen($bid)
    {
        $sql = "SELECT COUNT(id) FROM base2partno WHERE bid = ? AND status = '0'";
        return self::run($sql, [$bid])->fetchColumn();
    }

    # Teilenummer eintragen
    public static function insertPartNo($row, $id, $lid, $ziel)
    {
        var_dump($row);
        if (!isset($row[7]) || $row[7] == '') $row[7] = $ziel;
        $sql = "INSERT INTO base2partno SET anlage = '$row[1]', ";
        $sql .= "bezeichnung = '$row[2]', ";
        $sql .= "alt = '$row[3]', ";
        $sql .= "neu = '$row[4]', ";
        $sql .= "doppelsnr = '$row[5]', ";
        $sql .= "ziel = '$row[7]', ";
        $sql .= "bid = '$id', ";
        $sql .= "name = '{$_SESSION['user']['dbname']}', ";
        $sql .= "datum = now(), ";
        $sql .= "status = '0', ";
        $sql .= "citycode = '{$_SESSION['wrk']['citycode']}', ";
        $sql .= "lid = '$lid'";
        echo $sql;
        self::run($sql);
    }

    # Quelle ausgeben
    public static function getFeldValue($id, $tabelle)
    {
        return self::run("SELECT " . $tabelle . " FROM " . $tabelle . " WHERE id = '$id'")->fetchColumn();
    }

    # Kommentare zählen (in einem Bereich)
    public static function countComments($part, $bid, $bereich)
    {
        $sql = "SELECT COUNT(id) FROM kommentar WHERE part = ? AND bid = ? AND bereich = ?";
        return self::run($sql, [$part, $bid, $bereich])->fetchColumn();
    }

    # Antworten zählen
    public static function countAntwort($fid)
    {
        $sql = "SELECT COUNT(id) FROM antwort WHERE fid = ?";
        return self::run($sql, [$fid])->fetchColumn();
    }

    # Kommentare ausgeben
    public static function getCom($bid, $bereich, $part): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%d.%m.%Y %H:%i') AS am FROM kommentar WHERE bid = ? AND bereich = ? AND part = ? ORDER BY datum";
        return self::run($sql, [$bid, $bereich, $part])->fetchAll(PDO::FETCH_OBJ);
    }

    # Antworten ausgeben
    public static function getAntwort($fid): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%d.%m.%Y %H:%i') AS am FROM antwort WHERE fid = ? ORDER BY datum";
        return self::run($sql, [$fid])->fetchAll(PDO::FETCH_OBJ);
    }

    # Ersteller Kommentar
    public static function getErstellerCom($id, $tabelle)
    {
        $sql = "SELECT mid FROM " . $tabelle . " WHERE id = '$id'";
        $a = self::run($sql)->fetchColumn();
        $sql = "SELECT * FROM b_mitarbeiter WHERE id = '$a'";
        return self::runa($sql)->fetch(PDO::FETCH_OBJ);
    }

    # Lieferanten abrufen
    public static function getLieferanten($citycode): bool|array
    {
        $sql = "SELECT * FROM location2lieferant WHERE citycode = ?";
        return self::run($sql, [$citycode])->fetchAll(PDO::FETCH_OBJ);
    }

    # Details Lieferant
    public static function getLieferant($id)
    {
        $sql = "SELECT * FROM b_lieferanten WHERE id = ?";
        return self::run($sql, [$id])->fetch(PDO::FETCH_OBJ);
    }

    # Lieferant in Base
    public static function getLieferantBase($id)
    {
        $sql = "SELECT lid FROM base2lieferant WHERE bid = ?";
        return self::run($sql, [$id])->fetchColumn();
    }

    # Status Part No
    public static function getStatusPart($id)
    {
        $sql = "SELECT status FROM status WHERE id = '1" . $id . "'";
        return self::run($sql)->fetchColumn();
    }

    # Verantwortliche  Change Managementabrufen
    public static function getVerantwortlichCM(): bool|array
    {
        $sql = "SELECT * FROM b_verantwortlich ORDER BY verantwortlich";
        return self::run($sql)->fetchAll(PDO::FETCH_OBJ);
    }

    # Neuen APQP Punkt speichern
    public static function setNewAPQP($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        if (isset($apqpid) && $apqpid != '') {
            $sql = "UPDATE apqp SET titel = ?, ";
            $sql .= $bereich . " = ?, ";
            $sql .= "abteilung = ? ";
            $sql .= "WHERE id = '$apqpid' LIMIT 1";
            self::run($sql, [$titel, $abteilung, $verantwortlich]);
        } else {
            # Sortierung (Neuen Eintrag ans Ende stellen)
            $max = self::maxSortAPQP($bereich, $citycode, $abteilung);
            $max = ($max == 0) ? 1 : $max++;
            # String umformatieren
            $titel = str_replace("<p>", "", $titel);
            $titel = str_replace("<br></p>", "", $titel);
            # Eintrag speichern
            $sql = "INSERT INTO apqp SET titel = ?, ";
            $sql .= $bereich . " = ?, ";
            $sql .= "abteilung = ?, ";
            $sql .= "bemerkung = ?, ";
            $sql .= "status = '1', ";
            $sql .= "sort = '$max'";
            self::run($sql, [$titel, $abteilung, $verantwortlich, $citycode]);
            $id = self::maxID('apqp');
            # Eintrag zu Citycode hinzu
            $sql = "INSERT INTO apqp2citycode SET apqp = '$id', ";
            $sql .= "citycode = ?, ";
            $sql .= "" . $bereich . " = '1', ";
            $sql .= "mitarbeiter = '{$_SESSION['user']['dbname']}', ";
            $sql .= "datum = now()";
            self::run($sql, [$citycode]);
            # i18n
            $sql = "INSERT INTO b_i18n SET keyword = 'apqp_{$id}', ";
            $sql .= "de = '$titel'";
            self::runa($sql);
            $_SESSION['text']['apqp_' . $id . ''] = $titel;
        }
    }

    # APQP löschen
    public static function deleteAPQP($id)
    {
        $sql = "UPDATE apqp SET status = 0 WHERE id = ? LIMIT 1";
        self::run($sql, [$id]);
    }

    # Max Sort APQP
    public static function maxSortAPQP($bereich, $citycode, $kurz)
    {
        $sql = "SELECT MAX(sort) FROM apqp WHERE bemerkung = ? AND " . $bereich . " = ?";
        return self::run($sql, [$citycode, $kurz])->fetchColumn();
    }

    # Max ID
    public static function maxID($tabelle)
    {
        $sql = "SELECT MAX(id) FROM " . $tabelle;
        return self::run($sql)->fetchColumn();
    }

    # Zuordnung APQP Citycode löschen
    public static function deleteApqpCitycode($post)
    {
        foreach ($post as $key => $value) {
            $$key = $value;
        }
        if ($aktion == 0) {
            $sql = "DELETE FROM apqp2citycode WHERE apqp = '$id' AND citycode = '$citycode' LIMIT 1";
        }
        if ($aktion == 1) {
            $sql = "INSERT INTO apqp2citycode SET apqp = '$id', citycode = '$citycode', " . $bereich . " = '1', mitarbeiter = '{$_SESSION['user']['dbname']}', datum = now()";
        }

        self::run($sql);
    }

    # Einen Eintrag APQP abrufen
    public static function getOneAPQP($id)
    {
        $sql = "SELECT * FROM apqp WHERE id = ?";
        return self::run($sql, [$id])->fetch(PDO::FETCH_OBJ);
    }

    # Alle APQP Elemente eines Standortes in einem Bereich zählen
    public static function countAPQPCC($bereich, $citycode)
    {
        $sql = "SELECT COUNT(id) FROM apqp WHERE " . $bereich . " != '' AND bemerkung = ?";
        return self::run($sql, [$citycode])->fetchColumn();
    }

    # LOG
    public static function setLog($seite, $bid, $part, $pid, $aktion, $v)
    {
        $sql = "INSERT INTO logfile SET datum = now(), ";
        $sql .= "name = '{$_SESSION['user']['dbname']}', ";
        $sql .= "seite = '$seite', ";
        $sql .= "bid = '$bid', ";
        $sql .= "part = '$part', ";
        $sql .= "pid = '$pid', ";
        $sql .= "aktion ='$aktion', ";
        $sql .= "visibility = '$v'";
        self::run($sql);
    }


    # Ein Element abrufen
    public static function getOneApqpBereich($apqp, $bid, $part)
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%d.%m.%Y') as tag, ";
        $sql .= "DATE_FORMAT(datum, '%H:%i:%s') as zeit FROM base2apqp ";
        $sql .= "WHERE apqp = ?  AND bid = ? AND part = ?";
        return self::run($sql, [$apqp, $bid, $part])->fetch(PDO::FETCH_OBJ);
    }
    # Tracking / Evaluation bearbeiten

    /**
     * @param $id = Base ID
     * @param $part = 1 (Evaluation) oder 2 (Tracking)
     * @param $apqp = ID des APQP Eintrages in der Tabelle apqp (wird hier im Feld apqp gespeichert)
     * @param $citycode
     */
    public static function showAPQP($id, $part, $apqp, $citycode)
    {
        # Base
        $s = self::getElement($id);
        if ($apqp->apqp < 10) $apqp->apqp = "0" . $apqp->apqp;
        # Antwort abrufen
        $a = self::antwortAPQP($apqp->apqp, $id, $part);
        # Location
        $location = self::getLocationInfo($citycode);

        $bg = ($a->antwort == 'nio') ? 'bg-light-lines' : '';
        $colorh = ($a->antwort == 'nio') ? 'danger' : 'black';
        $border = ($a->antwort == 'nio') ? 'border__top--solid-red-3' : '';
        # Kosten
        $k = self::costAPQP($a->apqp,$id);

        # ROW getFormAPQP(bid,apqpid,citycode,part)
        if ($s->status < 6 && $a->antwort != ''):
            Functions::htmlOpenDivAction("row mt-4 $border pointer", "onclick=\"getFormAPQP($id,$apqp->apqp,'$citycode',$part)\"");
        else:
            Functions::htmlOpenSingleDiv("row mt-2 $border");
        endif;
        Functions::htmlOpenDiv("10", "bottom", "dotted", "", "", "", "py-2");
        ?>
        <div class="font-size-13 text-<?= $colorh ?>">
            <b><?= $apqp->id ?>.</b> <?= $_SESSION['text']['apqp_' . $apqp->apqp . ''] ?>
        </div>
        <?php
        Functions::htmlCloseDiv();
        Functions::htmlOpenDiv("2", "bottom", "dotted", "", "", "", "pb-2");
        if (!empty($k)):
            echo "<span class='float-end mt-2 me-0 badge badge-$colorh oswald font-size-13'><span class='font-weight-100'>$location->cur</span> $k->kosten</span>";
        endif;
        Functions::htmlCloseDiv();
        Functions::htmlCloseSingleDiv();
        # END ROW

        # Antworten umschreiben
        if($part == 2){
            $tio = $_SESSION['text']['ja'];
            $tnio = $_SESSION['text']['nein'];
            $tnoimpact = $_SESSION['text']['t_nichtErforderlich'];
        } else {
            $tio = $_SESSION['text']['io'];
            $tnio = $_SESSION['text']['nio'];
            $tnoimpact = $_SESSION['text']['t_noImpact'];
        }
        ?>
        <div class="<?= $bg ?> mb-1">

            <?php
            if (!$a->antwort) {
                $inaktiv = '<i class="far fa-circle text-muted"></i>';
                Functions::htmlOpenSingleDivID("a$apqp->apqp", "border__bottom--dotted-gray bg-light-lines font-weight-300 font-size-13 row p-0 m-0");
                Functions::htmlOpenDiv("3", "right", "dotted", "pointer", "", "", "pt-1");
                # ROW
                Functions::htmlOpenSingleDiv("row pb-2 font-size-12");
                Functions::htmlOpenDivAction("col-4 text-center", "onclick=\"setAPQP($id,'$apqp->apqp','io','$s->location', $part);\"", "d3$apqp->apqp");
                echo $inaktiv;
                Functions::htmlCloseSingleDiv();
                Functions::htmlOpenDivAction("col-8", "onclick=\"setAPQP($id,'$apqp->apqp','io','$s->location', $part);\"", "d3$apqp->apqp");
                echo $tio;
                Functions::htmlCloseSingleDiv();
                Functions::htmlCloseSingleDiv();
                # END ROW

                Functions::htmlCloseDiv();
                Functions::htmlOpenDiv("4", "right", "dotted", "pointer", "", "", "pt-1");
                # ROW
                Functions::htmlOpenSingleDiv("row pb-2 font-size-12");
                Functions::htmlOpenDivAction("col-4 text-center", "onclick=\"setAPQP($id,'$apqp->apqp','no-impact','$s->location', $part);\"", "d2$apqp->apqp");
                echo $inaktiv;
                Functions::htmlCloseSingleDiv();
                Functions::htmlOpenDivAction("col-8", "onclick=\"setAPQP($id,'$apqp->apqp','no-impact','$s->location', $part);\"", "d2$apqp->apqp");
                echo $tnoimpact;
                Functions::htmlCloseSingleDiv();
                Functions::htmlCloseSingleDiv();
                # END ROW
                Functions::htmlCloseDiv();
                Functions::htmlOpenDiv("3", "", "", "pointer", "", "", "pt-1");
                # ROW
                Functions::htmlOpenSingleDiv("row pb-2 font-size-12");
                Functions::htmlOpenDivAction("col-4 text-center", "onclick=\"setAPQP($id,'$apqp->apqp','nio','$s->location', $part);\"", "d1$apqp->apqp");
                echo $inaktiv;
                Functions::htmlCloseSingleDiv();
                Functions::htmlOpenDivAction("col-8", "onclick=\"setAPQP($id,'$apqp->apqp','nio','$s->location', $part);\"", "d1$apqp->apqp");
                echo $tnio;
                Functions::htmlCloseSingleDiv();
                Functions::htmlCloseSingleDiv();
                # END ROW
                Functions::htmlCloseDiv();
                Functions::htmlCloseSingleDiv();
                # END ROW
                ?>
                <div id="kf<?= $apqp->apqp ?>" class="dspnone">

                </div>
                <div id="e<?= $apqp->apqp ?>" class="dspnone">

                </div>
                <?php
                # Es wurde bereits eine Antwort eingetragen
            } else {
                # ROW
                Functions::htmlOpenSingleDiv("row font-size-12");
                Functions::htmlOpenDiv("4", "bottom", "dotted", "", "", "", "py-1");
                self::dspAntwortAPQP($apqp->antwort,$part);
                Functions::htmlCloseDiv();
                Functions::htmlOpenDiv("8", "bottom", "dotted", "", "", "", "py-1 text-end");
                echo $apqp->name . " &bull; " . $apqp->tag . " &bull; " . $apqp->zeit;
                Functions::htmlCloseDiv();
                Functions::htmlCloseSingleDiv();
                # END ROW
                # Bemerkung
                if ($apqp->bemerkung != ''):
                    # ROW
                    Functions::htmlOpenSingleDiv("row font-size-12");
                    Functions::htmlOpenDiv("1", "bottom", "dotted", "text-center", "", "", "py-2");
                    echo '<i class="fa fa-comment text-muted"></i>';
                    Functions::htmlCloseDiv();
                    Functions::htmlOpenDiv("11", "bottom", "dotted", "", "", "", "py-2");
                    echo $apqp->bemerkung;
                    Functions::htmlCloseDiv();
                    Functions::htmlCloseSingleDiv();
                    # END ROW
                endif;
                # Bemerkung Kosten
                if (!empty($k) && $k->anmerkung != ''):
                    ?>
                    <div class="cost_kom p-3 font-size-12">
                        <cite><?= $k->anmerkung ?></cite>
                    </div>
                <?php
                endif;
                ?>
                <?php
            }
            $bemerkung = (!empty($a->bemerkung)) ? $a->bemerkung : '';
            ?>
        </div>
        <div id="f<?= $apqp->apqp ?>"></div>
        <?php

    }

    # Suche
    public function treffer($parameter, $table, $feld)
    {
        return self::run("SELECT COUNT(id) AS anzahl FROM ". $table ." WHERE BINARY ". $feld ." LIKE '%$parameter%'")->fetchColumn();
    }
    public function suchergebnis($parameter, $table, $feld)
    {
        return self::run("SELECT * FROM ". $table ." WHERE BINARY ". $feld ." LIKE '%$parameter%' LIMIT 10")->fetchAll(PDO::FETCH_OBJ);
    }
    public static function dspParameter($table,$feld,$parameter,$bid)
    {
        return self::run("SELECT ". $feld ." FROM ". $table ." WHERE ". $parameter ." = '$bid'")->fetchColumn();
    }

    # Datei löschen
    public function deleteFile($id)
    {
        $datei = self::run("SELECT * FROM base2files WHERE id = '$id'");
        self::run("DELETE FROM base2files WHERE id = '$id' LIMIT 1");
        $file = Functions::getBaseURL()."lib/Pages/ChangeManagement/MVC/View/files/".$datei->datei;
        unset($file);
    }

    # Berechtigung Angebot ändern
    public function setAccess($part,$bid,$mid)
    {
        if($part == 'awrite') {
            $sql = "UPDATE base2angebot SET awrite = '1' WHERE bid = '$bid' AND mid = '$mid' LIMIT 1";
            echo $sql;
            self::run($sql);
        } else {
            $sql = "UPDATE base2angebot SET awrite = '0' WHERE bid = '$bid' AND mid = '$mid' LIMIT 1";
            echo $sql;
            self::run($sql);
        }
    }
}