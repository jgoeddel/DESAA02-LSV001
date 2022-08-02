<?php
/** (c) Joachim Göddel . RLMS */
namespace App\Pages\Error\MVC;

use App\App\AbstractMVC\AbstractController;
use App\Pages\Home\IndexDatabase;

class ErrorController extends AbstractController
{
    private IndexDatabase $indexDatabase;

    public function __construct(
        IndexDatabase $indexDatabase,
    ){
        $this->indexDatabase = $indexDatabase;
    }

    public function errorPage()
    {
        $i18n = $this->indexDatabase->setLanguageSession($_SESSION['lang'], 'a_i18n');
        $this->pageload("Error", "errorPage", [
            'i18n' => $i18n
        ]);
    }
}