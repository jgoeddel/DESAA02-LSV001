<?php
/** (c) Joachim Göddel . RLMS */
namespace App\Pages\Rotationsplan\MVC\View\functions;

use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Rotationsplan\RotationsplanDatabase;
use DateTime;
use Exception;
use PDO;

class Functions
{
    # Datenbankzugriff für Statische Methoden
    public static function db(): PDO
    {
        $dataSource = "mysql:host=localhost;dbname=rhs_rotationsplan";
        $pdo = new \PDO($dataSource, $_SESSION['db']['user'], $_SESSION['db']['pass']);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    }

    # Anfangsbuchstaben der Mitarbeiternachnamen zählen (Menü)
    public function countAnfangsBuchstabe($counter)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM b_mitarbeiter ";
        $sql.= "WHERE schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND BINARY name LIKE '{$_SESSION['parameter']['abc'][$counter]}%' AND status = '1'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl;
    }

    # Springer ID
    public function getSpringerID()
    {
        $sql = "SELECT id FROM b_mitarbeiter WHERE schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND vorname = 'A.' ";
        $sql.= "AND name = 'k.'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ)->id;
    }
    # Ausgabe Station (in Optionsfeld)
    public function getSelectOption($sid)
    {
        $sql = "SELECT * FROM b_station WHERE id = :sid";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':sid' => $sid]);
        $s = $stmt->fetch(\PDO::FETCH_OBJ);
        echo "<option value='$sid'>$s->station &bull; $s->bezeichnung</option>";
    }
    # Alle Stationen einer Abteilung abrufen
    public function getStationAbteilung(): bool|array
    {
        $sql = "SELECT * FROM b_station WHERE abteilung = :abteilung ";
        $sql .= "AND status = '1' ";
        $sql .= "ORDER BY station";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([
            ':abteilung' => $_SESSION['user']['wrk_abteilung']
        ]);
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
    # Temporäre Station
    public function getTmpStation($sid,$uid,$zeitschiene): bool
    {
        $sql = "SELECT id FROM tmp_station WHERE sid = '$sid' ";
        $sql.= "AND uid = '$uid' ";
        $sql.= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND zeitschiene = '$zeitschiene' ";
        $sql.= "AND sess = '{$_SESSION['sess']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            return $stmt->fetch(\PDO::FETCH_OBJ)->id;
        }
        return false;
    }
    # Wieviele Mitarbeiter können an den jeweiligen Stationen arbeiten
    public function getAnzahlMaStation($sid, $zeitschiene)
    {
        $sql = "SELECT COUNT(id) AS summe ";
        $sql .= "FROM tmp_station WHERE sid = '$sid' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}'";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND zeitschiene = '$zeitschiene' ";
        $sql .= "AND sess = '{$_SESSION['sess']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
           return $stmt->fetch(\PDO::FETCH_OBJ)->summe;
        }
    }
    # Prüfen, ob die Station bereits in der tmp_anzahl_station eingetragen ist
    public function getTmpAnzStation($sid): bool
    {
        $sql = "SELECT id FROM tmp_anzahl_station ";
        $sql .= "WHERE sid = '$sid' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND sess = '{$_SESSION['sess']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    # Anzahl je Station
    public function getAnzTmpAnzStation($sid): int
    {
        $sql = "SELECT anzahl FROM tmp_anzahl_station WHERE sid = '$sid' AND ";
        $sql .= "abteilung = '{$_SESSION['user']['wrk_abteilung']}' AND ";
        $sql .= "schicht = '{$_SESSION['user']['wrk_schicht']}' AND ";
        $sql .= "sess = '{$_SESSION['sess']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl;
        } else {
            return 0;
        }
    }
    # Anzahl Mitarbeiter je Station
    public function getAnzMaStation($sid): int
    {
        $sql = "SELECT mitarbeiter FROM b_station WHERE id = '$sid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ)->mitarbeiter;
        } else {
            return 1;
        }
    }
    # Mitarbeiter an festen Stationen
    public function getMaFesteStation()
    {
        $sql = "SELECT * FROM c_anwesenheit WHERE sid != '0' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND datum = '{$_SESSION['wrk']['datum']}' ";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
    }
    # Welche Mitarbeiterposition ist noch frei (wenn mehr als eine Person an der Station arbeitet
    public function getMaxMaStation($sid,$uid)
    {
        $sql = "SELECT mitarbeiter FROM tmp_rotationsplan WHERE sess ='{$_SESSION['sess']}' ";
        $sql .= "AND uid = '$uid' ";
        $sql .= "AND sid = '$sid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ)->mitarbeiter;
    }
    # Rückgabe der noch vorhandenen Mitarbeiter aus der tmp_station (Gruppiert)
    public function getMaGrp()
    {
        $sql = "SELECT uid FROM tmp_station WHERE sess = '{$_SESSION['sess']}' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "GROUP BY uid";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
    }
    # Mitarbeiter aus der temporären Tabelle abrufen, sortiert nach Anzahl möglicher Stationen
    public function getSummeMa()
    {
        $sql = "SELECT * FROM tmp_anzahl_user WHERE sess = '{$_SESSION['sess']}' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "ORDER BY anzahl, rand()";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
    }
    # Niedrigste Anzahl an möglichen Stationen in der tmp_anzahl_user
    public function getMinAnzStationen(): bool|array|int
    {
        $sql = "SELECT anzahl,uid FROM tmp_anzahl_user WHERE ";
        $sql .= "sess = '{$_SESSION['sess']}' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "AND anzahl < 4 ORDER BY anzahl";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        } else {
            return 0;
        }
    }
    # ID der Station anhand des Namens ermitteln
    public function getStationIdByName($name): int
    {
        $sql = "SELECT id FROM b_station WHERE bezeichnung = '$name' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ)->id;
        } else {
            return 0;
        }
    }
    # Mitarbeiter einer Station
    public function getMaStation($sid,$summe)
    {
        $sql = "SELECT * FROM tmp_station WHERE sid = '$sid' ORDER BY anzahl LIMIT $summe";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
    }
    # Station in temporärem Rotationsplan belegt ?
    public function getStationTmpRotation($sid, $zeitschiene, $mitarbeiter)
    {
        $sql = "SELECT uid FROM tmp_rotationsplan WHERE sid = '$sid' ";
        $sql .= "AND zeitschiene = '$zeitschiene' ";
        $sql .= "AND mitarbeiter = '$mitarbeiter' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND sess = '{$_SESSION['sess']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ)->uid;
        }
    }
    # Mitarbeiter aus dem Rotationsplan ermitteln, die bereits 3 Einsätze haben
    public function getMaRotationsplanGrp()
    {
        $sql = "SELECT uid FROM tmp_rotationsplan WHERE sess = '{$_SESSION['sess']}' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "GROUP BY uid";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
    }
    # Offene Stationen vom temporären Rotationsplan
    public function getOffenRotationsplan($uid)
    {
        $sql = "SELECT * FROM tmp_rotationsplan WHERE uid = '$uid' ";
        $sql.= "AND sess = '{$_SESSION['sess']}' ";
        $sql.= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "ORDER BY anzahl, sid, rand()";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
    }
    # Details Station
    public function getStation($sid)
    {
        $sql = "SELECT * FROM b_station WHERE id = '$sid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ);
        }
    }
    # Mitarbeiter an Station in Zeitschiene
    public function getRowRotationsplan($sid,$zeitschiene,$mitarbeiter)
    {
        $sql = "SELECT * FROM c_person2station WHERE sid = '$sid' ";
        $sql.= "AND datum = '{$_SESSION['wrk']['datum']}' ";
        $sql.= "AND zeitschiene = '$zeitschiene' ";
        $sql.= "AND mitarbeiter = '$mitarbeiter'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ);
        } else {
            return false;
        }
    }
    # Wann wurde ein Eintrag im Rotationsplan geändert
    public function getTimeChange($sid,$uid,$zeit)
    {
        $sql = "SELECT eintrag FROM c_person2station WHERE datum = '{$_SESSION['wrk']['datum']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND sid = '$sid' ";
        $sql .= "AND uid = '$uid' ";
        $sql .= "AND zeitschiene = '$zeit'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ)->eintrag;
        }
    }
    # Zeit zwischen Änderung und jetzt
    public function getTimeDiff($von)
    {
        $sql = "SELECT TIMEDIFF('{$_SESSION['parameter']['heuteSQL']}', '$von') AS dif";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ)->dif;
        }
    }
    # Anzahl der Einsätze an einem Tag
    public function getAnzahlEinsatzTag($uid)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM c_person2station WHERE datum = '{$_SESSION['wrk']['datum']}' ";
        $sql.= "AND uid = '$uid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl;
        }
    }
    # Qualifikation des Ma an einer Station
    public function getQualiMaStation($uid,$sid): bool
    {
        $sql = "SELECT id FROM c_qualifikation WHERE uid = '$uid' AND sid = '$sid' AND status IS NULL";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    # Qualifikation des Ma an einer Station
    public function getQualiNotNull($uid,$sid): bool
    {
        $sql = "SELECT id FROM c_qualifikation WHERE uid = '$uid' AND sid = '$sid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return true;
        } else {
            return false;
        }
    }
    # Qualifikation des Ma an einer Station (Details)
    public function getQualiMaStationDetails($uid,$sid)
    {
        $sql = "SELECT *, DATE_FORMAT(datum, '%d.%m.%Y') AS tag FROM c_qualifikation WHERE uid = '$uid' AND sid = '$sid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ);
        } else {
            return false;
        }
    }
    # Tabellenzeile ausgeben
    public function getTrZeitschiene($sid,$uid,$zeitschiene,$mitarbeiter,$springer,$guid)
    {
        # Station
        $stn = $this->getStation($sid);
        # Nicht besetzte Station kennzeichnen
        $bgtr = ($uid == $springer) ? 'bg-light-lines' : '';
        # Schwere Station kennzeichnen
        $red = ($stn->ergo == 'danger') ? 'bg-light-lines-danger' : '';
        # Einsätze an dem angezeigten Tag
        $einsatz = $this->getAnzahlEinsatzTag($uid);
        $dspEinsatz = match ($einsatz) {
            1,2 => "<span class='badge badge-warning'>$einsatz</span>",
            3 => "<span class='text-muted'>$einsatz</span>",
            default => "<span class='badge badge-danger'>$einsatz</span>",
        };
        # Gesamtanzahl der Einsätze an der Station
        $gesamt = $this->getAnzahlEinsatz($uid,$sid);
        # Icon anzeigen, wenn Springer
        $caretLeft = ($uid == $springer) ? '<i class="fa fa-caret-left text-danger"></i>' : '';
        # Neuer Eintrag ?
        $letzteAenderung = $this->getTimeChange($sid,$uid,$zeitschiene);
        $aenderung = $this->getTimeDiff(''.$letzteAenderung.'');
        # $neu = ($aenderung < '00:00:30') ? '<span class="badge badge-info ms-2 neu">NEU</span>' : '';
        # Klasse Mitarbeiter
        $classMa = ($guid == $uid) ? 'badge badge-success font-size-12 font-weight-300' : '';
        # Klasse Station
        $classStn = ($guid == $uid) ? 'badge badge-success font-size-12 font-weight-300' : '';
        # Qualifikation
        $qma = ($this->getQualiMaStation($guid,$stn->id) === true) ? "<i class='fa fa-square-full text-warning font-size-10 pointer' onclick='setMaStation($sid, $mitarbeiter, $guid, $zeitschiene, $uid, $springer)'></i>" : "";

        # Ausgabe der Zeile
        echo "<tr class='$bgtr'>";
        echo "<td class='$red'>&nbsp;</td>";
        echo "<td>";
        echo "<span class='$classMa'>";
        # Name des Mitarbeiters
        echo RotationsplanDatabase::getNameMAFormat($uid);
        # echo $neu;
        echo "</span>";
        echo "</td>";
        echo "<td class='text-center'>";
        if($uid != '40') echo $dspEinsatz;
        echo "</td>";
        echo "<td class='text-center'>$gesamt</td>";
        echo "<td class='text-center pointer stn$stn->id' onclick='showPossMa($stn->id)'>";
        echo $stn->station;
        echo "</td>";
        echo "<td class='icon$uid text-center'>$caretLeft $qma</td>";
        echo "</tr>";
    }

    # Tabellenzeile ausgeben
    public function getTrZeitschieneArchiv($sid,$uid,$zeitschiene,$mitarbeiter,$springer,$guid)
    {
        # Station
        $stn = $this->getStation($sid);
        # Nicht besetzte Station kennzeichnen
        $bgtr = ($uid == $springer) ? 'bg-light-lines' : '';
        # Schwere Station kennzeichnen
        $red = ($stn->ergo == 'danger') ? 'bg-light-lines-danger' : '';
        # Einsätze an dem angezeigten Tag
        $einsatz = $this->getAnzahlEinsatzTag($uid);
        $dspEinsatz = match ($einsatz) {
            1,2 => "<span class='badge badge-warning'>$einsatz</span>",
            3 => "<span class='text-muted'>$einsatz</span>",
            default => "<span class='badge badge-danger'>$einsatz</span>",
        };
        # Gesamtanzahl der Einsätze an der Station
        $gesamt = $this->getAnzahlEinsatz($uid,$sid);
        # Icon anzeigen, wenn Springer
        $caretLeft = ($uid == $springer) ? '<i class="fa fa-caret-left text-danger"></i>' : '';

        # Ausgabe der Zeile
        echo "<tr class='$bgtr'>";
        echo "<td class='$red'>&nbsp;</td>";
        echo "<td>";
        echo "<span class=''>";
        # Name des Mitarbeiters
        echo RotationsplanDatabase::getNameMAFormat($uid);
        echo "</span>";
        echo "</td>";
        echo "<td class='text-center'>";
        if($uid != '40') echo $dspEinsatz;
        echo "</td>";
        echo "<td class='text-center'>$gesamt</td>";
        echo "<td class='text-center'>";
        echo $stn->station;
        echo "</td>";
        echo "<td class='icon$uid text-center'>$caretLeft</td>";
        echo "</tr>";
    }

    # MITARBEITER ------------------------------------------------------------------------------------------------------
    # Mitarbeiter im Training
    public function getTraining($mid)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM c_matrix_training WHERE uid = :mid AND ";
        $sql .= "status IS NULL";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':mid' => $mid]);
        return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl;
    }
    # Mitarbeiter mit Handicap
    public function getHandicap($mid)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM c_handicap WHERE uid = :mid AND ";
        $sql .= "status != '9'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':mid' => $mid]);
        return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl;
    }
    # Mitarbeiter mit Handicap an Station
    public function getHandicapStation($mid,$sid)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM c_handicap WHERE uid = '$mid' AND  sid = '$sid' AND ";
        $sql .= "status != '9'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl;
    }
    # Mitarbeiter mit Handicap (Details)
    public function getHandicapDetails($mid): bool|array
    {
        $sql = "SELECT *, DATE_FORMAT(start, '%d.%m.%Y') AS beginn, DATE_FORMAT(ende, '%d.%m.%Y') AS stop";
        $sql.= " FROM c_handicap WHERE uid = :mid AND ";
        $sql .= "status != '9' AND ende > now()";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':mid' => $mid]);
        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        } else {
            return false;
        }
    }
    # Mitarbeiter außer Haus
    public function getAbwesend($mid): bool
    {
        $sql = "SELECT id FROM c_abwesend WHERE uid = :mid AND ";
        $sql .= "start <= '{$_SESSION['parameter']['heuteSQL']}' AND ende >= '{$_SESSION['parameter']['heuteSQL']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':mid' => $mid]);
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    # Anwesende Mitarbeiter Abteilung / Schicht
    public function getAnwesend(): bool|array
    {
        $sql = "SELECT * FROM c_anwesenheit WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND datum = '{$_SESSION['wrk']['datum']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_OBJ);
    }
    # Qualifikation vorhanden ?
    public function getQuali($uid, $sid): bool
    {
        $sql = "SELECT id FROM c_qualifikation WHERE uid = :uid ";
        $sql .= "AND sid = '$sid' AND status IS NULL";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute([':uid' => $uid]);
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    # Mitarbeiter in der Zeitschiene aktiv
    public function maZeitschieneAktiv($uid,$zeitschiene,$springer,$z)
    {
        $sql = "SELECT id FROM c_person2station WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "AND datum = '{$_SESSION['wrk']['datum']}' ";
        $sql.= "AND zeitschiene = '$zeitschiene' ";
        $sql.= "AND uid = '$uid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            $id = $stmt->fetch(\PDO::FETCH_OBJ)->id;
            $dsp = "<td id='td{$uid}{$zeitschiene}' class='text-center pointer' onclick='deleteMitarbeiterPlan($id,$uid,$zeitschiene,$springer,$z)'><i class='fas fa-user-circle text-primary'></i></td>";
        } else {
            if($this->getAnwesendMa($uid) === false){
                if($this->getAbwesend($uid) === false){
                    $dsp = '<td class="text-center"><i class="fa fa-caret-right text-muted"></td>';
                } else {
                    $dsp = '<td class="text-center"></td>';
                }
            } else {
                $dsp = '<td class="text-center"><i class="fa fa-user-circle text-muted"></i></td>';
            }
        }
        echo $dsp;
    }
    # Mitarbeiter in der Zeitschiene aktiv
    public function maZeitschieneArchiv($uid,$zeitschiene)
    {
        $sql = "SELECT id FROM c_person2station WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "AND datum = '{$_SESSION['wrk']['datum']}' ";
        $sql.= "AND zeitschiene = '$zeitschiene' ";
        $sql.= "AND uid = '$uid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            $id = $stmt->fetch(\PDO::FETCH_OBJ)->id;
            $dsp = "<td id='td{$uid}{$zeitschiene}' class='text-center'><i class='fas fa-user-circle text-primary'></i></td>";
        } else {
            if($this->getAnwesendMa($uid) === false){
                if($this->getAbwesend($uid) === false){
                    $dsp = '<td class="text-center"><i class="fa fa-caret-right text-muted"></td>';
                } else {
                    $dsp = '<td class="text-center"></td>';
                }
            } else {
                $dsp = '<td class="text-center"><i class="fa fa-user-circle text-muted"></i></td>';
            }
        }
        echo $dsp;
    }
    # Mitarbeiter anwesend
    public function getMaZsAnwesend($uid,$zeitschiene,$z): bool
    {
        $sql = "SELECT id FROM c_person2station WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "AND datum = '{$_SESSION['wrk']['datum']}' ";
        $sql.= "AND zeitschiene = '$zeitschiene' ";
        $sql.= "AND uid = '$uid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    # Mitarbeiter in Zeitschiene
    public function getMaZs($uid,$zeitschiene,$z): bool
    {
        $sql = "SELECT id FROM c_anwesenheit WHERE uid = '$uid' ";
        $sql.= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "AND datum = '{$_SESSION['wrk']['datum']}' ";
        $sql.= "AND zs$z = '1'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    # Ausgabe Mitarbeiter in der Zeitschiene anwesend
    public function maZeitschieneAnwesend($uid,$zeitschiene,$z)
    {
        if($this->getMaZsAnwesend($uid,$zeitschiene,$z) === true) {
            $dsp = '<td class="text-center"><i class="fa fa-user-circle text-muted"></i></td>';
        } else {
            $a = $this->getMaZs($uid,$zeitschiene,$z);
            if($this->getMaZs($uid,$zeitschiene,$z) === true){
                $dsp = "<td id='td{$uid}{$z}' class='text-center pointer' onclick='deleteMitarbeiterAnwesend($uid,$z)'><i class='fas fa-user-circle text-success'></i></td>";
            } else {
                $dsp = "<td class='text-center pointer' onclick='setMitarbeiterAnwesend($uid,$z)'><i class='fa fa-user-circle text-muted'></i></td>";
            }
        }
        echo $dsp;
    }
    # Wieviele Einsätze hatte ein Mitarbeiter an einer Station
    public function getMaAnzEinsatzStation($uid,$sid): int
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM c_person2station WHERE sid = '$sid' AND uid = '$uid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl;
        } else {
            return 0;
        }
    }
    # Wieviele Stationen kann der Mitarbeiter
    public function getAnzStationMa($uid)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM tmp_station WHERE uid = '$uid' ";
        $sql .= "AND sess = '{$_SESSION['sess']}' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl / 3;
        }
    }
    # Anzahl der Einätze eines Mitarbeiters
    public function getEinsatzMA($uid)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM tmp_rotationsplan WHERE uid = '$uid' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND sess = '{$_SESSION['sess']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl;
        }
    }
    # Abrufen aller Einsätze an der Station aus der Qualitabelle
    public function getAnzahlEinsatzStation($sid)
    {
        $sql = "SELECT * FROM c_qualifikation WHERE sid = '$sid' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND status IS NULL ";
        $sql .= "ORDER BY anzahl, rand()";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
    }
    # Mitarbeiter in der ausgewählten Zeitschiene noch frei?
    public function getFreiMaZeit($uid,$zeitschiene): bool
    {
        $sql = "SELECT id FROM tmp_rotationsplan WHERE uid = '$uid' ";
        $sql.= "AND zeitschiene = '$zeitschiene' ";
        $sql.= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "AND sess = '{$_SESSION['sess']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    # Mitarbeiter anwesend
    public function getAnwesendMa($uid): bool
    {
        $sql = "SELECT id FROM c_anwesenheit WHERE uid = '$uid' ";
        $sql .= "AND datum = '{$_SESSION['wrk']['datum']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    # Mitarbeiter in der Zeitschiene noch frei
    public function getMaZeitschiene($uid,$zeitschiene)
    {
        $sql = "SELECT id FROM tmp_rotationsplan WHERE uid = '$uid' ";
        $sql.= "AND zeitschiene = '$zeitschiene' ";
        $sql.= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "AND sess = '{$_SESSION['sess']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ)->id;
        }
    }
    # Qualifikation des Mitarbeiters
    public function getAnzahlQualiMa($uid): int
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM c_qualifikation WHERE uid = '$uid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl;
        } else {
            return 0;
        }
    }
    # Summe aller Einsätze
    public function getAnzahlEinsatzGesamt($uid): int
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM c_person2station WHERE uid = '$uid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl;
        } else {
            return 0;
        }
    }
    # Training
    public function getTrainingStation($uid,$sid): bool
    {
        $sql = "SELECT id FROM c_matrix_training WHERE uid = '$uid' AND sid = '$sid' AND status IS NULL";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    # Alle Qualifikationen eines Mitarbeiters
    public function getAllQuali($uid): bool|array
    {
        $sql = "SELECT * FROM c_qualifikation WHERE uid = '$uid' AND status IS NULL";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
        return false;
    }
    # ANzahl Einsätze an Station
    public function getAnzahlEinsatz($uid, $sid)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM c_person2station WHERE sid = '$sid' AND uid = '$uid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl;
    }
    # ANzahl Einsätze an Station
    public function getAnzahlEinsatzZeitraum($uid, $sid, $start, $ende)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM c_person2station WHERE sid = '$sid' AND uid = '$uid' AND (datum BETWEEN '$start' AND '$ende')";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl;
    }
    # Stationen des User aus temporärer Tabelle abrufen
    public function getStationenMa($uid)
    {
        $sql = "SELECT sid,anzahl FROM tmp_station WHERE uid = '$uid' ";
        $sql.= "AND sess = '{$_SESSION['sess']}' ";
        $sql.= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "GROUP BY sid ";
        $sql.= "ORDER BY anzahl, rand() ";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }
    }
    # Wie oft ware der Mitarbeiter anwesend
    public function getSummeAnwesend($uid)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM c_anwesenheit WHERE uid = '$uid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl;
    }


    # Anwesenheit Mitarbeiter
    public function setCheckbox($uid): bool
    {
        $sql = "SELECT id FROM c_anwesenheit WHERE uid = '$uid' ";
        $sql .= "AND datum = '{$_SESSION['wrk']['datum']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    # Anwesenheit speichern und den Rotationsplan erstellen
    public function setAnwesenheit($key, $station)
    {
        # Ist der User schon anwesend ?
        $sql = "SELECT id FROM c_anwesenheit WHERE datum = '{$_SESSION['wrk']['datum']}' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND sid = '$station' ";
        $sql .= "AND uid = '$key'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if ($stmt->rowCount() == 0) {
            $sql = "INSERT INTO c_anwesenheit SET datum = '{$_SESSION['wrk']['datum']}', ";
            $sql .= "uid = '$key', ";
            $sql .= "abteilung = '{$_SESSION['user']['wrk_abteilung']}', ";
            $sql .= "sid = '$station', ";
            $sql .= "schicht = '{$_SESSION['user']['wrk_schicht']}', ";
            $sql.= "zs1 = '1', zs2 = '1', zs3 = '1' ";
            $stmt = $this->db()->prepare($sql);
            $stmt->execute();
            return $key;
        }
    }
    # Mitarbeiter zu den qualifizierten Stationen schreiben
    public function setTmpUser2Station($sid, $uid, $einsaetze, $zeitschiene)
    {
        $sql = "INSERT INTO tmp_station SET sid = '$sid', ";
        $sql .= "uid = '$uid', ";
        $sql .= "anzahl = '$einsaetze', ";
        $sql .= "abteilung = '{$_SESSION['user']['wrk_abteilung']}', ";
        $sql .= "schicht = '{$_SESSION['user']['wrk_schicht']}', ";
        $sql .= "zeitschiene = '$zeitschiene', ";
        $sql .= "sess = '{$_SESSION['sess']}'";
        // echo "<small class='font-size-10'>$sql</small><br>";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
    }
    # Temporären Eintrag speichern
    public function setTmpAnzStation($sid,$anzahl)
    {
        $sql = "INSERT INTO tmp_anzahl_station SET sid = '$sid', ";
        $sql.= "anzahl = '$anzahl', ";
        $sql.= "abteilung = '{$_SESSION['user']['wrk_abteilung']}', ";
        $sql.= "schicht = '{$_SESSION['user']['wrk_schicht']}' , ";
        $sql.= "sess = '{$_SESSION['sess']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
    }
    # Temporären Rotationsplan erstellen
    public function setTmpRotationsplan($sid, $zeitschiene, $mitarbeiter, $anzahl,$uid)
    {
        $sql = "INSERT INTO tmp_rotationsplan SET sess = '{$_SESSION['sess']}', ";
        $sql.= "abteilung = '{$_SESSION['user']['wrk_abteilung']}', ";
        $sql.= "schicht = '{$_SESSION['user']['wrk_schicht']}', ";
        $sql.= "sid = '$sid', ";
        $sql.= "zeitschiene = '$zeitschiene', ";
        $sql.= "mitarbeiter = '$mitarbeiter', ";
        $sql.= "anzahl = '$anzahl', ";
        $sql.= "uid = '$uid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
    }
    # Temporären Rotationsplan aktualisieren
    public function setUpdateTmpRotationsplan($uid, $sid, $zeitschiene, $mitarbeiter)
    {
        $sql = "UPDATE tmp_rotationsplan SET uid = '$uid' WHERE sid = '$sid' ";
        $sql .= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql .= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql .= "AND sess = '{$_SESSION['sess']}' ";
        $sql .= "AND mitarbeiter = '$mitarbeiter' ";
        $sql .= "AND zeitschiene = '$zeitschiene' ";
        $sql .= "LIMIT 1";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
    }
    # Variable Mitarbeiter in temporären Rotationsplan schreiben
    public function setInsertTmpRotationsplan($uid,$sid,$zeitschiene,$mitarbeiter)
    {
        $sql = "INSERT INTO tmp_rotationsplan SET uid = '$uid', sid = '$sid', ";
        $sql .= "abteilung = '{$_SESSION['user']['wrk_abteilung']}', ";
        $sql .= "schicht = '{$_SESSION['user']['wrk_schicht']}', ";
        $sql .= "sess = '{$_SESSION['sess']}', ";
        $sql .= "mitarbeiter = '$mitarbeiter', ";
        $sql .= "zeitschiene = '$zeitschiene' ";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
    }
    # Anzahl der möglichen Stationen in temporäre Tabelle schreiben
    public function setSummeStationMa($uid, $anzahl)
    {
        $sql = "INSERT INTO tmp_anzahl_user SET uid = '$uid', ";
        $sql .= "anzahl = '$anzahl', ";
        $sql .= "abteilung = '{$_SESSION['user']['wrk_abteilung']}', ";
        $sql .= "schicht = '{$_SESSION['user']['wrk_schicht']}', ";
        $sql .= "sess = '{$_SESSION['sess']}'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
    }
    # Qualifikation anpassen (hochzählen)
    public function setUpdateQualifikation($uid, $sid)
    {
        $sql = "UPDATE c_qualifikation SET anzahl = anzahl + 1 ";
        $sql .= "WHERE uid = '$uid' ";
        $sql .= "AND sid = '$sid' ";
        $sql .= "LIMIT 1";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
    }
    # Rotationsplan aus temporären Tabellen erstellen
    public function setRotationsplan($datum)
    {
        # Daten des temporären Rotationsplan abrufen
        $sql = "SELECT * FROM tmp_rotationsplan ";
        $sql.= "WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "AND sess = '{$_SESSION['sess']}'";
        $a = $this->db()->prepare($sql);
        $a->execute();
        if($a->rowCount() > 0){
            $b = $a->fetchAll(\PDO::FETCH_OBJ);
            foreach($b as $c){
                $req = "SELECT id FROM c_person2station WHERE uid = '$c->uid' ";
                $req .= "AND sid = '$c->sid' AND datum = '$datum' AND abteilung = '$c->abteilung' AND schicht = '$c->schicht'";
                $d = $this->db()->prepare($req);
                $d->execute();
                if($d->rowCount() == 0){
                    # Training
                    $training = ($this->getTrainingStation($c->uid,$c->sid)) ? 1 : 0;
                    $e = "INSERT INTO c_person2station SET ";
                    $e .= "uid = '$c->uid', sid = '$c->sid', zeitschiene = '$c->zeitschiene', datum = '$datum', ";
                    $e .= "mitarbeiter = '$c->mitarbeiter', user = '{$_SESSION['user']['dbname']}', eintrag = now(), ";
                    $e .= "abteilung = '{$_SESSION['user']['wrk_abteilung']}', schicht = '{$_SESSION['user']['wrk_schicht']}', training = '$training'";
                    $this->db()->prepare($e)->execute();
                    $f = "INSERT INTO c_person2station_archiv SET ";
                    $f .= "uid = '$c->uid', sid = '$c->sid', zeitschiene = '$c->zeitschiene', datum = '$datum', ";
                    $f .= "mitarbeiter = '$c->mitarbeiter', user = '{$_SESSION['user']['dbname']}', eintrag = now(), ";
                    $f .= "abteilung = '{$_SESSION['user']['wrk_abteilung']}', schicht = '{$_SESSION['user']['wrk_schicht']}', training = '$training'";
                    $this->db()->prepare($f)->execute();
                    # Datensatz aus temporärer Tabelle löschen
                    $this->db()->prepare("DELETE FROM tmp_rotationsplan WHERE id = '$c->id' LIMIT 1")->execute();
                } # $d = 0
            } # $b as $c
        } # temporäre Daten vorhanden
    }


    # Eintrag aus der temporären Tabelle löschen
    public function deleteTmpStation($uid,$sid,$zeitschiene)
    {
        # Mitarbeiter löschen
        $this->db()->prepare("DELETE FROM tmp_station WHERE uid = '$uid' AND sess = '{$_SESSION['sess']}'")->execute();
        # Station löschen
        $this->db()->prepare("DELETE FROM tmp_station WHERE sid = '$sid' AND zeitschiene = '$zeitschiene' AND sess = '{$_SESSION['sess']}'")->execute();
    }
    # Station aus der temporären Tabelle löschen
    public function deleteTmpAnzStation($sid)
    {
        $this->db()->prepare("DELETE FROM tmp_anzahl_station WHERE sid = '$sid' AND sess = '{$_SESSION['sess']}'")->execute();
    }
    # Eintrag aus der temporären Tabelle löschen
    public function deleteEintrag($uid,$sid,$zeitschiene)
    {
        # Mitarbeiter löschen
        $this->db()->prepare("DELETE FROM tmp_station WHERE uid = '$uid' AND zeitschiene = '$zeitschiene'")->execute();
        # Station löschen
        $this->db()->prepare("DELETE FROM tmp_station WHERE sid = '$sid' AND zeitschiene = '$zeitschiene' LIMIT 1")->execute();
    }
    # Mitarbeiter mit 3 Einsätzen löschen
    public function deleteMa3($uid)
    {
        if ($uid != 0):
            $a = $this->getEinsatzMA($uid);
            if ($a == 3):
                $sql = "DELETE FROM tmp_anzahl_user WHERE uid = '$uid'";
                $stmt = $this->db()->prepare($sql);
                $stmt->execute();
            endif;
        endif;
    }
    # Temporäre Dateien löschen
    public function deleteTmpTables($datum)
    {
        # Daten des temporären Rotationsplan abrufen
        $sql = "SELECT * FROM tmp_rotationsplan ";
        $sql.= "WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "AND sess = '{$_SESSION['sess']}'";
        $a = $this->db()->prepare($sql);
        $a->execute();
        if($a->rowCount() > 0) {
            $b = $a->fetchAll(\PDO::FETCH_OBJ);
            foreach ($b as $c) {
                $e = "INSERT INTO c_person2station SET ";
                $e .= "uid = '$c->uid', sid = '$c->sid', zeitschiene = '$c->zeitschiene', datum = '$datum', ";
                $e .= "mitarbeiter = '$c->mitarbeiter', user = '{$_SESSION['user']['dbname']}', eintrag = now(), ";
                $e .= "abteilung = '{$_SESSION['user']['wrk_abteilung']}', schicht = '{$_SESSION['user']['wrk_schicht']}', training = '0'";
                $this->db()->prepare($e)->execute();
                $f = "INSERT INTO c_person2station_archiv SET ";
                $f .= "uid = '$c->uid', sid = '$c->sid', zeitschiene = '$c->zeitschiene', datum = '$datum', ";
                $f .= "mitarbeiter = '$c->mitarbeiter', user = '{$_SESSION['user']['dbname']}', eintrag = now(), ";
                $f .= "abteilung = '{$_SESSION['user']['wrk_abteilung']}', schicht = '{$_SESSION['user']['wrk_schicht']}', training = '0'";
                $this->db()->prepare($f)->execute();
                # Datensatz aus temporärer Tabelle löschen
                $this->db()->prepare("DELETE FROM tmp_rotationsplan WHERE id = '$c->id' LIMIT 1")->execute();
            }
        }
        $this->db()->prepare("DELETE FROM tmp_station WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' AND schicht = '{$_SESSION['user']['wrk_schicht']}' AND sess = '{$_SESSION['sess']}'")->execute();
        $this->db()->prepare("DELETE FROM tmp_anzahl_station WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' AND schicht = '{$_SESSION['user']['wrk_schicht']}' AND sess = '{$_SESSION['sess']}'")->execute();
        $this->db()->prepare("DELETE FROM tmp_anzahl_user WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' AND schicht = '{$_SESSION['user']['wrk_schicht']}' AND sess = '{$_SESSION['sess']}'")->execute();
    }

    # Rote Station
    public function getRedStation(): bool|array
    {
        $sql = "SELECT id FROM b_station WHERE ergo = 'danger' ";
        $sql.= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND status = '1'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        } else {
            return false;
        }
    }

    # Mitarbeiter auf roter Station ?
    public function getMaRedStation($uid): int
    {
        $stn = $this->getRedStation();
        $counter = 0;
        if($stn){
            foreach($stn as $station){
                $sql = "SELECT id FROM c_person2station WHERE uid = '$uid' ";
                $sql.= "AND sid = '$station->id' ";
                $sql.= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
                $sql.= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
                $sql.= "AND datum = '{$_SESSION['wrk']['datum']}'";
                $stmt = $this->db()->prepare($sql);
                $stmt->execute();
                if($stmt->rowCount() > 0) {
                    $counter++;
                }
            }
        }
        return $counter;
    }

    # Mitarbeiter im Training (array)
    public function getStnMaTraining($uid): bool|array
    {
        $sql = "SELECT sid FROM c_matrix_training WHERE uid = '$uid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        } else {
            return false;
        }
    }

    # Mitarbeiter im Handicap (array)
    public function getStnMaHandicap($uid): bool|array
    {
        $sql = "SELECT sid FROM c_handicap WHERE uid = '$uid'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        } else {
            return false;
        }
    }


    # Wieviele Mitarbeiter können die Station
    public function getAnzahlQualiStation($sid)
    {
        $sql = "SELECT COUNT(id) AS anzahl FROM c_qualifikation WHERE sid = '$sid' ";
        $sql.= "AND abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0){
            return $stmt->fetch(\PDO::FETCH_OBJ)->anzahl;
        } else {
            return false;
        }
    }

    # Nächster freier Tag

    /**
     * @throws Exception
     */
    public function getNewDate(): string
    {
        $sql = "SELECT datum FROM c_person2station WHERE abteilung = '{$_SESSION['user']['wrk_abteilung']}' ";
        $sql.= "AND schicht = '{$_SESSION['user']['wrk_schicht']}' ";
        $sql.= "ORDER BY datum DESC LIMIT 1";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        if($stmt->rowCount() > 0) {
            $datum = $stmt->fetch(\PDO::FETCH_OBJ)->datum;
            $a = new DateTime($datum);
            $b = $a->modify('+1 day');
            return date_format($b, "Y-m-d");
        } else {
            return DATE('Y-m-d');
        }
    }
































    # Cronjob
    public function cronjob()
    {
        # Notwendige Parameter
        $heuteSQL = DATE('Y-m-d');

        # Aktionen, die durchgeführt werden müssen
        # Alle Mitarbeiter durchgehen (unabhängig der Abteilung / Schicht)
        $sql = "SELECT * FROM b_mitarbeiter WHERE status = '1'";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute();
        $ma = $stmt->fetchAll(\PDO::FETCH_OBJ);
        foreach($ma AS $user){
            # Handicap
            $a = "SELECT * FROM c_handicap WHERE uid = '$user->id'";
            $at = $this->db()->prepare($a);
            $at->execute();
            if($at->rowCount() > 0) {
                $stn = $at->fetchAll(\PDO::FETCH_OBJ);
                foreach($stn AS $station){
                    # Qualifikation
                    if($station->ende < $heuteSQL){
                        # Qualifikation updaten
                        $b = "UPDATE c_qualifikation SET status = NULL WHERE uid = '$user->id' AND sid = '$station->sid' LIMIT 1";
                        echo $b."<br>";
                        $bt = $this->db()->prepare($b)->execute();
                    }
                }
            }
            # Abwesenheit
            $c = "SELECT * FROM c_abwesenheit WHERE uid = '$user->id'";
            $ct = $this->db()->prepare($a);
            $ct->execute();
            if($ct->rowCount() > 0) {
                $stn = $ct->fetchAll(\PDO::FETCH_OBJ);
                foreach($stn AS $station){
                    # Qualifikation
                    if($station->ende < $heuteSQL){
                        # Qualifikation updaten
                        $d = "UPDATE c_qualifikation SET status = NULL WHERE uid = '$user->id' AND sid = '$station->sid' LIMIT 1";
                        echo $d."<br>";
                        $bt = $this->db()->prepare($d)->execute();
                    }
                }
            }
        }
    }
}