<?php
/** (c) Joachim Göddel . RLMS */

namespace App\Pages\Rotationsplan\MVC;

use App\App\AbstractMVC\AbstractController;
use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Home\IndexDatabase;
use App\Pages\Rotationsplan\RotationsplanDatabase;
use DateTime;
use Exception;
use mysql_xdevapi\Warning;


class RotationsplanController extends AbstractController
{

    private IndexDatabase $indexDatabase;
    private RotationsplanDatabase $rotationsplanDatabase;
    private AdministrationDatabase $administrationDatabase;

    public function __construct(
        IndexDatabase         $indexDatabase,
        RotationsplanDatabase $rotationsplanDatabase,
        AdministrationDatabase $administrationDatabase
    )
    {
        $this->indexDatabase = $indexDatabase;
        $this->rotationsplanDatabase = $rotationsplanDatabase;
        $this->administrationDatabase = $administrationDatabase;
    }

    public function ebene()
    {
        # Berechtigungsebene
        return $this->indexDatabase->getEbene('/rotationsplan');
    }

    public function index()
    {
        # Berechtigungsebene
        $ebene = $this->indexDatabase->getEbene('/rotationsplan');
        # Untermenü erstellen
        $_SESSION['menu']['rotationsplan'] = $this->administrationDatabase->getAllPages('1', '1', '10');
        # Eingetragene Pläne
        $_SESSION['menu']['verwaltung'] = $this->rotationsplanDatabase->getPlan();
        # Erforderliche Mitarbeiter
        $erforderliche_mitarbeiter = $this->rotationsplanDatabase->getPersonalAbteilung();
        # Anwesendes Personal
        $mitarbeiter_schicht = $this->rotationsplanDatabase->getPersonalAnwesend();
        # Eindeutige Session
        $_SESSION['sess'] = $_SESSION['seite']['sess'];
        # Sprachdaten laden
        $this->indexDatabase->i18n();
        # Übergabe
        $this->pageload("Rotationsplan", "index", [
            'ebene' => $ebene,
            'erforderliche_mitarbeiter' => $erforderliche_mitarbeiter,
            'mitarbeiter_schicht' => $mitarbeiter_schicht
        ]);
    }

    # Prüfen, ob bereits ein Rotationsplan eingetragen ist 
    public function planExists()
    {
        echo $this->rotationsplanDatabase->planExistsDate($_POST['datum']);
    }

    # Anzeige der Anwesenheitstabellen
    public function tableAnwesenheit()
    {
        # Anzahl Mitarbeiter auf der Schicht
        $mitarbeiter = $this->rotationsplanDatabase->getPersonalSchicht();
        # Tabelle splitten
        $left = ceil($mitarbeiter / $_SESSION['parameter']['zeitschienen']);
        $center = $left * 2;
        $right = $mitarbeiter - $center;
        # Welche Tabelle wird angezeigt
        if ($_POST['tabelle'] == 1) $tb = "0, $left";
        if ($_POST['tabelle'] == 2) $tb = "$left, $left";
        if ($_POST['tabelle'] == 3) $tb = "$center, $right";
        # Mitarbeiter in Variable schreiben
        $maTable = $this->rotationsplanDatabase->getPersonalTable($tb);

        # Übergabe
        $this->pageload("Rotationsplan", "includes/getTableAnwesenheit", [
            'maTable' => $maTable
        ]);
    }
    # Verwaltung Rotationsplan

    /**
     * @throws Exception
     */
    public function verwaltung()
    {
        # Datum ändern
        if($_POST) $_SESSION['wrk']['datum'] = $_POST['datum'];
        # Berechtigungsebene
        $ebene = $this->indexDatabase->getEbene('/rotationsplan');
        # Zeiten in Array schreiben
        $this->rotationsplanDatabase->zeitschiene2Array();
        # In welcher Zeitschiene bewege ich mich ?
        $zzone = Functions::getZeitschieneSchicht('' . $_SESSION['wrk']['datum'] . '');
        # Mitarbeiter der Schicht
        $ma = $this->rotationsplanDatabase->getMitarbeiterSchicht(500);
        # Eingetragene Pläne
        $_SESSION['menu']['verwaltung'] = $this->rotationsplanDatabase->getPlan();

        $this->pageload("Rotationsplan","verwaltung",[
            'ebene' => $ebene,
            'zzone' => $zzone,
            'ma' => $ma
        ]);
    }

    /**
     * @throws Exception
     */
    public function rotationsplan()
    {
        # Zeiten in Array schreiben
        $this->rotationsplanDatabase->zeitschiene2Array();
        $zzone = Functions::getZeitschieneSchicht('' . $_SESSION['wrk']['datum'] . '');
        $ma = $this->rotationsplanDatabase->getMitarbeiterSchicht(500);
        $_SESSION['menu']['verwaltung'] = $this->rotationsplanDatabase->getPlan();
        $this->pageload("Rotationsplan", "rotationsplan", [
            'zzone' => $zzone,
            'ma' => $ma
        ]);
    }
    # Prüfen, ob der Mitarbeiter im aktuellen Plan vorhanden ist
    public function checkRfid()
    {
        $rfid = trim($_POST['rfid']);
        echo $this->rotationsplanDatabase->getMitarbeiterID($rfid);
    }
    public function rfid()
    {
        $id = $_POST['id'];
        $this->pageload("Rotationsplan", "includes/rfid", [
            'id' => $id
        ]);
    }

    # Archiv Rotationsplan

    /**
     * @throws Exception
     */
    public function archiv()
    {
        # Datum ändern
        if($_POST) $_SESSION['wrk']['datum'] = $_POST['datum'];
        # Berechtigungsebene
        $ebene = $this->indexDatabase->getEbene('/rotationsplan');
        # Zeiten in Array schreiben
        $this->rotationsplanDatabase->zeitschiene2Array();
        # In welcher Zeitschiene bewege ich mich ?
        $zzone = Functions::getZeitschieneSchicht('' . $_SESSION['wrk']['datum'] . '');
        # Mitarbeiter der Schicht
        $ma = $this->rotationsplanDatabase->getMitarbeiterSchicht(500);
        # Eingetragene Pläne
        $_SESSION['menu']['verwaltung'] = $this->rotationsplanDatabase->getPlan();
        # Plan für dieses Datum ?
        $plan = $this->rotationsplanDatabase->planExistsDate($_SESSION['wrk']['datum']);

        $this->pageload("Rotationsplan","archiv",[
            'ebene' => $ebene,
            'zzone' => $zzone,
            'ma' => $ma,
            'plan' => $plan
        ]);
    }

    /**
     * @throws Exception
     */
    public function mitarbeiter()
    {
        # Berechtigungsebene
        $ebene = $this->indexDatabase->getEbene('/rotationsplan');
        # Mitarbeiter
        $ma = $this->rotationsplanDatabase->getMitarbeiterSchicht(500);

        $this->pageload("Rotationsplan","mitarbeiter",[
            'ebene' => $ebene,
            'ma' => $ma
        ]);
    }
    # Mitarbeiter Details
    public function mitarbeiterDetails()
    {
        # Berechtigungsebene
        $ebene = $this->indexDatabase->getEbene('/rotationsplan');
        # Mitarbeiterdetails
        $id = Functions::decrypt($_GET['id']);
        $ma = $this->rotationsplanDatabase->getMitarbeiterDetails($id);
        # Einsatz
        $this->rotationsplanDatabase->getSumEinsatzMa($id);
        # Übergabe
        $this->pageload("Rotationsplan","mitarbeiterDetails",[
            'ebene' => $ebene,
            'ma' => $ma,
            'id' => $_GET['id']
        ]);
    }

    # Tabelle Personal Rotationsplan Verwaltung

    /**
     * @throws Exception
     */
    public function tablePersonal()
    {
        # Mitarbeiter der Schicht
        $ma = $this->rotationsplanDatabase->getMitarbeiterSchicht(500);
        # In welcher Zeitschiene bewege ich mich ?
        $zzone = Functions::getZeitschieneSchicht('' . $_SESSION['wrk']['datum'] . '');
        # Zeitschiene
        $zschiene = ($zzone == 1) ? array(1, 2, 3) : array(4, 5, 6);
        # User Id
        $uid = (!$_POST) ? 0 : $_POST['uid'];
        $sid = (!$_POST) ? 0 : $_POST['sid'];
        # Übergabe
        $this->pageload("Rotationsplan", "includes/getTablePersonal", [
            'ma' => $ma,
            'zschiene' => $zschiene,
            'uid' => $uid,
            'sid' => $sid
        ]);
    }
    # Tabelle Personal Rotationsplan Verwaltung

    /**
     * @throws Exception
     */
    public function tablePersonalArchiv()
    {
        # Mitarbeiter der Schicht
        $ma = $this->rotationsplanDatabase->getMitarbeiterSchicht(500);
        # In welcher Zeitschiene bewege ich mich ?
        $zzone = Functions::getZeitschieneSchicht('' . $_SESSION['wrk']['datum'] . '');
        # Zeitschiene
        $zschiene = ($zzone == 1) ? array(1, 2, 3) : array(4, 5, 6);
        # User Id
        $uid = (!$_POST) ? 0 : $_POST['uid'];
        $sid = (!$_POST) ? 0 : $_POST['sid'];
        # Übergabe
        $this->pageload("Rotationsplan", "includes/getTablePersonalArchiv", [
            'ma' => $ma,
            'zschiene' => $zschiene,
            'uid' => $uid,
            'sid' => $sid
        ]);
    }
    # Tabelle Personal Rotationsplan Zeitschiene
    public function tableZeitschiene()
    {
        # Stationen der Abteilung
        $stn = $this->rotationsplanDatabase->getStationAbteilung();
        # In welcher Zeitschiene bewege ich mich ?
        $zzone = Functions::getZeitschieneSchicht('' . $_SESSION['wrk']['datum'] . '');
        # Zeitschiene
        $zschiene = ($zzone == 1) ? array(1, 2, 3) : array(4, 5, 6);
        # POST Parameter
        $zeitschiene = $_POST['zeitschiene'];
        $datum = $_POST['datum'];
        $uid = $_POST['uid'];
        # Übergabe
        $this->pageload("Rotationsplan", "includes/getTableZeitschiene", [
            'stn' => $stn,
            'zschiene' => $zschiene,
            'datum' => $datum,
            'zeitschiene' => $zeitschiene,
            'uid' => $uid
        ]);
    }
    # Tabelle Personal Rotationsplan Zeitschiene (Archiv)
    public function tableZeitschieneArchiv()
    {
        # Stationen der Abteilung
        $stn = $this->rotationsplanDatabase->getStationAbteilung();
        # In welcher Zeitschiene bewege ich mich ?
        $zzone = Functions::getZeitschieneSchicht('' . $_SESSION['wrk']['datum'] . '');
        # Zeitschiene
        $zschiene = ($zzone == 1) ? array(1, 2, 3) : array(4, 5, 6);
        # POST Parameter
        $zeitschiene = $_POST['zeitschiene'];
        $datum = $_POST['datum'];
        $uid = $_POST['uid'];
        # Übergabe
        $this->pageload("Rotationsplan", "includes/getTableZeitschieneArchiv", [
            'stn' => $stn,
            'zschiene' => $zschiene,
            'datum' => $datum,
            'zeitschiene' => $zeitschiene,
            'uid' => $uid
        ]);
    }

    # Anwesenheit speichern (Seite anzeigen)

    /**
     * @throws Exception
     */
    public function setAnwesenheit()
    {
        # Berechtigungsebene
        $ebene = $this->indexDatabase->getEbene('/rotationsplan');
        # Plan vorhanden ?
        $pln = $this->rotationsplanDatabase->planExistsDate($_POST['newdate']);
        # Springer
        $springer = $this->rotationsplanDatabase->getKA();
        # Zeitzone
        $zzone = $this->getZeitschieneSchicht($_SESSION['wrk']['datum']);
        # Zeitschiene
        $zschiene = ($zzone == 1) ? $zschiene = array(1,2,3) : $zschiene = array(4,5,6);
        # Endkontrolle
        $idEndkontrolle = $this->rotationsplanDatabase->getStationID('Endkontrolle');
        # Übergabe
        $this->pageload("Rotationsplan", "setAnwesenheit", [
            'post' => $_POST,
            'ebene' => $ebene,
            'pln' => $pln,
            'springer' => $springer,
            'zzone' => $zzone,
            'zschiene' => $zschiene,
            'idEndkontrolle' => $idEndkontrolle
        ]);
    }

    # Ermitteln, welche Schicht im Moment arbeiten sollte

    /**
     * @throws Exception
     */
    public function getZeitschieneSchicht($datum): int
    {
        # Kalenderwoche
        $date = new DateTime('' . $datum . '');
        $kw = $date->format('W');
        # Gerade oder ungerade KW
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

    # Mitarbeiter auf neue Station schreiben
    public function setMitarbeiterStation()
    {
        $this->rotationsplanDatabase->updateRotationsplan($_POST['uid'],$_POST['guid'],$_POST['sid'],$_POST['springer'],$_POST['zeitschiene'],$_POST['mitarbeiter']);
    }
    # Abteilung wechseln
    public function setAbteilung()
    {
        $_SESSION['user']['wrk_abteilung'] = $_POST['id'];
    }
    # Schicht wechseln
    public function setSchicht()
    {
        $_SESSION['user']['wrk_schicht'] = $_POST['id'];
    }

    # Mitarbeiter Handicap eintragen
    public function setMitarbeiterHandicap()
    {
        $this->rotationsplanDatabase->setMitarbeiterHandicap($_POST['uid'],$_POST['station'],$_POST['start'],$_POST['ende']);
        # Berechtigungsebene
        $ebene = $this->indexDatabase->getEbene('/rotationsplan');
        # Mitarbeiter
        $ma = $this->rotationsplanDatabase->getMitarbeiterDetails($_POST['id']);

        $this->pageload("Rotationsplan","mitarbeiterDetails",[
            'ebene' => $ebene,
            'ma' => $ma
        ]);
    }

    # Mitarbeiter als Abwesend eintragen
    public function setMitarbeiterAbwesend()
    {
        $this->rotationsplanDatabase->setMitarbeiterAbwesend($_POST['uid'],$_POST['start'],$_POST['ende']);
    }
    # Mitarbeiter als Abwesend eintragen
    public function neuerMitarbeiter()
    {
        $this->rotationsplanDatabase->neuerMitarbeiter($_POST);
    }

    # Mitarbeiter aus einer Zeitschiene im Rotationsplan löschen
    public function deleteMitarbeiterPlan()
    {
        $id = $_POST['id'];
        $springer = $_POST['springer'];
        $this->rotationsplanDatabase->deleteMitarbeiterPlan($id,$springer);
    }

    # Mitarbeiter aus einer Zeitschiene in der Anwesenheit löschen
    public function deleteMitarbeiterAnwesend()
    {
        $uid = $_POST['uid'];
        $z = $_POST['z'];
        $this->rotationsplanDatabase->deleteMitarbeiterAnwesend($uid,$z);
    }

    # Mitarbeiter einer Zeitschiene in der Anwesenheit hinzufügen
    public function setMitarbeiterAnwesend()
    {
        $uid = $_POST['uid'];
        $z = $_POST['z'];
        $this->rotationsplanDatabase->setMitarbeiterAnwesend($uid,$z);
    }

    # Abwesenheit eines Mitarbeiters löschen
    public function deleteAbwesend()
    {
        $this->rotationsplanDatabase->deleteAbwesend($_POST['uid']);
        # Berechtigungsebene
        $ebene = $this->indexDatabase->getEbene('rotationsplan');
        # Mitarbeiter
        $ma = $this->rotationsplanDatabase->getMitarbeiterDetails($_POST['id']);

        $this->pageload("Rotationsplan","mitarbeiterDetails",[
            'ebene' => $ebene,
            'ma' => $ma
        ]);
    }

    # Qualifikation löschen
    public function deleteQualiMa()
    {
        $this->rotationsplanDatabase->deleteQualiMa($_POST['sid'],$_POST['uid']);
    }

    # Anzeige: zeigt ein leeres Kommentar-Formular an
    public function getFormularQualiMa()
    {
        $sid = $_POST['sid'];
        $uid = $_POST['uid'];
        $this->pageLoad("Rotationsplan", "includes/getFormularQualiMa", [
            'sid' => $sid,
            'uid' => $uid
        ]);
    }

    # Qualifikation eines Mitarbeiters eintragen
    public function setQualiMa()
    {
        $this->rotationsplanDatabase->setQualiMa($_POST['sid'],$_POST['uid']);
        # Berechtigungsebene
        $ebene = $this->indexDatabase->getEbene('/rotationsplan');
        # Mitarbeiter
        $ma = $this->rotationsplanDatabase->getMitarbeiterDetails($_POST['uid']);

        $this->pageload("Rotationsplan","mitarbeiterDetails",[
            'ebene' => $ebene,
            'ma' => $ma
        ]);
    }

    # Training eines Mitarbeiters eintragen
    public function setTrainingMa()
    {
        $this->rotationsplanDatabase->setTrainingMa($_POST['sid'],$_POST['uid']);
        # Berechtigungsebene
        $ebene = $this->indexDatabase->getEbene('/rotationsplan');
        # Mitarbeiter
        $ma = $this->rotationsplanDatabase->getMitarbeiterDetails($_POST['uid']);

        $this->pageload("Rotationsplan","mitarbeiterDetails",[
            'ebene' => $ebene,
            'ma' => $ma
        ]);
    }

    # Mitarbeiter löschen
    public function deleteMa()
    {
        $this->rotationsplanDatabase->deleteMa($_POST['id']);
    }

    # Mitarbeiter Passwort ändern
    public function setMitarbeiterPassword()
    {
        $this->rotationsplanDatabase->setMitarbeiterPassword($_POST['uid'],$_POST['password'],$_POST['rfid']);
    }


    # Stationen
    public function stationen()
    {
        # Berechtigungsebene
        $ebene = $this->indexDatabase->getEbene('/rotationsplan');
        # Stationen
        $stn = $this->rotationsplanDatabase->getStationAbteilung();

        $this->pageload("Rotationsplan","stationen",[
            'ebene' => $ebene,
            'stn' => $stn
        ]);
    }
    # Stationen (Details)
    public function stationDetails()
    {
        # Ebene
        $ebene = $this->ebene();
        # Alle Mitarbeiter einer Abteilung / Schicht
        $ma = $this->rotationsplanDatabase->getMitarbeiterSchicht(500);
        # ID
        $id = Functions::decrypt($_GET['id']);
        $station = $this->rotationsplanDatabase->getStation($id);
        # Übergabe
        $this->pageload("Rotationsplan","stationDetails",[
            'ebene' => $ebene,
            'ma' => $ma,
            'station' => $station
        ]);
    }
    # Stationen
    public function changeStation()
    {
        # Details Station
        $this->rotationsplanDatabase->changeStation($_POST);
    }
    # Stationen
    public function neueStation()
    {
        # Details Station
        $this->rotationsplanDatabase->neueStation($_POST);
    }
    # Qualifikation via Station
    public function setQualiStation()
    {
        # Details Station
        $this->rotationsplanDatabase->setQualiStation($_POST);
    }


    # Auswertungen
    public function auswertung()
    {
        # Berechtigungsebene
        $ebene = $this->indexDatabase->getEbene('/rotationsplan');
        # Alle Mitarbeiter einer Abteilung / Schicht
        $ma = $this->rotationsplanDatabase->getMitarbeiterSchicht(500);
        # Stationen
        $stn = $this->rotationsplanDatabase->getStationAbteilung();
        # Übergabe
        $this->pageload("Rotationsplan","auswertung",[
            'ebene' => $ebene,
            'ma' => $ma,
            'stn' => $stn
        ]);
    }
    # Vergleich
    public function vergleich()
    {
        # Berechtigungsebene
        $ebene = $this->indexDatabase->getEbene('/rotationsplan');
        # Alle Mitarbeiter einer Abteilung / Schicht
        $ma = $this->rotationsplanDatabase->getMitarbeiterSchicht(500);
        # Stationen
        $stn = $this->rotationsplanDatabase->getStationAbteilung();
        # Übergabe
        $this->pageload("Rotationsplan","vergleich",[
            'ebene' => $ebene,
            'ma' => $ma,
            'stn' => $stn
        ]);
    }
    # Vergleich
    public function vergleichMitarbeiter()
    {
        $ma = $_POST['ma'];
        # Übergabe
        $this->pageload("Rotationsplan","includes/getMitarbeiterVergleich",[
            'ma' => $ma
        ]);
    }
    public function getVergleich()
    {
        $ma = $_POST['hma'];
        $a = explode(",", $ma);
        # Mitarbeiter abrufen
        foreach($a AS $b){
            $u[] = $this->rotationsplanDatabase->getMitarbeiterDetails($b);
        }
        # Stationen
        $q = $this->rotationsplanDatabase->getStationAbteilung();
        # Übergabe
        $this->pageload("Rotationsplan","includes/getVergleich",[
            'u' => $u,
            'q' => $q,
            'start' => $_POST['start'],
            'ende' => $_POST['ende']
        ]);
    }
    public function getVergleichChart()
    {
        $ma = $_POST['hma'];
        $a = explode(",", $ma);
        # Mitarbeiter abrufen
        foreach($a AS $b){
            $u[] = $this->rotationsplanDatabase->getMitarbeiterDetails($b);
        }
        # Stationen
        $q = $this->rotationsplanDatabase->getStationAbteilung();
        # Übergabe
        $this->pageload("Rotationsplan","includes/getVergleichChart",[
            'u' => $u,
            'q' => $q,
            'start' => $_POST['start'],
            'ende' => $_POST['ende']
        ]);
    }








    # Cronjob
    public function cronjob()
    {
        $this->pageload("Rotationsplan", "cronjob", [

        ]);
    }
}