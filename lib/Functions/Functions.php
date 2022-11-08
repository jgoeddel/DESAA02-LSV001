<?php
/** (c) Joachim Göddel . RLMS */


namespace App\Functions;

use App\Pages\Administration\AdministrationDatabase;
use App\Pages\Produktion\ProduktionDatabase;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;

class Functions
{

    # Sonderzeichen ersetzen
    public static function translateFilename($filename): string
    {
        $trans = array("%20" => "_", " " => "_", "ü" => "ue",
            "Ü" => "Ue", "Ä" => "Ae", "ä" => "ae", "Ö" => "Oe",
            "ö" => "oe", "ß" => "ss", "," => "_",
            "É" => "E", "é" => "e", "È" => "E", "è" => "e",
            "Ê" => "E", "ê" => "e", "Î" => "I", "î" => "i",
            "Ï" => "I", "ï" => "i", "Ç" => "C", "ç" => "c",
            "À" => "A", "à" => "a",
            "Ë" => "E", "ë" => "e",
            "©" => "c", "®" => "r",
            "&" => "und", "(" => "_", ")" => "_",
            "@" => "at", "?" => "_", "#" => "_",
            "]" => "_", "[" => "_");
        return strtr($filename, $trans);
    }

    # Verschlüsseln
    public static function encrypt($text): string
    {
        $salt = 'DFS65';
        $key = md5($salt);
        $append = "-";
        return urlencode(base64_encode($text . $append . $key));
    }

    # Entschlüsseln
    public static function decrypt($text): string
    {
        $d = base64_decode(urldecode($text));
        $append = "-";
        $value = explode($append, $d);
        return $value[0];
    }

    # Base URL
    public static function getBaseURL(): string
    {
        return "http://desaa02-lsv001/";
    }

    # Anzeige German Date
    public static function germanDate($date): array|string
    {
        $datum = str_replace($_SESSION['en']['tage'], $_SESSION['de']['tage'], $date);
        return str_replace($_SESSION['en']['monate'], $_SESSION['de']['monate'], $datum);
    }
    # Ausgabe deutsches Datumsformat

    /**
     * @throws Exception
     */
    public static function germanDateFormat($date): bool|string
    {
        $datum = new DateTime($date);
        return $datum->format('d.m.Y');
    }
    public static function germanDateFormatTime($date): bool|string
    {
        $datum = new DateTime($date);
        return $datum->format('d.m.Y, H:i');
    }

    # Ausgabe deutscher Tag
    public static function germanTag($date): array|string
    {
        return str_replace($_SESSION['en']['tage'], $_SESSION['de']['tage'], $date);
    }

    # Ausgabe deutscher Monat
    public static function germanMonat($date): array|string
    {
        return str_replace($_SESSION['en']['monate'], $_SESSION['de']['monate'], $date);
    }

    # Ausgabe deutsche Nummer
    public static function germanNumber($number): string
    {
        return number_format($number, 2, ',', '.');
    }

    # Ausgabe deutsche Nummer ohne Dezimalstellen
    public static function germanNumberNoDez($number): string
    {
        return number_format($number, 0, ',', '.');
    }

    # Anzeige Differenz Vorgabe Ergebnis
    public static function getDiffEV($ergebnis, $vorgabe)
    {
        if (empty($vorgabe)): $vorgabe = 0; endif;
        $ergebnis = str_replace(".", "", $ergebnis);
        $vorgabe = str_replace(".", "", $vorgabe);
        $berechnung = ($vorgabe > 0) ? $ergebnis - $vorgabe : 0;
        $berechnung = self::germanNumberNoDez($berechnung);
        if ($berechnung > 0): return $berechnung . ' <i class="fa fa-caret-up text-success ms-1"></i>'; endif;
        if ($berechnung == 0): return $berechnung . ' '; endif;
        if ($berechnung < 0): return $berechnung . ' <i class="fa fa-caret-down text-danger ms-1"></i>'; endif;
    }


    # Zeigt das Bild im Parallax Bereich an
    public static function dspParallaxImg(string $bild): void
    {
        echo "$('.parallax-window').parallax({src: '$bild'});";
    }

    # Anzeige Parallax
    public static function dspParallax(string $titel, string $subLine)
    {
        echo "<div class=\"container-fluid\" id=\"parallax\">\n";
        echo "<div class=\"p-5\">\n";
        echo "<div class=\"white-box-left\">\n";
        echo "<p class=\"font-size-16 font-weight-300 p-0 m-0 pt-5 oswald\">$titel</p>";
        echo "<h1 class=\"oswald hero pt-3\">\n";
        echo "$subLine\n";
        echo "</h1>\n";
        echo "</div>\n";
        echo "</div>\n";
        echo "</div>\n";
    }

    # Anzeige Parallax
    public static function dspParallaxMedium(string $titel, string $subLine): void
    {
        echo "<div class=\"container-fluid\" id=\"parallax\">\n";
        echo "<div class=\"px-4\">\n";
        echo "<div class=\"medium-white-box-left\">\n";
        echo "<p class=\"font-size-16 font-weight-300 oswald m-0 p-0 pt-5\">$titel</p>";
        echo "<h1 class=\"oswald hero pt-3\">\n";
        echo "$subLine\n";
        echo "</h1>\n";
        echo "</div>\n";
        echo "</div>\n";
        echo "</div>\n";
    }

    # Anzeige Parallax mit Login
    public static function dspParallaxLogin(string $titel, string $subLine): void
    {
        # Version für alle Ansichten, ausser mobil
        echo "<div class=\"container-fluid d-none d-sm-block\" id=\"parallax\">\n";
        echo "<div class=\"p-5\">\n";
        echo "<div class=\"medium-white-box-left\">\n";
        echo "<p class=\"font-size-16 font-weight-300 oswald m-0 p-0 pt-5\">$titel</p>";
        echo "<h1 class=\"oswald hero m-0 p-0 mb-3\">\n";
        echo "$subLine\n";
        echo "</h1>\n";
        if (!isset($_SESSION['login'])) {
            echo "<div class=\"row m-0 p-0 mt-2 pb-3\">\n";
            echo "<div class=\"col-12 col-lg-5 border__right--dotted-gray\">\n";
            echo "<div class=\"pe-3\">\n";
            echo "<h3 class=\"oswald font-weight-300 font-size-18 m-0 p-0\">\n";
            echo $_SESSION['text']['h_nichtAngemeldet'];
            echo "</h3>\n";
            echo "<p class='m-0 p-0 font-size-12'>" . $_SESSION['text']['t_nichtAngemeldet'] . "</p>";
            echo "</div>\n"; #p-3
            echo "</div>\n"; #col-5
            echo "<div class=\"col-12 col-lg-5\">\n";
            ?>
            <form class="needs-validation mx-3" method="post" action="/goLogin">
                <div class="border__bottom--dotted-gray mb-1">
                    <input type="text" name="loginuser" placeholder="<?= $_SESSION['text']['t_username'] ?>"
                           class="invisible-formfield" required>
                </div>
                <div class="border__bottom--dotted-gray mb-1">
                    <input type="password" name="password" placeholder="<?= $_SESSION['text']['t_password'] ?>"
                           class="invisible-formfield" required>
                </div>
                <button class="btn btn-primary btn-sm mt-1 float-end"
                        type="submit"><?= $_SESSION['text']['b_anmelden'] ?></button>
            </form>
            <?php
            echo "</div>\n"; #col-7
            echo "</div>\n"; # ROW
        }
        echo "</div>\n";
        echo "</div>\n";
        echo "</div>\n";
        # Mobile Version
        echo "<div class=\"container-fluid d-block d-sm-none\" id=\"parallax\">\n";
        echo "<div class=\"\">\n";
        echo "<div class=\"medium-white-box-left\">\n";
        echo "<p class=\"font-size-12 font-weight-300 oswald p-0 m-0 pt-3\">$titel</p>";
        echo "<h1 class=\"oswald font-size-24 m-0 p-0\">\n";
        echo "$subLine\n";
        echo "</h1>\n";
        if (!isset($_SESSION['login'])) {
            echo "<div class=\"row m-0 p-0 mt-2 py-3 border__top--dotted-gray\">\n";
            echo "<div class=\"col-10\">\n";
            echo "<div class=\"pe-3\">\n";
            echo "<h3 class=\"oswald font-weight-300 font-size-18 m-0 p-0\">\n";
            echo $_SESSION['text']['h_nichtAngemeldet'];
            echo "</h3>\n";
            echo "<p class='m-0 p-0 font-size-12'>" . $_SESSION['text']['t_nichtAngemeldet'] . "</p>";
            echo "</div>\n"; #p-3
            echo "</div>\n"; #col-5
            echo "<div class=\"col-10 mt-3\">\n";
            ?>
            <form class="needs-validation" method="post" action="/goLogin">
                <div class="border__bottom--dotted-gray mb-1">
                    <input type="text" name="loginuser" placeholder="<?= $_SESSION['text']['t_username'] ?>"
                           class="invisible-formfield" required>
                </div>
                <div class="border__bottom--dotted-gray mb-1">
                    <input type="password" name="password" placeholder="<?= $_SESSION['text']['t_password'] ?>"
                           class="invisible-formfield" required>
                </div>
                <button class="btn btn-primary btn-sm mt-1 float-end"
                        type="submit"><?= $_SESSION['text']['b_anmelden'] ?></button>
            </form>
            <?php
            echo "</div>\n"; #col-7
            echo "</div>\n"; # ROW
        }
        echo "</div>\n";
        echo "</div>\n";
        echo "</div>\n";
    }

    # Anzeige Parallax mit Login
    public static function dspParallaxNeuerUser(string $titel, string $subLine): void
    {
        # Version für alle Ansichten, ausser mobil
        echo "<div class=\"container-fluid d-none d-sm-block\" id=\"parallax\">\n";
        echo "<div class=\"p-5\">\n";
        echo "<div class=\"medium-white-box-left\">\n";
        echo "<p class=\"font-size-16 font-weight-300 oswald m-0 p-0 pt-5\">$titel</p>";
        echo "<h1 class=\"oswald hero m-0 p-0 mb-3\">\n";
        echo "$subLine\n";
        echo "</h1>\n";
        echo "<div class=\"row m-0 p-0 mt-2 pb-3\" id=\"form\">\n";
        echo "<div class=\"col-12 col-lg-5 border__right--dotted-gray\">\n";
        echo "<div class=\"pe-3\">\n";
        echo "<h3 class=\"oswald font-weight-300 font-size-18 m-0 p-0\">\n";
        echo $_SESSION['text']['h_neuerMitarbeiter'];
        echo "</h3>\n";
        echo "<p class='m-0 p-0 font-size-12'>" . $_SESSION['text']['t_neuerMitarbeiter'] . "</p>";
        echo "</div>\n"; #p-3
        echo "</div>\n"; #col-5
        echo "<div class=\"col-12 col-lg-5\">\n";
        ?>
        <form class="needs-validation mx-3" method="post" id="mitarbeiterNeu">
            <div class="border__bottom--dotted-gray mb-1">
                <input type="text" name="vorname" placeholder="<?= $_SESSION['text']['t_vorname'] ?>"
                       class="invisible-formfield" required>
            </div>
            <div class="border__bottom--dotted-gray mb-1">
                <input type="name" name="name" placeholder="<?= $_SESSION['text']['t_name'] ?>"
                       class="invisible-formfield" required>
            </div>
            <button class="btn btn-primary btn-sm mt-1 float-end"
                    type="submit"><?= $_SESSION['text']['b_speichern'] ?></button>
        </form>
        <?php
        echo "</div>\n"; #col-7
        echo "</div>\n"; # ROW
        echo "</div>\n";
        echo "</div>\n";
        echo "</div>\n";
    }

    # Anzeige Parallax mit Login
    public static function dspParallaxNeueStation(string $titel, string $subLine): void
    {
        # Version für alle Ansichten, ausser mobil
        echo "<div class=\"container-fluid d-none d-sm-block\" id=\"parallax\">\n";
        echo "<div class=\"p-5\">\n";
        echo "<div class=\"medium-white-box-left\">\n";
        echo "<p class=\"font-size-16 font-weight-300 oswald m-0 p-0 pt-5\">$titel</p>";
        echo "<h1 class=\"oswald hero m-0 p-0 mb-3\">\n";
        echo "$subLine\n";
        echo "</h1>\n";
        echo "<div class=\"row m-0 p-0 mt-2 pb-3\" id=\"form\">\n";
        echo "<div class=\"col-12 col-lg-5 border__right--dotted-gray\">\n";
        echo "<div class=\"pe-3\">\n";
        echo "<h3 class=\"oswald font-weight-300 font-size-18 m-0 p-0\">\n";
        echo $_SESSION['text']['h_neueStation'];
        echo "</h3>\n";
        echo "<p class='m-0 p-0 font-size-12'>" . $_SESSION['text']['t_neueStation'] . "</p>";
        echo "</div>\n"; #p-3
        echo "</div>\n"; #col-5
        echo "<div class=\"col-12 col-lg-5\">\n";
        ?>
        <form class="needs-validation mx-3" method="post" id="stationNeu">
            <div class="border__bottom--dotted-gray mb-1">
                <input type="text" name="station" placeholder="<?= $_SESSION['text']['h_station'] ?>"
                       class="invisible-formfield" required>
            </div>
            <div class="border__bottom--dotted-gray mb-1">
                <input type="name" name="bezeichnung" placeholder="<?= $_SESSION['text']['h_bezeichnung'] ?>"
                       class="invisible-formfield" required>
            </div>
            <button class="btn btn-primary btn-sm mt-1 float-end"
                    type="submit"><?= $_SESSION['text']['b_speichern'] ?></button>
        </form>
        <?php
        echo "</div>\n"; #col-7
        echo "</div>\n"; # ROW
        echo "</div>\n";
        echo "</div>\n";
        echo "</div>\n";
    }

    # Anzeige Parallax
    public static function dspParallaxSmall(string $titel, string $subLine): void
    {
        echo "<div class=\"container-fluid\" id=\"parallax\">\n";
        echo "<div class=\"p-5\">\n";
        echo "<div class=\"small-white-box-left\">\n";
        echo "<p class=\"font-size-16 font-weight-300 oswald m-0 p-0 pt-5\">$titel</p>";
        echo "<h1 class=\"oswald hero pt-3\">\n";
        echo "$subLine\n";
        echo "</h1>\n";
        echo "</div>\n";
        echo "</div>\n";
        echo "</div>\n";
    }

    # Anzeige Parallax (ohne Text)
    public static function dspParallaxImage(): void
    {
        echo "<div class=\"parallax-window w-100 mt-85\">\n";
        echo "<div class=\"container-fluid\">\n";
        echo "</div><!-- container-fluid -->\n";
        echo "</div><!-- parallax -->\n";
    }

    # Base Head Elements
    public static function getHeadBase(): void
    {
        echo "<meta charset=\"UTF-8\">\n";
        echo "<meta name=\"viewport\" content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">\n";
        echo "<meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">\n";
        echo "<meta name=\"author\" content=\"Joachim Göddel . Rhenus LMS GmbH\">\n";
        echo "<meta name=\"description\" content=\"\">\n";
        echo "<link rel=\"stylesheet\" href=\"" . self::getBaseUrl() . "skin/plugins/node_modules/bootstrap/dist/css/bootstrap-reboot.min.css\">\n";
        echo "<link rel=\"stylesheet\" href=\"" . self::getBaseUrl() . "skin/plugins/node_modules/bootstrap/dist/css/bootstrap.min.css\">\n";
        echo "<link rel=\"stylesheet\" href=\"" . self::getBaseUrl() . "skin/plugins/node_modules/@fortawesome/fontawesome-free/css/all.css\">\n";
        echo "<link rel=\"stylesheet\" href=\"" . self::getBaseUrl() . "skin/plugins/ui/jquery-ui.css\">\n";
        echo "<link rel=\"stylesheet\" href=\"" . self::getBaseUrl() . "skin/plugins/other/toastr/toastr.css\">\n";
        echo "<link rel=\"stylesheet\" href=\"" . self::getBaseUrl() . "skin/plugins/other/switchbutton/bootstrap-switch-button.min.css\">\n";
        echo "<link rel=\"stylesheet\" href=\"" . self::getBaseUrl() . "skin/sass/main.css\">\n";
        echo "<link rel=\"icon\" href=\"" . self::getBaseUrl() . "skin/files/images/index.ico\">\n";
    }

    # Base Header mit Logo und Hinweisen
    public static function getHeaderBase(): void
    {
        echo "<header class=\"bg__rhenus--70\">\n";
        echo "<div class=\"container-fluid border__bottom--solid-gray_25 px-4\" id=\"main_header\">\n";
        echo "<div class=\"d-flex flex-row justify-content-between\" id=\"headline\">\n";
        echo "<div class=\"col-3\">\n";
        echo "<img src=\"" . self::getBaseUrl() . "skin/files/images/rhenus_logo_weiss.png\" alt=\"Logo\" class=\"pointer\" onclick=\"top.location.href='/'\">\n";
        echo "</div>\n";
        echo "<div class=\"col-9\">\n";
        echo "<div class=\"float-end\">\n";
        echo "<i class=\"fa fa-exclamation-triangle pt-3 px-2 font-size-20 text-warning\"></i>\n";
        echo "</div>\n";
        echo "</div>\n";
        echo "</div>\n";
        echo "</div>\n";
        echo "</header>\n";
    }

    # Base Header mit Logo und Hinweisen
    public static function getHeaderBasePage($bild): void
    {
        $bg = ($bild == 1) ? 'bg__rhenus--70' : 'bg__primary';
        echo "<header class=\"$bg\" id=\"fixTop\">\n";
        echo "<div class=\"container-fluid\" id=\"main_header\">\n";
        echo "<div class=\"d-flex flex-row justify-content-between px-4\" id=\"headline\">\n";
        echo "<div class=\"col-3\">\n";
        echo "<img src=\"" . self::getBaseUrl() . "skin/files/images/rhenus_logo_weiss.png\" alt=\"Logo\" class=\"pointer\" onclick=\"top.location.href='/'\">\n";
        echo "</div>\n";
        echo "<div class=\"col-9\">\n";
        echo "<div class=\"float-end\">\n";
        if (isset($_SESSION['user']['vorname'])) {
            echo "<p class=\"pt-2 oswald font-weight-300 text-uppercase\"><a href=\"/logout\" class=\"text-white\"><i class=\"fa fa-sign-out-alt\"></i> " . $_SESSION['text']['h_logout'] . "</a></p>";
        }
        echo "</div>\n"; # float end
        echo "</div>\n"; # col-9
        echo "</div>\n"; # flex-row
        echo "</div>\n"; # container-fluid
        echo "</header>\n";
    }

    # Navigation anzeigen
    public static function dspNavigation($dspedit, $seite, $id, $bild): void
    {
        $bg = ($bild == 1) ? 'bg__rhenus--70' : 'bg__primary';
        echo "<nav class=\"navbar navbar-expand-lg $bg sticky-nav w-100 mainnav px-4\" id=\"mnav\">\n";
        echo "<div class=\"container-fluid\">\n";
        echo "<button class=\"navbar-toggler\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#navbarABC\" aria-controls=\"navbarSupportedContent\" aria-expanded=\"false\" aria-label=\"Toggle navigation\">\n";
        echo "<span class=\"navbar-toggler-icon\"></span>\n";
        echo "</button>\n";
        echo "<div class=\"collapse border__top--dotted-white_75 navbar-collapse\" id=\"navbarABC\">\n";
        echo "<ul class=\"navbar-nav me-auto w-100\">\n";
        # Aktiver link
        $act = ($_SESSION['seite']['id'] == 0) ? ' active' : '';
        echo "<li class=\"nav-item\">\n";
        echo "<a class=\"nav-link $act\" aria-current=\"page\" href=\"/\"><i class='fa fa-home'></i></a>\n";
        echo "</li>\n\t";
        # Seiten einbinden
        foreach (AdministrationDatabase::getAllPages(0, 1) as $page) {
            # Berechtigung prüfen
            if (!empty($_SESSION['rechte'][$page->id]) && $_SESSION['rechte'][$page->id] == 1 || $page->adm == '0') {
                echo "<li class=\"nav-item\">\n";
                # Aktiver link
                $act = ($_SESSION['seite']['id'] == $page->id) ? ' active' : '';
                # Externer Link
                $ext = ($page->extern == 1) ? ' target="_blank"' : '';
                echo "<a class=\"nav-link $act\" aria-current=\"page\" $ext href=\"$page->link\">" . $_SESSION['text']['' . $page->i18n . ''] . "</a>\n";
                echo "</li>\n\t";
            }
        }
        echo "<li class=\"nav-item dropdown\">";
        echo "<a class=\"nav-link dropdown-toggle\" href=\"#\" id=\"anzeigen\" role=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\">";
        echo $_SESSION['text']['h_anzeigen'];
        echo "</a>";
        echo "<ul class=\"dropdown-menu font-size-12\" aria-labelledby=\"anzeigen\">";
        # Dropdownseiten einbinden
        foreach (AdministrationDatabase::getAllPages(9, 0) as $page) {
            # Externer Link
            $ext = ($page->extern == 1) ? ' target="_blank"' : '';
            if (!empty($_SESSION['rechte'][$page->id]) && $_SESSION['rechte'][$page->id] == 1 || $page->adm == '0') {
                echo "<a class=\"dropdown-item\" $ext href=\"$page->link\">" . $_SESSION['text']['' . $page->i18n . ''] . "</a>";
            }
        }
        echo "</ul>\n\t";
        /*
        echo "<li class=\"nav-item\">\n";
        echo "<a class=\"nav-link\" aria-current=\"page\" href=\"/\">{$_SESSION['lang']}</a>\n";
        echo "</li>\n\t";
        */
        echo "</ul>\n\t";
        echo "</div>\n\t"; # collapse
        echo "</div>\n\t"; # container fluid
        echo "</nav>\n\t";
    }


    # Base Footer Elements
    public static function getFooterBase(): void
    {
        echo "<footer class=\"footer mt-auto py-2 bg__primary font-size-10 w-100 text-white z10\" id=\"footer\">\n";
        echo "<div class=\"container-fluid\">\n";
        echo "<div class=\"row\">\n";
        echo "<div class=\"col-8\">\n";
        echo "<a onclick='setLanguage(\"de\");' class=\"pe-2 pointer\">DE</a> | <a onclick='setLanguage(\"en\");' class=\"px-2 pointer\">EN</a>\n";
        echo "<b class=\"px-2\">" . $_SESSION['page']['version'] . "</b>\n";
        if (isset($_SESSION['rechte']['1']) && $_SESSION['rechte']['1'] == 1) {
            echo "&bull;";
            echo "<span class=\"px-2\">";
            echo "<a href=\"/administration\" title=\"" . $_SESSION['text']['h_administration'] . "\">";
            echo $_SESSION['text']['h_administration'];
            echo "</a>\n";
            echo "</span>\n";
        }
        if (isset($_SESSION['user']['id'])) {
            echo "&bull;";
            echo "<span class=\"px-2\">";
            echo $_SESSION['user']['vorname'] . " " . $_SESSION['user']['name'];
            echo "</span>\n";
        }
        echo "</div>\n"; # col-8
        echo "<div class=\"col-4 text-end\">\n";
        echo "<b>Support</b>: Joachim Göddel ";
        echo "&bull; ";
        echo "<a href=\"mailto:" . $_SESSION['page']['supportMail'] . "\" title=\"\">";
        echo $_SESSION['page']['supportMail'];
        echo "</a>";
        echo "</div>\n"; # col-4
        echo "</div>\n"; # row
        echo "</div>\n"; # container-fluid
        echo "</footer>\n";
    }

    # Base JS Libs
    public static function getFooterJs(): void
    {
        echo "<script type=\"text/javascript\" src=\"" . self::getBaseUrl() . "skin/plugins/other/popper.js\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"" . self::getBaseUrl() . "skin/plugins/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"" . self::getBaseUrl() . "skin/plugins/node_modules/jquery/dist/jquery.min.js\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"" . self::getBaseUrl() . "skin/plugins/ui/jquery-ui.js\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"" . self::getBaseUrl() . "skin/plugins/node_modules/sweetalert2/dist/sweetalert2.all.js\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"" . self::getBaseUrl() . "skin/plugins/other/toastr/toastr.js\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"" . self::getBaseUrl() . "skin/plugins/other/switchbutton/bootstrap-switch-button.min.js\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"" . self::getBaseUrl() . "skin/plugins/node_modules/jquery.redirect/jquery.redirect.js\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"" . self::getBaseUrl() . "skin/plugins/parallax/jquery.parallax.min.js\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"" . self::getBaseUrl() . "skin/plugins/other/afix.js\"></script>\n";
        echo "<script type=\"text/javascript\" src=\"" . self::getBaseUrl() . "skin/js/base.js\"></script>\n";
    }

    # Ausgabe der Abteilung
    public static function dspAbteilung($id)
    {
        return $_SESSION['text']['abt_' . $id . ''];
    }

    # Array sortieren
    public static function array_sort($array, $on, $order = SORT_ASC): array
    {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    # Dateityp per Font Awesome ausgeben
    public static function dspFileType($typ): void
    {
        switch ($typ):
            case 'pdf':
                echo '<img src="' . Functions::getBaseURL() . 'skin/files/images/pdf1.png" class="img-fluid">';
                break;
            case 'xlsx':
            case 'xls':
                echo '<img src="' . Functions::getBaseURL() . 'skin/files/images/excel1.png" class="img-fluid">';
                break;
            case 'docx':
            case 'doc':
                echo '<img src="' . Functions::getBaseURL() . 'skin/files/images/word1.png" class="img-fluid">';
                break;
            case 'csv':
                echo '<img src="' . Functions::getBaseURL() . 'skin/files/images/csv1.png" class="img-fluid">';
                break;
            case 'msg':
                echo '<img src="' . Functions::getBaseURL() . 'skin/files/images/outlook1.png" class="img-fluid">';
                break;
            case 'txt':
                echo '<img src="' . Functions::getBaseURL() . 'skin/files/images/text1.png" class="img-fluid">';
                break;
            case 'ppt':
            case 'pptx':
                echo '<img src="' . Functions::getBaseURL() . 'skin/files/images/powerpoint1.png" class="img-fluid">';
                break;
        endswitch;
    }

    # Passwort erstellen
    function generatePassword($passwordlength = 10, $numNonAlpha = 0, $numNumberChars = 4, $useCapitalLetter = true): string
    {
        $numberChars = '123456789';
        $specialChars = '!%?*-_';
        $secureChars = 'abcdefghjkmnpqrstuvwxyz';
        $stack = '';
        // Stack für Password-Erzeugung füllen
        $stack = $secureChars;
        if ($useCapitalLetter == true) $stack .= strtoupper($secureChars);
        $count = $passwordlength - $numNonAlpha - $numNumberChars;
        $temp = str_shuffle($stack);
        $stack = substr($temp, 0, $count);
        if ($numNonAlpha > 0):
            $temp = str_shuffle($specialChars);
            $stack .= substr($temp, 0, $numNonAlpha);
        endif;
        if ($numNumberChars > 0):
            $temp = str_shuffle($numberChars);
            $stack .= substr($temp, 0, $numNumberChars);
        endif;
        // Stack durchwürfeln
        $stack = str_shuffle($stack);
        // Rückgabe des erzeugten Passwort
        return $stack;
    }

    # Ausgabe Wert aus MSQL Server
    public static function getSQLNumber($srv, $db, $uid, $pw, $tabelle)
    {
        $conn = ProduktionDatabase::connectSQL($srv, $db, $uid, $pw);
        if ($tabelle == 'Aktuell') {
            $sql = "SELECT TOP 1 * FROM CallOffs WHERE LineID = 1 ORDER BY Id_CallOff DESC";
        }
        if ($tabelle == 'Motorband') {
            $sql = "SELECT TOP 1 * FROM ActiveOnLine WHERE LineID = 1 ORDER BY Active DESC";
        }
        if ($tabelle == 'Kuehler') {
            $sql = "SELECT TOP 1 * FROM ActiveOnLine WHERE LineID = 26 ORDER BY Active DESC";
        }
        if ($tabelle == 'Bolster') {
            $sql = "SELECT TOP 1 * FROM ActiveOnLine WHERE LineID = 27 ORDER BY Active DESC";
        }
        if ($tabelle == 'AKL') {
            $sql = "SELECT TOP 1 * FROM calloffs WHERE statuscalloff = 1 AND LineID = '61' ORDER BY id_calloff DESC";
        }
        if ($tabelle == 'Frontcorner') {
            $sql = "SELECT TOP 1 * FROM ActiveOnLine ORDER BY Active DESC";
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_OBJ);
        #return $lastRotNr;
    }

    # Ausgabe Wert aus MSQL Server
    public static function getProduktionDifNeu($srv, $db, $uid, $pw, $tabelle, $lastRotPDO, $lastRotSQL)
    {
        $conn = ProduktionDatabase::connectSQL($srv, $db, $uid, $pw);
        if ($tabelle == 'Motorband') {
            $sql = "SELECT TOP 1 * FROM ActiveOnLine WHERE LineID = 1 ORDER BY Active DESC";
        }
        if ($tabelle == 'Kuehler') {
            $sql = "SELECT TOP 1 * FROM ActiveOnLine WHERE LineID = 26 ORDER BY Active DESC";
        }
        if ($tabelle == 'Bolster') {
            $sql = "SELECT TOP 1 * FROM ActiveOnLine WHERE LineID = 27 ORDER BY Active DESC";
        }
        if ($tabelle == 'AKL') {
            $sql = "SELECT TOP 1 * FROM calloffs WHERE statuscalloff = 1 AND LineID = '61' ORDER BY id_calloff DESC";
        }
        if ($tabelle == 'Frontcorner') {
            $sql = "SELECT TOP 1 * FROM ActiveOnLine ORDER BY Active DESC";
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $lastRotLine = $stmt->fetch(\PDO::FETCH_OBJ)->ExtraSequence;
        return ((int)$lastRotLine > $lastRotSQL) ? $lastRotPDO->ExtraSequence - $lastRotLine + $lastRotSQL : $lastRotSQL - $lastRotLine;
    }

    # Berechtigungen ermitteln
    public static function getBerechtigung($ebene): array # Berechtigung auf der jeweilgen Seite
    {
        $seiteadmin = 0;
        # Leserechte
        $seitelesen = (isset($_SESSION['rechte']['' . $ebene . '.1']) && $_SESSION['rechte']['' . $ebene . '.1'] == 1) ? 1 : 0;

        # Schreib- und Adminrechte
        if (isset($_SESSION['rechte']['' . $ebene . '.0']) && $_SESSION['rechte']['' . $ebene . '.0'] == 1): // Admin Rechte
            $seiteschreiben = 1;
            $seitelesen = 1;
            $seiteadmin = 1;
        else:
            if (isset($_SESSION['rechte']['' . $ebene . '.2']) && $_SESSION['rechte']['' . $ebene . '.2'] == 1): // Schreibrechte
                $seiteschreiben = 1;
                $seitelesen = 1;
            else:
                $seiteschreiben = 0;
            endif;
        endif;
        return array($seitelesen, $seiteschreiben, $seiteadmin);
    }

    # Berechtigungen ermitteln
    public static function checkBerechtigung($ebene): int
    {
        $seiteadmin = 0;
        # Leserechte
        $seitelesen = (isset($_SESSION['rechte']['' . $ebene . '.1']) && $_SESSION['rechte']['' . $ebene . '.1'] == 1) ? 1 : 0;

        # Schreib- und Adminrechte
        if (isset($_SESSION['rechte']['' . $ebene . '.0']) && $_SESSION['rechte']['' . $ebene . '.0'] == 1): // Admin Rechte
            $seiteschreiben = 1;
            $seitelesen = 1;
            $seiteadmin = 1;
        else:
            if (isset($_SESSION['rechte']['' . $ebene . '.2']) && $_SESSION['rechte']['' . $ebene . '.2'] == 1): // Schreibrechte
                $seiteschreiben = 1;
                $seitelesen = 1;
            else:
                $seiteschreiben = 0;
            endif;
        endif;
        return $seitelesen + $seiteschreiben + $seiteadmin;
    }

    /**
     * @throws Exception
     */
    public static function getZeitschieneSchicht($datum): int
    {
        // Kalenderwoche
        $date = new DateTime('' . $datum . '');
        $kw = $date->format('W');
        if(!isset($_SESSION['user']['wrk_schicht'])) $_SESSION['user']['wrk_schicht'] = 1;
        // Gerade oder ungerade KW
        if ($kw % 2 == 0) :
            if ($_SESSION['user']['wrk_schicht'] == 1) :
                return 1;
            else:
                return 2;
            endif;
        else :
            if ($_SESSION['user']['wrk_schicht'] == 1) :
                return 2;
            else:
                return 1;
            endif;
        endif;
    }

    # Dezimal zu Zeit
    public static function clockalize($in): string
    {

        $h = intval($in);
        $m = round((((($in - $h) / 100.0) * 60.0) * 100), 0);
        if ($m == 60) {
            $h++;
            $m = 0;
        }
        return sprintf("%02d:%02d", $h, $m);
    }

    # Sekunden zu Zeit
    public static function sek2Time($a)
    {
        $a = abs($a);
        return sprintf("%02d:%02d:%02d", $a/60/60/24,($a/60/60)%24,($a/60)%60,$a%60);
    }
    # Sommerzeit
    public static function sommerzeit($jahr,$datum)
    {
        date_default_timezone_set("Europe/Berlin");

        $startSummertime = date_create('last Sunday of March '.$jahr.' 02:00');
        $endSummertime = date_create('last Sunday of October '.$jahr.' 03:00');
        $startSummertime = $startSummertime->format('Y-m-d');
        $endSummertime = $endSummertime->format('Y-m-d');
        return ($startSummertime < $datum && $endSummertime > $datum) ? 1 : 0;
    }

    # HTML ----------------------------
    public static function htmlOpenDiv($col, $side, $art, $align = "", $m = "", $p = "", $pi = ""): void
    {
        echo "<div class='col-$col border__$side--$art-gray $m $p $align'>";
        echo "<div class='$pi'>";
    }

    public static function htmlOpenDivNoBorder($col, $align = "", $m = "", $p = "", $pi = ""): void
    {
        echo "<div class='col-$col $m $p $align'>";
        echo "<div class='$pi'>";
    }

    public static function htmlOpenSingleDiv($class, $m = "", $p = "", $align = ""): void
    {
        echo "<div class=\"$class $m $p $align\">";
    }

    public static function htmlOpenSingleDivID($id, $class, $m = "", $p = "", $align = ""): void
    {
        echo "<div id=\"$id\" class=\"$class $m $p $align\">";
    }

    public static function htmlOpenBorderDiv($class, $side = "", $art = "dotted", $color = "gray", $opacity = "", $pi = "", $m = "", $p = "", $align = ""): void
    {
        echo "<div class=\"$class border__$side--$art-{$color}_{$opacity} $m $p $align\">";
        echo "<div class=\"$pi\">";
    }

    public static function htmlOpenBorderDiv2($class, $side = "", $art = "dotted", $color = "gray", $opacity = "", $pi = "", $m = "", $p = "", $align = ""): void
    {
        echo "<div class=\"$class border__$side--$art-{$color} $m $p $align\">";
    }

    public static function htmlOpenDivAction($class, $action, $id = ""): void
    {
        echo "<div id=\"$id\" class=\"$class\" $action>";
    }

    public static function alert($text): void
    {
        echo "<div class='alert alert-muted font-size-11'>";
        echo $text;
        echo "</div>";
    }

    public static function alertVar($text, $var): void
    {
        echo "<div class='alert alert-muted font-size-11'>";
        echo sprintf($text, $var);
        echo "</div>";
    }

    public static function alertIcon($icon, $text): void
    {
        echo "<div class='alert alert-muted font-size-11'>";
        echo "<div class='row'>";
        echo "<div class='col-2 text-center'>";
        echo "<i class='fa fa-$icon fa-3x'></i>";
        echo "</div>";
        echo "<div class='col-10'>";
        echo $text;
        echo "</div>";
        echo "</div>";
        echo "</div>";
    }

    public static function htmlCloseDiv(): void
    {
        echo "</div>\n";
        echo "</div>\n";
    }

    public static function htmlCloseSingleDiv(): void
    {
        echo "</div>\n";
    }

    public static function modalHinweis(): void
    {
        echo "
        <div class='modal fade' id='modalHinweis' data-bs-backdrop='static' data-bs-keyboard='false' tabindex='-1'
     aria-labelledby='modalHinweis' aria-hidden='true'>
            <div class='modal-dialog modal-fullscreen'>
                <div class='modal-content bg__white--85'>
                    <div class='hinweis_container'>
                        <div class='col-4 text-center'>
                            <h2 class='oswald font-weight-100 font-size-30 pb-4 mb-4 border__bottom--dotted-gray'>{$_SESSION['text']['h_augenblick']}</h2>
                            <i class='fa fa-cog fa-spin fa-5x text-primary'></i>
                            <p class='oswald font-weight-100 font-size-20 pt-4 mt-4 border__top--dotted-gray'>{$_SESSION['text']['t_augenblick']}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        ";
    }

    # Funktion Personal ausgeben
    public static function getFunktionPersonal($id): string
    {
        return match ($id) {
            0 => '&nbsp;',
            1 => 'Teamleiter',
            2 => 'Stelv. Teamleiter',
            3 => 'Qualitätssicherung',
            4 => 'Material',
        };
    }

    public static function warten(): void
    {
        echo "
        <div class='p-4 text-center border__dotted--gray_50 border__radius--10 bg-light-lines'>
            <i class='fa fa-cog fa-spin text-muted'></i><br>
            <p class='font-size-10 itlic'><b>Einen Augenblick bitte</b><br>Die erforderlichen Daten werden zusammengestellt.</p>
        </div>
        ";
    }

    public static function pre($var): void
    {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

    # Kalender (Monat zusammenstellen)
    public static function buildMonth($start, $tage): DatePeriod
    {
        setlocale(LC_TIME, 'deu_deu');
        # Startdatum letzter Vormonat
        $date = new DateTime("$start");
        # Interval defineren
        $interval = DateInterval::createFromDateString('+1 day');
        # Die Periode erstellen
        return new DatePeriod($date, $interval, $tage, DatePeriod::EXCLUDE_START_DATE);

    }

    # FORMULARE
    # input
    public static function invisibleInput($type, $id, $class = '', $value = '', $action = '', $status = '', $placeholder = '', $max = '', $min = '', $required = ''): void
    {
        echo "<input type='$type' name='$id' id = '$id' class='invisible-formfield $class' value='$value' $action $status placeholder='$placeholder' max='$max' min='$min' $required>";
    }

    public static function invisibleDataList($name, $id, $class = '', $value = ''): void
    {
        echo "<input list='$name' id='$id' name='$id' class='invisible-formfield $class' value='$value'>";
    }

    public static function hiddenField($id, $value): void
    {
        echo "<input type='hidden' name='$id' value='$value'>";
    }


    # Service Desk
    # Status Feld
    public static function dspStatusFeld($datum, $text, $icon, $user)
    {
        echo "
        <div class=\"row border__bottom--dotted-gray mb-3 pb-3\">
        <div class=\"col-2 border__right--dotted-gray\">
        <div class=\"p-1 text-center\">
        <i class=\"fa fa-$icon-circle text-muted fa-2x\"></i>
        </div><!-- p-3 -->
        </div><!-- col -->
        <div class=\"col-10\">
        <div class=\"ps-3\">
        <p class=\"p-0 m-0\">$text</p>
        <p class=\"p-0 m-0 font-size-11 italic text-gray\">$user &bull; $datum</p>
        </div><!-- ps-3 -->
        </div><!-- col -->
        </div><!-- row -->
        ";
    }
    # Datei Feld
    public static function dspDateiFeld($datum, $text, $icon, $user, $id)
    {
        # Datei von angemeldetem User ?
        $dlt = ($_SESSION['user']['dbname'] == $user) ? '<i class="fa fa-trash text-muted pointer" onclick="deleteFile(\''.$id.'\',\''.$text.'\',\''.$_SESSION['text']['i_deleteFile'].'\')"></i>' : '';
        echo "
        <div class=\"row border__bottom--dotted-gray mb-3 pb-3\">
        <div class=\"col-2 border__right--dotted-gray pointer\" onclick='window.open(\"" . Functions::getBaseURL() . "/lib/Pages/Servicedesk/MVC/View/files/$text\")';>
        <div class=\"p-1 text-center\">
        <i class=\"fa $icon text-muted fa-2x\"></i>
        </div><!-- p-3 -->
        </div><!-- col -->
        <div class=\"col-10\">
        <div class=\"ps-3\">
        <p class=\"p-0 m-0\">$text</p>
        <p class=\"p-0 m-0 font-size-11 italic text-gray\">$user &bull; $datum <span class='float-end'>$dlt</span></p>
        </div><!-- ps-3 -->
        </div><!-- col -->
        </div><!-- row -->
        ";
    }
    # Status Button
    public static function dspStatusButton($text, $icon, $action, $color = 'primary')
    {
        echo "
        <div class=\"row border__bottom--dotted-gray mb-3 pb-3 pointer\" onclick=\"$action\">
        <div class=\"col-2 border__right--dotted-gray\">
        <div class=\"p-1 text-center\">
        <i class=\"fa fa-$icon-circle text-$color fa-2x\"></i>
        </div><!-- p-3 -->
        </div><!-- col -->
        <div class=\"col-10\">
        <div class=\"ps-3\">
        <p class=\"p-0 m-0 pt-2\">$text</p>
        </div><!-- ps-3 -->
        </div><!-- col -->
        </div><!-- row -->
        ";
    }

    # Arry nach Attribut sortieren
    public static function build_sorter($key)
    {
        return function ($a, $b) use ($key) {
            return strnatcmp($a[$key], $b[$key]);
        };
    }
}