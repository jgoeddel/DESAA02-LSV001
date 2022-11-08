<?php
/** (c) Joachim Göddel . RLMS */

namespace App\App;

# Eingebundene Klassen


# Klasse
use App\Connections\ConMySQL;
use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Administration\MVC\AdministrationController;
use App\Pages\ChangeManagement\ChangeManagementDatabase;
use App\Pages\ChangeManagement\MVC\ChangeManagementController;
use App\Pages\Cron\CronDatabase;
use App\Pages\Cron\MVC\CronController;
use App\Pages\Email\MVC\EmailController;
use App\Pages\Error\MVC\ErrorController;
use App\Pages\Home\IndexDatabase;
use App\Pages\Home\MVC\IndexController;
use App\Pages\Kalender\KalenderDatabase;
use App\Pages\Kalender\MVC\KalenderController;
use App\Pages\Login\MVC\LoginAuth;
use App\Pages\Login\MVC\LoginController;
use App\Pages\Produktion\MVC\ProduktionController;
use App\Pages\Produktion\ProduktionDatabase;
use App\Pages\Prodview\MVC\ProdviewController;
use App\Pages\Prodview\ProdviewDatabase;
use App\Pages\Rotationsplan\MVC\RotationsplanController;
use App\Pages\Rotationsplan\RotationsplanDatabase;
use App\Pages\Scan\MVC\ScanController;
use App\Pages\Schulungen\MVC\SchulungenController;
use App\Pages\Schulungen\SchulungenDatabase;
use App\Pages\SecurityLogin\SecurityLoginDatabase;
use App\Pages\Servicedesk\MVC\ServicedeskController;
use App\Pages\Servicedesk\ServicedeskDatabase;

class Container
{
    # Elements
    private array $classInstances = [];
    private array $builds = [];

    # Construct
    public function __construct()
    {
        $this->builds = [
            # Basiselemente - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
            'errorController' => function () {
                return new ErrorController($this->build("indexDatabase"));
            },
            "container" => function () {
                return new Container();
            },
            'router' => function () {
                return new Router($this->build("container"));
            },
            # STARTSEITE
            'indexController' => function () {
                return new IndexController(
                    $this->build("indexDatabase"),
                    $this->build("loginAuth"),
                    $this->build("securityLoginDatabase")
                );
            },
            'indexDatabase' => function () {
                return new IndexDatabase($this->build("rhs_admin"));
            },
            # LOGIN / LOGOUT
            'loginController' => function () {
                return new LoginController($this->build("loginAuth"));
            },
            'loginAuth' => function () {
                return new LoginAuth(
                    $this->build('securityLoginDatabase'),
                    $this->build('administrationDatabase'),
                    $this->build('indexDatabase')
                );
            },
            'securityLoginDatabase' => function () {
                return new SecurityLoginDatabase($this->build("rhs_admin"));
            },
            # KALENDER
            'kalenderController' => function() {
                return new KalenderController(
                    $this->build('administrationDatabase'),
                    $this->build('kalenderDatabase')
                );
            },
            'kalenderDatabase' => function() {
                return new KalenderDatabase($this->build('rhs_admin'));
            },
            # ADMINISTRATION
            'administrationController' => function () {
                return new AdministrationController(
                    $this->build('administrationDatabase'),
                    $this->build('indexDatabase')
                );
            },
            'administrationDatabase' => function () {
                return new AdministrationDatabase($this->build('rhs_admin'));
            },
            # PRODUKTION
            'produktionController' => function () {
                return new ProduktionController(
                    $this->build('produktionDatabase'),
                    $this->build('kalenderDatabase')
                );
            },
            'produktionDatabase' => function () {
                return new ProduktionDatabase($this->build('rhs_produktion'));
            },
            # LOGBUCH

            # SERVICEDESK
            'servicedeskController' => function () {
                return new ServicedeskController(
                    $this->build('servicedeskDatabase')
                );
            },
            'servicedeskDatabase' => function () {
                return new ServicedeskDatabase($this->build('rhs_servicedesk'));
            },
            # KUNDENÄNDERUNGEN

            # SCHULUNGEN
            'schulungenController' => function () {
                return new SchulungenController(
                  $this->build('schulungenDatabase')
                );
            },
            'schulungenDatabase' => function () {
                return new SchulungenDatabase($this->build('rhs_schulungen'));
            },

            # ROTATIONSPLAN
            'rotationsplanController' => function () {
                return new RotationsplanController(
                    $this->build('indexDatabase'),
                    $this->build('rotationsplanDatabase'),
                    $this->build('administrationDatabase')
                    );
            },
            'rotationsplanDatabase' => function () {
                return new RotationsplanDatabase($this->build('rhs_rotationsplan'));
            },
            # SCANNER
            'scanController' => function () {
                return new ScanController($this->build('administrationDatabase'));
            },
            # ANZEIGEN

            # CHANGE MANAGEMENT
            'changeManagementController' => function () {
                return new ChangeManagementController($this->build('changeManagementDatabase'));
            },
            'changeManagementDatabase' => function () {
                return new ChangeManagementDatabase($this->build('rhs_cm'));
            },
            # CRON
            'cronController' => function () {
                return new CronController($this->build('cronDatabase'));
            },
            'cronDatabase' => function () {
                return new CronDatabase($this->build('rhs_cm'));
            },
            # PRODUKTIONSANZEIGEN
            'prodviewController' => function () {
                return new ProdviewController($this->build(('prodviewDatabase')));
            },
            'prodviewDatabase' => function () {
                return new ProdviewDatabase($this->build('rhs_produktion'));
            },
            # EMAIL
            'emailController' => function () {
                return new EmailController($this->build('administrationDatabase'));
            },
            # -------------------------------------
            # DATENBANKVERBINDUNGEN
            'rhs_admin' => function () {
                $connection = new ConMySQL();
                return $connection->rhs_admin();
            },
            'rhs_produktion' => function () {
                $connection = new ConMySQL();
                return $connection->rhs_produktion();
            },
            'rhs_stoerungen' => function () {
                $connection = new ConMySQL();
                return $connection->rhs_stoerungen();
            },
            'rhs_logbuch' => function () {
                $connection = new ConMySQL();
                return $connection->rhs_logbuch();
            },
            'rhs_kundenaenderungen' => function () {
                $connection = new ConMySQL();
                return $connection->rhs_kundenaenderungen();
            },
            'rhs_servicedesk' => function () {
                $connection = new ConMySQL();
                return $connection->rhs_servicedesk();
            },
            'rhs_schulungen' => function () {
                $connection = new ConMySQL();
                return $connection->rhs_schulungen();
            },
            'rhs_rotationsplan' => function () {
                $connection = new ConMySQL();
                return $connection->rhs_rotationsplan();
            },
            'rhs_cm' => function () {
                $connection = new ConMySQL();
                return $connection->rhs_cm();
            }
        ];
    }

    # Seite erstellen
    public function build($objekt)
    {
        if (isset($this->builds[$objekt])) {
            if (!empty($this->classInstances[$objekt])) {
                return $this->classInstances[$objekt];
            }
            $this->classInstances[$objekt] = $this->builds[$objekt]();
        }
        return $this->classInstances[$objekt];
    }


}