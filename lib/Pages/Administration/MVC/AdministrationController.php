<?php
/** (c) Joachim Göddel . RLMS */


namespace App\Pages\Administration\MVC;

use App\Functions\Functions;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\Home\IndexDatabase;

class AdministrationController extends \App\App\AbstractMVC\AbstractController
{
    # Construct
    private AdministrationDatabase $administrationDatabase;
    private IndexDatabase $indexDatabase;

    public function __construct(
        AdministrationDatabase $administrationDatabase,
        IndexDatabase $indexDatabase
    ){
        $this->administrationDatabase = $administrationDatabase;
        $this->indexDatabase = $indexDatabase;
    }
    # Startseite laden
    public function index()
    {
        # Untermenü erstellen
        $_SESSION['menu']['admin'] = $this->indexDatabase->getAllPages('1', '1', '0');
        # Übergabe
        $this->pageload("Administration", "index", [

        ]);
    }
    # Mitarbeiter laden
    public function mitarbeiter()
    {
        # Alle Mitarbeiter
        $ma = IndexDatabase::selectMa();
        $user = '';
        # Alle Abteilungen
        $ab = $this->administrationDatabase->getAbteilungen();
        $ab2 = $this->administrationDatabase->getAbteilungenRP();
        # Übergabe
        $this->pageload("Administration", "mitarbeiter", [
            'ma' => $ma,
            'ab' => $ab,
            'ab2' => $ab2,
            'user' => $user
        ]);
    }

    # Detailseite Mitarbeiter laden
    public function mitarbeiterDetails()
    {
        $id = Functions::decrypt($_GET['id']);
        # Alle Mitarbeiter
        $user = IndexDatabase::getUserInfo($id);
        # Alle Abteilungen
        $ab = $this->administrationDatabase->getAbteilungen();
        $ab2 = $this->administrationDatabase->getAbteilungenRP();
        # Übergabe
        $this->pageload("Administration", "detailsMitarbeiter", [
            'user' => $user,
            'ab' => $ab,
            'ab2' => $ab2,
            'id' => $id,
            'shw' => 1
        ]);
    }

    # APQP Seite laden
    public function netzwerk()
    {

        # Übergabe
        $this->pageload("Administration", "netzwerk", [

        ]);
    }

    # APQP Seite laden
    public function apqp()
    {

        # Übergabe
        $this->pageload("Administration", "apqp", [

        ]);
    }
    # APQP Elemente
    public function getAPQP()
    {
        # Alle Einträge
        $apqp = ChangeManagementDatabase::getAllAPQPCitycode($_POST['bereich'],$_POST['citycode']);
        # Abteilungen
        $ab = $this->administrationDatabase->getAbteilungenCM();
        $va = ChangeManagementDatabase::getVerantwortlichCM();
        # ID
        $apqpid = (isset($_POST['id'])) ? $apqpid = $_POST['id'] : '';

        # Übergabe
        $this->pageload("Administration", "includes/getAPQP", [
            'apqp' => $apqp,
            'citycode' => $_POST['citycode'],
            'bereich' => $_POST['bereich'],
            'ab' => $ab,
            'va' => $va,
            'apqpid' => $apqpid
        ]);
    }

    # Mitarbeiter speichern
    public function setMitarbeiter()
    {
        $this->administrationDatabase->setMitarbeiter($_POST);
    }

    # Mitarbeiter Rechte speichern
    public function setRechte()
    {
        $this->administrationDatabase->setRechte($_POST);
    }

    # Mitarbeiter speichern
    public function deleteApqpCitycode()
    {
        $post = $_POST;
        ChangeManagementDatabase::deleteApqpCitycode($post);
    }

    # APQP speichern
    public function setAPQP()
    {
        ChangemanagementDatabase::setNewAPQP($_POST);
    }


    # Netzwerk: ARUBA STACK
    public function getArubaStack()
    {
        $this->pageload("Administration","includes/get.aruba.stack", []);
    }

    # Netzwerk: ARUBA STACK
    public function getArubaSwitch()
    {
        $this->pageload("Administration","includes/get.aruba.switch", []);
    }

    # Netzwerk: MICROSENS RING 1
    public function getMicrosensRing1()
    {
        $this->pageload("Administration","includes/get.microsens.ring1", []);
    }

    # Netzwerk: MICROSENS RING 2
    public function getMicrosensRing2()
    {
        $this->pageload("Administration","includes/get.microsens.ring2", []);
    }

    # Netzwerk: MICROSENS RING 3
    public function getMicrosensRing3()
    {
        $this->pageload("Administration","includes/get.microsens.ring3", []);
    }

    # Netzwerk: MICROSENS RING 5
    public function getMicrosensRing5()
    {
        $this->pageload("Administration","includes/get.microsens.ring5", []);
    }

    # Netzwerk: MICROSENS RING 6
    public function getMicrosensRing6()
    {
        $this->pageload("Administration","includes/get.microsens.ring6", []);
    }

    # Netzwerk: MICROSENS RING 7
    public function getMicrosensRing7()
    {
        $this->pageload("Administration","includes/get.microsens.ring7", []);
    }
}