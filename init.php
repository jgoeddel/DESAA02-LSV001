<?php
/** (c) Joachim Göddel . RLMS */

require_once "autoloader.php";

function html(string $str): string
{
    return htmlentities($str, ENT_QUOTES, 'UTF-8');
}

# Session starten, wenn noch nicht geschehen
if (session_status() == PHP_SESSION_NONE) session_start();

# Einstellungen PHP
setlocale(LC_ALL, 'German', 'de_DE@euro', 'de_DE', 'deu_deu', 'de', 'ge');
setlocale(LC_TIME, 'German');
date_default_timezone_set('Europe/Berlin');
# php.ini
ini_set("SMTP", "cau-mailrelay.service.rhs.zz");
ini_set('display_errors', true);
ini_set('soap.wsdl_cache_enabled',0);
ini_set('soap.wsdl_cache_ttl',0);
# Basispfad Projekt
define('BASEPATH', dirname(__FILE__));
// Projektverzeichnis
const PATH_PROJECT = '/';

# Funktionen einbinden
require_once "lib/Functions/array.func.php";
require_once "lib/Functions/func.php";
require_once "lib/Functions/session.func.php";

# Sprache einstellen
if(!isset($_SESSION['user']['lang']) || $_SESSION['user']['lang'] == '') {
    $lang = \App\Pages\Home\IndexDatabase::getBrowserLanguage();
    \App\Pages\Home\IndexDatabase::setLanguageSession('de', 'a_i18n');
}