<?php
/** (c) Joachim Göddel . RLMS */

namespace App\Pages\Schulungen\MVC;

use App\App\AbstractMVC\AbstractController;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Home\IndexDatabase;
use App\Pages\Schulungen\SchulungenDatabase;

class SchulungenController extends AbstractController
{
    private SchulungenDatabase $schulungenDatabase;

    public function __construct(
        SchulungenDatabase $schulungenDatabase
    )
    {
        $this->schulungenDatabase = $schulungenDatabase;
    }

    # Startseite laden
    public function index()
    {
        # Alle Abteilungen
        $abt = AdministrationDatabase::getAllAbt('b_abteilung','schulung');
        # Alle Räume
        $rms = $this->schulungenDatabase->getRooms();
        # Alle Mitarbeiter
        $ma = IndexDatabase::selectMaCitycode($_SESSION['user']['citycode']);
        # Schulungsarten
        $art = $this->schulungenDatabase->getArt();
        # Übergabe
        $this->pageload("Schulungen", "index", [
            'abt' => $abt,
            'rms' => $rms,
            'ma' => $ma,
            'art' => $art
        ]);
    }

    # Neue Schulung speichern
    public function insertSchulung()
    {
        $this->schulungenDatabase->insertSchulung($_POST);
    }
}