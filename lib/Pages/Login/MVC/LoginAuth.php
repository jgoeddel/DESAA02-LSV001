<?php
/** (c) Joachim Göddel . RLMS */
namespace App\Pages\Login\MVC;

# Klassen und Erweiterungen
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Home\IndexDatabase;
use App\Pages\SecurityLogin\SecurityLoginDatabase;
use Exception;

class LoginAuth
{
    # Parameter
    private SecurityLoginDatabase $securityLoginDatabase;
    private AdministrationDatabase $administrationDatabase;
    private IndexDatabase $indexDatabase;
    # Construct


    public function __construct(
        SecurityLoginDatabase $securityLoginDatabase,
        AdministrationDatabase $administrationDatabase,
        IndexDatabase $indexDatabase
    )
    {
        $this->securityLoginDatabase = $securityLoginDatabase;
        $this->administrationDatabase = $administrationDatabase;
        $this->indexDatabase = $indexDatabase;
    }

    # Identifier erstellen
    /**
     * @throws Exception
     */
    function setIdentifier(): string
    {
        return bin2hex(time() . random_bytes(8));
    }

    # Token erstellen
    /**
     * @throws Exception
     */
    function setSecurityToken(): string
    {
        return bin2hex(time() . random_bytes(10));
    }

    # Cookie für Anmeldung erstellen
    /**
     * @throws Exception
     */
    function buildStayin($username)
    {
        $identifier = $this->setIdentifier();
        $securitytoken = $this->setSecurityToken();
        $user = $this->administrationDatabase->getUser("", $username);
        if($user) {
            $this->securityLoginDatabase->newStayin($user->id, $identifier, password_hash($securitytoken, PASSWORD_DEFAULT));
            setcookie("identifier", $identifier, time() + (3600 * 24 * 365));
            setcookie("securitytoken", $securitytoken, time() + (3600 * 24 * 365));
        } else {
            return false;
        }
    }

    # Prüfen, ob der User bereits per Cookie angemeldet ist
    /**
     * @throws Exception
     */
    public function checkStayin()
    {
        if (isset($_COOKIE["identifier"])) {
            if (isset($_COOKIE["securitytoken"])) {
                $hs = password_hash($_COOKIE['securitytoken'], PASSWORD_DEFAULT);
                $stayindata = $this->securityLoginDatabase->getStayinData($_COOKIE["identifier"]);
                if (!password_verify($_COOKIE["securitytoken"], $stayindata->securitytoken)) {
                    header(header: "Location: /logout");
                } else {
                    session_regenerate_id(true);
                    $newSecurityToken = $this->setSecurityToken();
                    $this->securityLoginDatabase->updateSecurityToken(password_hash($newSecurityToken, PASSWORD_DEFAULT), $stayindata->user_id);
                    setcookie("securitytoken", $newSecurityToken, time() + (3600 * 24 * 365));
                    $userdata = $this->administrationDatabase->getUser($stayindata->user_id, "");
                    $_SESSION['user']['id'] = $userdata->id;
                    $_SESSION['user']['cryptID'] = \App\Functions\Functions::encrypt($userdata->id);
                    $_SESSION['user']['vorname'] = $userdata->vorname;
                    $_SESSION['user']['name'] = $userdata->name;
                    $n = \App\Functions\Functions::translateFilename($_SESSION['user']['name']);
                    $n = strtolower($n);
                    $_SESSION['user']['filer_name'] = $n;
                    $_SESSION['user']['username'] = $userdata->username;
                    $_SESSION['user']['status'] = $userdata->status;
                    $_SESSION['user']['pass'] = $userdata->password;
                    $_SESSION['user']['wrk_abteilung'] = $userdata->wrk_abteilung;
                    $_SESSION['user']['abteilung'] = $userdata->abteilung;
                    $_SESSION['user']['wrk_schicht'] = $userdata->wrk_schicht;
                    $_SESSION['user']['dbname'] = $userdata->vorname . " " . $userdata->name;
                    $_SESSION['user']['lang'] = $userdata->lang;
                    $_SESSION['user']['cryptID'] = \App\Functions\Functions::encrypt($userdata->id);
                    // Rechte setzen
                    $this->administrationDatabase->setUserRights($userdata->id);
                    // Login setzen
                    $_SESSION['login'] = true;
                }
            }
        }
    }

    # Prüfen, ob der User sich angemeldet hat
    public function checklogin($username, $password): bool
    {
        $user = $this->administrationDatabase->getUser("", $username);
        var_dump($user);
        if ($user) {
            // $password_hash = password_hash('rka719', PASSWORD_DEFAULT);
            if (password_verify($password, $user->password)) {
                $user = $this->administrationDatabase->getUser("", $username);
                session_regenerate_id(true);
                $_SESSION['user']['id'] = $user->id;
                $_SESSION['user']['cryptID'] = \App\Functions\Functions::encrypt($user->id);
                $_SESSION['user']['vorname'] = $user->vorname;
                $_SESSION['user']['name'] = $user->name;
                $n = \App\Functions\Functions::translateFilename($_SESSION['user']['name']);
                $n = strtolower($n);
                $_SESSION['user']['filer_name'] = $n;
                $_SESSION['user']['username'] = $user->username;
                $_SESSION['user']['status'] = $user->status;
                $_SESSION['user']['pass'] = $user->password;
                $_SESSION['user']['wrk_abteilung'] = $user->wrk_abteilung;
                $_SESSION['user']['abteilung'] = $user->abteilung;
                $_SESSION['user']['wrk_schicht'] = $user->wrk_schicht;
                $_SESSION['user']['dbname'] = $user->vorname . " " . $user->name;
                $_SESSION['user']['lang'] = $user->lang;
                $lang = strtolower($user->lang);
                // Rechte setzen
                $this->administrationDatabase->setUserRights($user->id);
                // Login setzen
                $_SESSION['login'] = true;
                $this->indexDatabase->setLanguageSession($lang, 'a_i18n');
                return true;
            } else {
                return false;
            }
        }
        return false;
    }
}