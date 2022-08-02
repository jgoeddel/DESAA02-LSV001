<?php
/** (c) Joachim Göddel . RLMS */

# Basisparameter
$_SESSION['page']['location'] = "dev"; // 'dev' = Entwicklungsumgebung, 'sap' = Liveumgebung
$_SESSION['page']['url'] = "http://desaa02-lsv001/";
$_SESSION['page']['pfad'] = "/";
$_SESSION['page']['supportMail'] = "joachim.goeddel@de.rhenus.com";
$_SESSION['page']['version'] = "Rhenus LMS GmbH &bull; Intranet 5.0";
# Produktionslinien (Anzeige)
$_SESSION['page']['linien'] = array("Motorband", "Frontcorner", "Kuehler", "Bolster", "AKL");

# Mysql
$_SESSION['db']['user'] = 'rhs';
$_SESSION['db']['pass'] = 'LMS%jg_01';

# Parameter
$_SESSION['parameter']['jahr'] = DATE('Y');
$_SESSION['parameter']['monat'] = DATE('m');
$_SESSION['parameter']['tag'] = DATE('d');
$_SESSION['parameter']['heuteSQL'] = DATE('Y-m-d');
$_SESSION['parameter']['start'] = '2020';
$_SESSION['parameter']['abc'] = array('A', 'Ä', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'Ö', 'P', 'Q', 'R', 'S', 'T', 'U', 'Ü', 'V', 'W', 'X', 'Y', 'Z');
$_SESSION['parameter']['tapeletters'] = array("","A","B","A","C","A","B","A","D","A","B","A","C","A","B","A","E");
$_SESSION['parameter']['anfangszeit'] = array('00:00:00', '01:00:00', '02:00:00', '03:00:00', '04:00:00', '05:00:00', '06:00:00', '07:00:00', '08:00:00', '09:00:00', '10:00:00', '11:00:00', '12:00:00', '13:00:00', '14:00:00', '15:00:00', '16:00:00', '17:00:00', '18:00:00', '19:00:00', '20:00:00', '21:00:00', '22:00:00', '23:00:00');
$_SESSION['parameter']['endzeit'] = array('00:59:59', '01:59:59', '02:59:59', '03:59:59', '04:59:59', '05:59:59', '06:59:59', '07:59:59', '08:59:59', '09:59:59', '10:59:59', '11:59:59', '12:59:59', '13:59:59', '14:59:59', '15:59:59', '16:59:59', '17:59:59', '18:59:59', '19:59:59', '20:59:59', '21:59:59', '22:59:59', '23:59:59');
$_SESSION['i18n']['tage'] = array('montag','dienstag','mittwoch','donnerstag','freitag','samstag','sonntag');
$_SESSION['i18n']['monate'] = array('januar','februar','maerz','april','mai','juni','juli','august','september','oktober','november','dezember');
$_SESSION['de']['tage'] = array('Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag','Sonntag');
$_SESSION['de']['monate'] = array('Januar','Februar','Maerz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember');
$_SESSION['en']['tage'] = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
$_SESSION['en']['monate'] = array('January','February','March','April','May','June','July','August','September','October','November','December');
$_SESSION['parameter']['zeitschienen'] = 3;

# Serverzugriffe
$_SESSION['mkspts']['server'] = "172.16.101.51, 1433";
$_SESSION['mkspts']['database'] = "PTS";
$_SESSION['mkspts']['uid'] = "PTS2";
$_SESSION['mkspts']['pwd'] = "PTS2";
#
$_SESSION['akl']['server'] = "172.16.101.51, 1433";
$_SESSION['akl']['database'] = "pts_teppich";
$_SESSION['akl']['uid'] = "PTSLMS";
$_SESSION['akl']['pwd'] = "PTSLMS";
#
$_SESSION['corner']['server'] = "172.16.101.101, 1433";
$_SESSION['corner']['database'] = "PTSFC";
$_SESSION['corner']['uid'] = "PTS2";
$_SESSION['corner']['pwd'] = "PTS";

# Wenn keine Sessionparameter vorhanden sind sollen die aktuellen Werte übernommmen werden
if (!isset($_SESSION['wrk']['jahr']) || $_SESSION['wrk']['jahr'] < $_SESSION['parameter']['start']) $_SESSION['wrk']['jahr'] = $_SESSION['parameter']['jahr'];
if (!isset($_SESSION['wrk']['datum'])) $_SESSION['wrk']['datum'] = $_SESSION['parameter']['heuteSQL'];
if (!isset($_SESSION['wrk']['monat']) || ($_SESSION['wrk']['jahr'] == $_SESSION['parameter']['jahr'] && $_SESSION['wrk']['monat'] > $_SESSION['parameter']['monat'])) $_SESSION['wrk']['monat'] = $_SESSION['parameter']['monat'];

# Erzeugt eine zufällige ID
$a = DATE('YmdHis');
$b = mt_rand();
$c = $a."|".$b;
$d = md5($c);
$_SESSION['seite']['sess'] = $d; // wird in den meisten Funktionen benötigt

// Wenn keine Sessionparameter vorhanden sind sollen die parameteren Werte übernommmen werden
if(!isset($_SESSION['wrk']['jahr']) || $_SESSION['wrk']['jahr'] < $_SESSION['parameter']['start']):
    $_SESSION['wrk']['jahr'] = $_SESSION['parameter']['jahr'];
endif;
if(!isset($_SESSION['wrk']['datum'])):
    $_SESSION['wrk']['datum'] = $_SESSION['parameter']['datum'];
endif;
if(!isset($_SESSION['wrk']['monat']) || ($_SESSION['wrk']['jahr'] == $_SESSION['parameter']['jahr'] && $_SESSION['wrk']['monat'] > $_SESSION['parameter']['monat'])):
    $_SESSION['wrk']['monat'] = $_SESSION['parameter']['monat'];
endif;

// Wenn Parameter übergeben werden müssen die Sessionparameter ersetzt werden
if(!isset($_SESSION['wrk']['datum'])): $_SESSION['wrk']['datum'] = DATE('Y-m-d'); endif;
if(!empty($_GET['datum'])): // Datum
    $_SESSION['wrk']['datum'] = $_GET['datum'];
endif;
if(empty($_SESSION['wrk']['tag'])): $_SESSION['wrk']['tag'] = DATE('d'); endif;
if(!empty($_GET['tag'])): // Tag
    $_SESSION['wrk']['tag'] = $_GET['tag'];
    $_SESSION['wrk']['datum'] = $_SESSION['wrk']['jahr']."-".$_SESSION['wrk']['monat']."-".$_SESSION['wrk']['tag'];
endif;
if(!empty($_GET['monat'])): // Monat
    if($_SESSION['wrk']['jahr'] == $_SESSION['parameter']['jahr'] && $_GET['monat'] > $_SESSION['parameter']['monat']):
        $_SESSION['wrk']['monat'] = $_SESSION['parameter']['monat'];
    else:
        $_SESSION['wrk']['monat'] = $_GET['monat'];
    endif;
endif;
if(!empty($_GET['jahr'])): // Jahr
    if($_GET['jahr'] < $_SESSION['parameter']['start']):
        $_SESSION['wrk']['jahr'] = $_SESSION['parameter']['jahr'];
        $_SESSION['wrk']['monat'] = $_SESSION['parameter']['monat'];
    else:
        $_SESSION['wrk']['jahr'] = $_GET['jahr'];
        if($_SESSION['wrk']['jahr'] == $_SESSION['parameter']['jahr'] && $_SESSION['wrk']['monat'] > $_SESSION['parameter']['monat']):
            $_SESSION['wrk']['monat'] = $_SESSION['parameter']['monat'];
        endif;
    endif;
endif;
// Anzahl der Monate
($_SESSION['wrk']['jahr'] == $_SESSION['parameter']['jahr']) ? $dspMonat = DATE('n') : $dspMonat = '12';
