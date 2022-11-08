<?php
/** (c) Joachim Göddel . RLMS */

namespace App\Pages\Prodview\MVC;

use App\App\AbstractMVC\AbstractController;
use App\Pages\Prodview\ProdviewDatabase;

class ProdviewController extends AbstractController
{
    # Startseite
    private $prodviewDatabase;

    public function __construct(
        ProdviewDatabase $prodviewDatabase
    )
    {
        $this->prodviewDatabase = $prodviewDatabase;
    }

    public function index()
    {
        # Linien
        $ln = $this->prodviewDatabase->getLineCitycode();
        # Übergabe
        $this->pageload("Prodview", "index", [
            'ln' => $ln
        ]);
    }

    public function line()
    {
        # Linie
        $line = $this->prodviewDatabase->getLine($_POST['id']);
        # Übergabe
        $this->pageload("Prodview", "includes/dspLine", [
            'line' => $line
        ]);
    }

    public function citycode()
    {
        # Übergabe
        $this->pageload("Prodview", "citycode", [

        ]);
    }

}