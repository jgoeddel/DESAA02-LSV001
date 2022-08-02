<?php
/** (c) Joachim Göddel . RLMS */


namespace App\Pages\Login\MVC;

use Exception;

class LoginController extends \App\App\AbstractMVC\AbstractController
{
    # Parameter
    private LoginAuth $loginAuth;

    # Construct
    public function __construct(
        LoginAuth $loginAuth,
    )
    {
        $this->loginAuth = $loginAuth;
    }

    # Login Seite laden
    public function login()
    {
        # Übergabe
        $this->pageload("Home", "login", [

        ]);
    }

    # Login
    /**
     * @throws Exception
     */
    public function goLogin()
    {
        $_SESSION['text']['error'] = null;
        if (!empty($_POST)) {
            $loginuser = $_POST['loginuser'];
            $password = $_POST['password'];
            $this->loginAuth->buildStayin($loginuser);
            $login = $this->loginAuth->checklogin($loginuser, $password);
            var_dump($login);
            if (!$login) {
                $_SESSION['text']['error'] = $_SESSION['text']['error_anmeldung'];
            }
            header(header: "Location: /");
        }

        if (!isset($_SESSION["login"])) {
            $this->loginAuth->checkStayin();
            $_SESSION['text']['error'] = '';
        }

        if (empty($_SESSION['login'])) {
            $_SESSION['text']['error'] = '';
            $this->pageload("Home", "index", []);
        }
    }
}