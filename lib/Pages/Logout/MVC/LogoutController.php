<?php
/** (c) Joachim Göddel . RLMS */


namespace App\Pages\Logout\MVC;

use App\Pages\SecurityLogin\SecurityLoginDatabase;

class LogoutController extends \App\App\AbstractMVC\AbstractController
{
    private SecurityLoginDatabase $securityLoginDatabase;

    public function __construct(SecurityLoginDatabase $securityLoginDatabase)
    {
        $this->securityLoginDatabase = $securityLoginDatabase;
    }

    public function logout()
    {
        $this->securityLoginDatabase->deleteStayindata($_SESSION["user"]["id"]);
        unset($_SESSION['user']);
        unset($_SESSION['login']);
        unset($_SESSION['rechte']);
        unset($_SESSION['text']);
        unset($_SESSION['lang']);
        setcookie("identifier", "", time() - 3600);
        setcookie("securitytoken", "", time() - 3600);
        header(header: "Location: /");
    }
}