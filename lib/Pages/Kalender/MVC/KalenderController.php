<?php
namespace App\Pages\Kalender\MVC;
use App\App\AbstractMVC\AbstractController;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Kalender\KalenderDatabase;

/** (c) Joachim Göddel . RLMS */

class KalenderController extends AbstractController
{
    # Construct
    private AdministrationDatabase $administrationDatabase;
    private KalenderDatabase $kalenderDatabase;

    public function __construct(
        AdministrationDatabase $administrationDatabase,
        KalenderDatabase $kalenderDatabase
    ){
        $this->administrationDatabase = $administrationDatabase;
        $this->kalenderDatabase = $kalenderDatabase;
    }
    # Startseite laden
    public function index()
    {

        # Übergabe
        $this->pageload("Kalender", "index", [

        ]);
    }
}