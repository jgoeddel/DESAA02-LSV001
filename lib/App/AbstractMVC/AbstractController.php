<?php
/** (c) Joachim Göddel . RLMS */
namespace App\App\AbstractMVC;

class AbstractController
{
    public function pageload($dir, $view, $var){
        extract($var);
        require_once BASEPATH . "/lib/Pages/$dir/MVC/View/$view.php";
    }
}