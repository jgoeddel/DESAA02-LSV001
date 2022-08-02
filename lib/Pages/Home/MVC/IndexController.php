<?php
/** (c) Joachim Göddel . RLMS */
namespace App\Pages\Home\MVC;

use App\App\AbstractMVC\AbstractController;
use App\Pages\Home\IndexDatabase;
use App\Pages\Login\MVC\LoginAuth;
use App\Pages\Produktion\ProduktionDatabase;
use App\Pages\SecurityLogin\SecurityLoginDatabase;
use Exception;

class IndexController extends AbstractController
{
    # Page Load

    # Construct
    private IndexDatabase $indexDatabase;
    private LoginAuth $loginAuth;
    private SecurityLoginDatabase $securityLoginDatabase;

    public function __construct(
        IndexDatabase $indexDatabase,
        LoginAuth $loginAuth,
        SecurityLoginDatabase $securityLoginDatabase
    ){
        $this->indexDatabase = $indexDatabase;
        $this->loginAuth = $loginAuth;
        $this->securityLoginDatabase = $securityLoginDatabase;
    }

    # Startseite laden

    /**
     * @throws Exception
     */
    public function index()
    {
        $this->loginAuth->checkStayin();
        # Übergabe
        $this->pageload("Home", "index", [

        ]);
    }
    public function logout()
    {
        $this->securityLoginDatabase->deleteStayindata($_SESSION["user"]["id"]);
        unset($_SESSION['user']);
        unset($_SESSION['login']);
        unset($_SESSION['rechte']);
        setcookie("identifier", "", time() - 3600);
        setcookie("securitytoken", "", time() - 3600);
        # Übergabe
        $this->pageload("Home", "index", [

        ]);
    }

    # Übersetzungen
    public function i18n()
    {
        $lang = (isset($_POST['lang'])) ? $_POST['lang'] : $_SESSION['lang'];
        $this->indexDatabase->setLanguageSession($lang, 'a_i18n');
    }

    # Produktionsdaten Startseite
    public function getProduktionIndex()
    {
        # Summe Jahr
        $summeJahr = ProduktionDatabase::getSummeJahr(DATE('Y'), 'fzg');
        $summeVorgabe = ProduktionDatabase::getSummeJahr(DATE('Y'), 'vorgabe');
        $ergebnis = ProduktionDatabase::getErgebnis('fzg');
        $vorgabe = ProduktionDatabase::getErgebnis('vorgabe');

        # Übergabe
        $this->pageload("Home", "includes/getProduktionIndex", [
            'summeJahr' => $summeJahr,
            'summeVorgabe' => $summeVorgabe,
            'ergebnis' => $ergebnis,
            'vorgabe' => $vorgabe
        ]);
    }
    # Kalendereinträge Startseite
    public function getKalenderIndex()
    {
        # Übergabe
        $this->pageload("Home", "includes/getKalenderIndex", [
            'k' => $_POST['k']
        ]);
    }
    # Aushang Startseite
    public function getAushangIndex()
    {
        # Übergabe
        $this->pageload("Home", "includes/getAushangIndex", [

        ]);
    }
    # Produktion Startseite (Jahresübersicht)
    public function getProduktionJahr()
    {
        # Datumswerte für Funktion
        $m = DATE('n');
        $y = DATE('Y');
        # Produktionsdaten
        for ($i = 1; $i <= 12; $i++) {
            $ergebnis[] = ProduktionDatabase::getDiffMonat($m, $y);
            $prodTage[] = ProduktionDatabase::getProdTageMonat($m, $y);
            $vorgabe[] = ProduktionDatabase::getSummeMonat($m, $y, 'vorgabe');
            $fzg[] = ProduktionDatabase::getSummeMonat($m, $y, 'fzg');
            $monat[] = $m;
            $m--;
            if ($m == 0) {
                $y--;
                $m = 12;
            }
        }
        # Übergabe
        $this->pageload("Home", "includes/getProduktionJahr", [
            'e' => $ergebnis,
            'prodTage' => $prodTage,
            'v' => $vorgabe,
            'fzg' => $fzg,
            'monat' => $monat
        ]);
    }
}