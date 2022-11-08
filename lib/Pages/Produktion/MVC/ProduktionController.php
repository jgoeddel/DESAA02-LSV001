<?php
/** (c) Joachim Göddel . RLMS */
namespace App\Pages\Produktion\MVC;

use App\App\AbstractMVC\AbstractController;
use App\Functions\Functions;
use App\Pages\Kalender\KalenderDatabase;
use App\Pages\Produktion\ProduktionDatabase;

class ProduktionController extends  AbstractController
{
    # Construct
    private ProduktionDatabase $produktionDatabase;
    private KalenderDatabase $kalenderDatabase;

    public function __construct(
        ProduktionDatabase $produktionDatabase,
        KalenderDatabase $kalenderDatabase
    ){
        $this->produktionDatabase = $produktionDatabase;
        $this->kalenderDatabase = $kalenderDatabase;
    }

    # Startseite
    public function index()
    {
        # Übergabe
        $this->pageload("Produktion", "index", [

        ]);
    }

    # Kalenderwoche Startseite
    public function ajaxGetProduktionKW()
    {
        # Übergabe
        $this->pageload("Produktion", "includes/getProduktionKW", [

        ]);
    }

    # FAB Anlage Motorband
    public function fabMotorband()
    {
        # Übergabe
        $this->pageload("Produktion", "fabMotorband", [

        ]);
    }

    # Taktzeit Motorband
    public function taktMotorband()
    {
        # Übergabe
        $this->pageload("Produktion", "taktMotorband", [

        ]);
    }

    # Offen Calloffs Motorband (Seite)
    public function openCalloffs()
    {
        # Übergabe
        $this->pageload("Produktion", "calloffsMotorband", [

        ]);
    }

    # Offen Calloffs Motorband (Chart)
    public function chartCalloffs()
    {
        $query = $this->produktionDatabase->getTmpValues();
        # Übergabe
        $this->pageload("Produktion", "includes/chartCalloffs", [
            'query' => $query
        ]);
    }
    # Offen Calloffs Motorband (Number)
    public function numberCalloffs()
    {
        $number = $this->produktionDatabase->setCalloffs($_SESSION['mkspts']['server'], $_SESSION['mkspts']['database'], $_SESSION['mkspts']['uid'], $_SESSION['mkspts']['pwd']);
        # Übergabe
        $this->pageload("Produktion", "includes/numberCalloffs", [
            'number' => $number
        ]);
    }
    # Offen Calloffs Motorband (Table)
    public function tableCalloffs()
    {
        $query = $this->produktionDatabase->getTmpValues();
        $i = $this->produktionDatabase->setCalloffs($_SESSION['mkspts']['server'], $_SESSION['mkspts']['database'], $_SESSION['mkspts']['uid'], $_SESSION['mkspts']['pwd']);
        # Übergabe
        $this->pageload("Produktion", "includes/tableCalloffs", [
            'query' => $query,
            'i' => $i
        ]);
    }
    # Bandsicherung iSeries
    public function bandsicherung()
    {
        $cel = $this->produktionDatabase->getBandsicherung();
        $row = $this->produktionDatabase->getLastRow();

        # Übergabe
        $this->pageload("Produktion", "bandsicherung", [
            'cel' => $cel,
            'row' => $row
        ]);
    }
    # Bandsicherung iSeries speichern
    public function insertBandsicherung()
    {
        $this->produktionDatabase->insertBandsicherung($_POST);
    }
    # Logfiles Frontcorner
    public function logfilesFrontcorner()
    {
        # Übergabe
        $this->pageload("Produktion", "logfilesFrontcorner", [

        ]);
    }
    # Dashboard Frontcorner
    public function dashboardFrontcorner()
    {
        // Daten einlesen
        for ($i = 1; $i < 5; $i++):
            $context = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
            $url = 'http://172.16.101.101:813' . $i . '/xml/stations';
            $xml = file_get_contents($url, false, $context);
            $xml = simplexml_load_string($xml);
            foreach ($xml->station as $x):
                $y[] = $x;
            endforeach;
        endfor;
        # Übergabe
        $this->pageload("Produktion", "frontcornerDashboard", [
            'y' => $y
        ]);
    }
    # Dashboard Frontcorner
    public function dspDashboardFrontcorner()
    {
        # Daten einlesen
        for ($i = 1; $i < 5; $i++):
            $context = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
            $url = 'http://172.16.101.101:813' . $i . '/xml/stations';
            $xml = file_get_contents($url, false, $context);
            $xml = simplexml_load_string($xml);
            foreach ($xml->station as $x):
                $y[] = $x;
            endforeach;
        endfor;
        # Array sortieren
        usort($y,Functions::build_sorter('name'));
        # Übergabe
        $this->pageload("Produktion", "includes/getFrontcornerStations", [
            'y' => $y
        ]);
    }
    # Inhalt Station Frontcorner Dashboard
    public function dspStationDashboardFrontcorner()
    {
        # Daten einlesen
        for ($i = 1; $i < 5; $i++):
            $context = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
            $url = 'http://172.16.101.101:813' . $i . '/xml/stations';
            $xml = file_get_contents($url, false, $context);
            $xml = simplexml_load_string($xml);
            foreach ($xml->station as $x):
                $y[] = $x;
            endforeach;
        endfor;
        # Array sortieren
        usort($y,Functions::build_sorter('name'));
        # Übergabe
        $this->pageload("Produktion", "includes/getFrontcornerStation", [
            'y' => $y,
            'id' => $_POST['id']
        ]);
    }
}