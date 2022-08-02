<?php
/** (c) Joachim Göddel . RLMS */


namespace App\Pages\Scan\MVC;

class ScanController extends \App\App\AbstractMVC\AbstractController
{



    # Startseite laden
    public function index()
    {

        # Übergabe
        $this->pageload("Scan", "index", [

        ]);
    }
}