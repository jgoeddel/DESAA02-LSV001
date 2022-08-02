<?php
/** (c) Joachim Göddel . RLMS */
namespace App\Pages\ChangeManagement\MVC;

use App\Functions\Functions;
use App\Pages\ChangeManagement\ChangeManagementDatabase;

class ChangeManagementController extends \App\App\AbstractMVC\AbstractController
{
    # Construct
    private ChangeManagementDatabase $changeManagementDatabase;

    public function __construct(
        ChangeManagementDatabase $changeManagementDatabase
    ){
        $this->changeManagementDatabase = $changeManagementDatabase;
    }

    # Startseite laden
    public function index()
    {
        # Parameter
        $jahr = $_SESSION['parameter']['jahr'];

        # Einträge (Change Management)
        $cm = $this->changeManagementDatabase->getElements($jahr,8);

        # Übergabe
        $this->pageload("ChangeManagement", "index", [
            'cm' => $cm
        ]);
    }

    # Neuer Auftrag
    public function neu()
    {
        # Übergabe
        $this->pageload("ChangeManagement", "neu", [

            ]);
    }

    # Detailseite laden
    public function details()
    {
        # Parameter
        $jahr = $_SESSION['parameter']['jahr'];
        $id = Functions::decrypt($_GET['id']);
        $cc = $_GET['loc'];
        $_SESSION['wrk']['citycode'] = $cc;

        # Einträge (Change Management)
        $row = $this->changeManagementDatabase->getElement($id);
        $gmas = $this->changeManagementDatabase->getGMAS($id);
        $nael = $this->changeManagementDatabase->getNAEL($id);
        $ersteller = $this->changeManagementDatabase->getErsteller($id);
        $info = $this->changeManagementDatabase->getInfo($id);
        $quelle = $this->changeManagementDatabase->getQuelle();
        $changetype = $this->changeManagementDatabase->getChangeType();
        $partin = $this->changeManagementDatabase->getPartNo($id,'in');
        $partout = $this->changeManagementDatabase->getPartNo($id,'out');
        $deviation = $this->changeManagementDatabase->getDeviation($id);
        $loc = $this->changeManagementDatabase->getLocationInfo($cc);
        $cid = $_GET['id'];
        $tracking = $this->changeManagementDatabase->checkTracking($id);
        $evaluation = $this->changeManagementDatabase->checkEvaluation($id);
        $l = $this->changeManagementDatabase->getLieferantBase($id);
        $lieferant = $this->changeManagementDatabase->getLieferant($l);

        # Übergabe
        $this->pageload("ChangeManagement", "details", [
            'row' => $row,
            'id' => $id,
            'gmas' => $gmas,
            'nael' => $nael,
            'ersteller' => $ersteller,
            'info' => $info,
            'quelle' => $quelle,
            'changetype' => $changetype,
            'partin' => $partin,
            'partout' => $partout,
            'deviation' => $deviation,
            'loc' => $loc,
            'cid' => $cid,
            'tracking' => $tracking,
            'evaluation' => $evaluation,
            'lieferant' => $lieferant
        ]);
    }

    # Evaluation laden
    public function evaluation()
    {
        # Parameter
        $id = Functions::decrypt($_GET['id']);
        $evaluation = $_GET['evaluation'];

        # Einträge (Change Management)
        $row = $this->changeManagementDatabase->getElement($id);
        $ersteller = $this->changeManagementDatabase->getErsteller($id);
        $info = $this->changeManagementDatabase->getInfo($id);

        # Übergabe
        $this->pageload("ChangeManagement", "evaluation", [
            'row' => $row,
            'id' => $id,
            'ersteller' => $ersteller,
            'info' => $info,
            'evaluation' => $evaluation
        ]);
    }

    # vererinfachten Durchlauf durchführen
    public function simpleChange()
    {
        # Parameter
        $bid = $_POST['bid'];
        $antwort = $_POST['antwort'];
        $password = $_POST['password'];
        $this->changeManagementDatabase->simpleChange($bid,$antwort,$password);
    }

    # Tracking laden
    public function tracking()
    {
        # Parameter
        $id = Functions::decrypt($_GET['id']);
        $evaluation = $_GET['tracking'];

        # Einträge (Change Management)
        $row = $this->changeManagementDatabase->getElement($id);
        $ersteller = $this->changeManagementDatabase->getErsteller($id);
        $info = $this->changeManagementDatabase->getInfo($id);

        # Übergabe
        $this->pageload("ChangeManagement", "tracking", [
            'row' => $row,
            'id' => $id,
            'ersteller' => $ersteller,
            'info' => $info,
            'evaluation' => $evaluation
        ]);
    }

    # Tracking laden
    public function nachrichten()
    {
        # Parameter
        $id = Functions::decrypt($_GET['id']);

        # Einträge (Change Management)
        $row = $this->changeManagementDatabase->getElement($id);
        $ersteller = $this->changeManagementDatabase->getErsteller($id);
        $info = $this->changeManagementDatabase->getInfo($id);
        $com = $this->changeManagementDatabase->getComBase($id);

        # Übergabe
        $this->pageload("ChangeManagement", "nachrichten", [
            'row' => $row,
            'id' => $id,
            'ersteller' => $ersteller,
            'info' => $info,
            'com' => $com
        ]);
    }

    # Dateien laden
    public function dateien()
    {
        # Parameter
        $id = Functions::decrypt($_GET['id']);

        # Einträge (Change Management)
        $row = $this->changeManagementDatabase->getElement($id);
        $ersteller = $this->changeManagementDatabase->getErsteller($id);
        $info = $this->changeManagementDatabase->getInfo($id);
        $dateien = $this->changeManagementDatabase->getDateien($id,2);
        $bilder = $this->changeManagementDatabase->getDateien($id,1);

        # Übergabe
        $this->pageload("ChangeManagement", "dateien", [
            'row' => $row,
            'id' => $id,
            'ersteller' => $ersteller,
            'info' => $info,
            'dateien' => $dateien,
            'bilder' => $bilder
        ]);
    }

    # LOP laden
    public function lop()
    {
        # Parameter
        $id = Functions::decrypt($_GET['id']);
        # Einträge (Change Management)
        $row = $this->changeManagementDatabase->getElement($id);
        # Einträge in der Tabelle abrufen
        $lp = $this->changeManagementDatabase->getLops($id);

        # Übergabe
        $this->pageload("ChangeManagement", "lop", [
            'row' => $row,
            'id' => $id,
            'lp' => $lp
        ]);
    }

    # Lop speichern
    public function setLop()
    {
        $this->changeManagementDatabase->setLop($_POST);
    }

    # Lop ändern
    public function changeLop()
    {
        $this->changeManagementDatabase->changeLop($_POST);
    }

    # Meeting laden
    public function meeting()
    {
        # Parameter
        $id = Functions::decrypt($_GET['id']);
        # Einträge (Change Management)
        $row = $this->changeManagementDatabase->getElement($id);
        $ersteller = $this->changeManagementDatabase->getErsteller($id);
        $info = $this->changeManagementDatabase->getInfo($id);

        # Übergabe
        $this->pageload("ChangeManagement", "meeting", [
            'row' => $row,
            'id' => $id,
            'ersteller' => $ersteller,
            'info' => $info
        ]);
    }

    # Meetings laden
    public function getMeetings()
    {
        # Einträge (Change Management)
        $row = $this->changeManagementDatabase->getElement($_POST['id']);
        # Übergabe
        $this->pageload("ChangeManagement", "includes/getMeetings", [
            'id' => $_POST['id'],
            'row' => $row
        ]);
    }
    # Meetingeintrag speichern
    public function setMeeting()
    {
        $post = $_POST;
        $this->changeManagementDatabase->setMeeting($post);
    }
    # Mitarbeiter zu Meeting speichern
    public function setMaMeeting()
    {
        $post = $_POST;
        $this->changeManagementDatabase->setMaMeeting($post);
    }

    # Partno laden
    public function partno()
    {
        # Parameter
        $id = Functions::decrypt($_GET['id']);
        # Einträge (Change Management)
        $row = $this->changeManagementDatabase->getElement($id);
        # Info
        $info = $this->changeManagementDatabase->getInfo($id);
        # Eingetragene PartNo
        $pn = $this->changeManagementDatabase->getPartNumbers($id);
        # Lieferanten
        $lft = $this->changeManagementDatabase::getLieferanten($_GET['loc']);
        # Partno gruppiert

        $partno = $this->changeManagementDatabase->getPartNoGrp($_SESSION['wrk']['citycode']);

        # Übergabe
        $this->pageload("ChangeManagement", "partno", [
            'row' => $row,
            'id' => $id,
            'pn' => $pn,
            'lft' => $lft,
            'info' => $info,
            'partno' => $partno
        ]);
    }

    # Partno Tabelle laden
    public function partnoTable()
    {
        # Parameter
        $id = $_POST['id'];
        # Einträge (Change Management)
        $row = $this->changeManagementDatabase->getElement($id);
        # Eingetragene PartNo
        $pn = $this->changeManagementDatabase->getPartNumbers($id);

        # Übergabe
        $this->pageload("ChangeManagement", "includes/getPartnoTable", [
            'row' => $row,
            'id' => $id,
            'pn' => $pn
        ]);
    }

    # Partno ändern
    public function changePartno()
    {
        $this->changeManagementDatabase->changePartno($_POST['id']);
    }

    # Partno löschen
    public function deletePartno()
    {
        $this->changeManagementDatabase->deletePartno($_POST['id']);
    }

    # Partno hochladen
    public function partnoUpload()
    {
        # Parameter
        $files = $_FILES;
        $id= $_POST['id'];
        # Info
        $info = $this->changeManagementDatabase->getInfo($id);

        # Übergabe
        $this->pageload("ChangeManagement", "includes/upload.partno", [
            'files' => $files,
            'id' => $id,
            'ziel' => $info->zieldatum
        ]);
    }
    # Neue Teilenummer speichern
    public function setPartno()
    {
        $post = $_POST;
        $this->changeManagementDatabase->setPartno($post);
    }

    # Dateien speichern
    public function setDatei()
    {
        # Parameter
        $post = $_POST;
        $id= $_POST['id'];

        # Übergabe
        $this->pageload("ChangeManagement", "includes/upload.file", [
            'post' => $post,
            'id' => $id
        ]);
    }

    # Aktionen laden
    public function aktionen()
    {
        # Parameter
        $id = Functions::decrypt($_GET['id']);

        # Einträge (Change Management)
        $row = $this->changeManagementDatabase->getElement($id);
        $ersteller = $this->changeManagementDatabase->getErsteller($id);
        $info = $this->changeManagementDatabase->getInfo($id);
        $aktionen = $this->changeManagementDatabase->getAktionen($id);
        $oldPart = $this->changeManagementDatabase->getOldPart($id);

        # Übergabe
        $this->pageload("ChangeManagement", "aktionen", [
            'row' => $row,
            'id' => $id,
            'ersteller' => $ersteller,
            'info' => $info,
            'aktionen' => $aktionen,
            'oldPart' => $oldPart
        ]);
    }
    # Freigabe
    public function setFreigabe()
    {
        $this->changeManagementDatabase->setFreigabe($_POST);
    }
    # Abschliessen
    public function abschliessen()
    {
        $this->changeManagementDatabase->abschliessen($_POST);
    }
    # Alte Teile
    public function alteTeile()
    {
        $this->changeManagementDatabase->alteTeile($_POST);
    }
    # Alte Teile
    public function endeAlteTeile()
    {
        $this->changeManagementDatabase->endeAlteTeile($_POST);
    }
    # Löschen
    public function delete()
    {
        $this->changeManagementDatabase->delete($_POST);
    }
    # Nachricht speichern
    public function setNachricht()
    {
        $this->changeManagementDatabase->setNachricht($_POST);
    }

    # Statusboxen laden
    public function getStatus()
    {
        $bid = $_POST['bid'];
        $status = $_POST['status'];
        $citycode = $_POST['citycode'];
        # Übergabe
        $this->pageload("ChangeManagement", "includes/getStatus", [
            'bid' => $bid,
            'status' => $status,
            'citycode' => $citycode
        ]);
    }

    # Felder setzen
    public function changeValue()
    {
        $bid = $_POST['bid'];
        $value = $_POST['value'];
        $feld = $_POST['feld'];
        $table = $_POST['table'];
        $sid = $_POST['sid'];
        $this->changeManagementDatabase->changeValue($table,$feld,$value,$bid,$sid);
    }
    public function setValue()
    {
        $bid = $_POST['bid'];
        $value = $_POST['value'];
        $feld = $_POST['feld'];
        $table = $_POST['table'];
        $this->changeManagementDatabase->setValue($table,$feld,$value,$bid);
    }

    # Orderfelder setzen
    public function changeOrder()
    {
        $bid = $_POST['bid'];
        $value = $_POST['value'];
        $feld = $_POST['feld'];
        $part = $_POST['part'];
        $this->changeManagementDatabase->changeOrder($part,$feld,$value,$bid);
    }

    # Verantwortlichen setzen
    public function changeVerantwortung()
    {
        $bid = $_POST['bid'];
        $uid = $_POST['uid'];
        $this->changeManagementDatabase->changeVerantwortung($bid,$uid);
    }

    # Zieldatum ändern
    public function changeZieldatum()
    {
        $bid = $_POST['bid'];
        $bemerkung = $_POST['bemerkung'];
        $zieldatum = $_POST['zieldatum'];
        $this->changeManagementDatabase->changeZieldatum($bid,$bemerkung,$zieldatum);
    }

    # APQP setzen ändern
    public function setAPQP()
    {
        foreach($_POST as $key => $value) {
            $$key = $value;
        }
        $this->changeManagementDatabase->setAPQP($bid,$apqp,$part,$antwort);
    }

    # APQP ändern
    public function changeAPQP()
    {
        foreach($_POST as $key => $value) {
            $$key = $value;
        }
        $this->changeManagementDatabase->changeAPQP($apqp,$bemerkung,$kosten,$anmerkung,$bid);
    }

    # APQP setzen zurücksetzen
    public function resetAPQP()
    {
        foreach($_POST as $key => $value) {
            $$key = $value;
        }
        $this->changeManagementDatabase->resetAPQP($base_apqp);
    }

    # APQP Form anzeigen
    public function formBemerkungAntwort()
    {
        $bid = $_POST['bid'];
        $antwort = $_POST['antwort'];
        $apqp = $_POST['apqp'];
        $part = $_POST['part'];
        # Übergabe
        $this->pageload("ChangeManagement", "includes/getFormAntwort", [
            'bid' => $bid,
            'antwort' => $antwort,
            'apqp' => $apqp,
            'part' => $part
        ]);
    }

    # Kommentare APQP anzeigen
    public function comAPQP()
    {
        $bid = $_POST['bid'];
        $bereich = $_POST['bereich'];
        $part = $_POST['part'];
        # Übergabe
        $this->pageload("ChangeManagement", "includes/getKomAPQP", [
            'bid' => $bid,
            'bereich' => $bereich,
            'part' => $part
        ]);
    }

    # Kmmentar APQP speichern
    public function setComAPQP()
    {
        $post = $_POST;
        $this->changeManagementDatabase->setComAPQP($post);

    }
    # Antwort zu Kommentar APQP
    public function setAntwortCom()
    {
        $post = $_POST;
        $this->changeManagementDatabase->setAntwortCom($post);
    }

    # APQP Form senden
    public function sendBemerkungAntwort()
    {
        foreach($_POST as $key => $value) {
            $$key = $value;
        }
        $bemerkung = trim($bemerkung);
        $bemerkung = str_replace("<br></p>","</p>", $bemerkung);
        $this->changeManagementDatabase->setBemerkungAPQP($bemerkung,$apqpid);
    }

    # Status prüfen
    public function checkStatus()
    {
        $id = $_POST['id'];
        return $this->changeManagementDatabase::checkAuftrag($id);
    }
    # Status prüfen
    public function checkAuftrag()
    {
        $id = $_POST['id'];
        return $this->changeManagementDatabase::checkAuftrag($id);
    }
    # Auftrag auf Userebene beenden
    public function finishID()
    {
        $id = $_POST['id'];
        $this->changeManagementDatabase::finishID($id);
    }
    # Citycode setzen (Evaluation und Tracking)
    public function setCitycode()
    {
        $_SESSION['wrk']['citycode'] = $_POST['citycode'];
        $part = $_POST['part'];
        # Anzahl Elemente
        $evaluation = ChangeManagementDatabase::countAPQPCC('evaluation',$_POST['citycode']);
        $tracking = ChangeManagementDatabase::countAPQPCC('tracking',$_POST['citycode']);
        $elemente = $this->changeManagementDatabase::getAllAPQP($part);

        # Übergabe
        $this->pageload("ChangeManagement", "includes/getElemente", [
            'elemente' => $elemente,
            'part' => $part,
            'evaluation' => $evaluation,
            'tracking' => $tracking
        ]);
    }
    # Citycode setzen (Lieferanten)
    public function setLieferanten()
    {
        $lieferanten = $this->changeManagementDatabase::getLieferanten($_POST['citycode']);
        # Übergabe
        $this->pageload("ChangeManagement", "includes/getLieferanten", [
            'lieferanten' => $lieferanten,
            'citycode' => $_POST['citycode']
        ]);
    }
    # Neuen Auftrag speichern
    public function neuSend()
    {
        $post = $_POST;
        $this->changeManagementDatabase->neu($post);
    }
    # APQP Element anzeigen
    public function getAPQPElement()
    {
        # Übergabe
        $this->pageload("ChangeManagement", "includes/getAPQPElement", [
            'bid' => $_POST['bid'],
            'part' => $_POST['part'],
            'apqpid' => $_POST['apqpid'],
            'loc' => $_POST['loc']
        ]);
    }
    # APQP Formular anzeigen
    public function getFormAPQP()
    {
        # Übergabe
        $this->pageload("ChangeManagement", "includes/getFormAPQP", [
            'bid' => $_POST['bid'],
            'part' => $_POST['part'],
            'apqpid' => $_POST['apqpid'],
            'citycode' => $_POST['citycode']
        ]);
    }

}