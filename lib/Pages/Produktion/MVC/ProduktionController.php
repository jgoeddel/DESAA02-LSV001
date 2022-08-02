<?php
/** (c) Joachim Göddel . RLMS */
namespace App\Pages\Produktion\MVC;

use App\App\AbstractMVC\AbstractController;
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
    # Logfiles Frontcorner
    public function logfilesFrontcorner()
    {
        # Übergabe
        $this->pageload("Produktion", "logfilesFrontcorner", [

        ]);
    }
}