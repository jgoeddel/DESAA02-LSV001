<?php
/** (c) Joachim Göddel . RLMS */
use App\App\Container;
use App\Pages\Produktion\ProduktionDatabase;

require_once "init.php";
# Container
$Container = new Container;
$router = $Container->build("router");
$pfad = $_SESSION['page']['pfad'];
#$request = $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'];
$request = $_SERVER['PATH_INFO'] ?? "/";
# CRONJOBS
# $router->add("emailController", "sendEmailCMDaily"); # Change Management tägliche Mail

# Call Offs einbinden (alle 30 Minuten)
ProduktionDatabase::cronCallOffs($_SESSION['mkspts']['server'],
    $_SESSION['mkspts']['database'],
    $_SESSION['mkspts']['uid'],
    $_SESSION['mkspts']['pwd']);

# Status Handicap