<?php
/** (c) Joachim Göddel . RLMS */

namespace App\Pages\Servicedesk\MVC;

use App\App\AbstractMVC\AbstractController;
use App\Functions\Functions;
use App\Pages\Servicedesk\ServicedeskDatabase;


class ServicedeskController extends AbstractController
{
    # Construct
    private ServicedeskDatabase $servicedeskDatabase;

    public function __construct(
        ServicedeskDatabase $servicedeskDatabase,
    ){
        $this->servicedeskDatabase = $servicedeskDatabase;
    }
    # Startseite laden
    public function index()
    {
        # Eigene Einträge
        $anzE = $this->servicedeskDatabase->getOpenEntryUser();
        # Alle Einträge
        $eintrag = $this->servicedeskDatabase->getOpenEntry();

        # Übergabe
        $this->pageload("Servicedesk", "index", [
            'anzE' => $anzE,
            'eintrag' => $eintrag
        ]);
    }
    # Eintrag speichern
    public function insert()
    {
        $this->servicedeskDatabase->insert($_POST);
    }
    # Detailseite laden
    public function details()
    {
        # ID entschlüssen
        $id = Functions::decrypt($_GET['id']);
        # Ein Eintrag
        $eintrag = $this->servicedeskDatabase->getOneEntry($id);
        # NIO
        $nio = $this->servicedeskDatabase->nio($id);
        # Anzahl Kommentare
        $ak = $this->servicedeskDatabase->getAnzahlKommentare($id);
        # Alle Kommentare
        $kom = $this->servicedeskDatabase->getAlleKommentare($id);
        # Anzahl Dateien
        $fl = $this->servicedeskDatabase->getAnzahlFiles($id);
        # Alle Dateien
        $fls = $this->servicedeskDatabase->getAllFiles($id);

        # Übergabe
        $this->pageload("Servicedesk", "details", [
            'eintrag' => $eintrag,
            'cid' => $_GET['id'],
            'nio' => $nio,
            'ak' => $ak,
            'kom' => $kom,
            'fl' => $fl,
            'fls' => $fls
        ]);
    }
    # Archiv laden
    public function archiv()
    {
        # Alle Einträge
        $eintrag = $this->servicedeskDatabase->getCloseEntry();

        # Übergabe
        $this->pageload("Servicedesk", "archiv", [
            'eintrag' => $eintrag
        ]);
    }
    # Mein Auftrag
    public function meinAuftrag()
    {
        # ID entschlüssen
        $id = Functions::decrypt($_POST['id']);
        $this->servicedeskDatabase->auftragUebernehmen($id);
    }
    # Auftrag starten
    public function start()
    {
        # ID entschlüssen
        $id = Functions::decrypt($_POST['id']);
        $this->servicedeskDatabase->auftragStart($id);
    }
    # Auftrag starten
    public function pause()
    {
        # ID entschlüssen
        $id = Functions::decrypt($_POST['id']);
        $this->servicedeskDatabase->auftragPause($id);
    }
    # Auftrag starten
    public function weiter()
    {
        # ID entschlüssen
        $id = Functions::decrypt($_POST['id']);
        $this->servicedeskDatabase->auftragWeiter($id);
    }
    # Auftrag beenden
    public function beenden()
    {
        # ID entschlüssen
        $id = Functions::decrypt($_POST['id']);
        $this->servicedeskDatabase->auftragEnde($id);
    }
    # Auftrag abschliesseb
    public function abschluss()
    {
        # ID entschlüssen
        $id = Functions::decrypt($_POST['id']);
        $this->servicedeskDatabase->auftragAbschluss($id);
    }
    # Auftrag nio
    public function nio()
    {
        # ID entschlüssen
        $id = Functions::decrypt($_POST['id']);
        $this->servicedeskDatabase->auftragNIO($id);
    }
    # Kommentar speichern
    public function insertKommentar()
    {
        $this->servicedeskDatabase->insertKommentar($_POST);
    }
    # Datei speichern
    public function upload()
    {
        # Übergabe
        $this->pageload("Servicedesk", "includes/upload.file", [

        ]);
    }
    # Datei speichern
    public function deleteFile()
    {
        $this->servicedeskDatabase->deleteFile($_POST['id'],$_POST['datei']);
    }
}