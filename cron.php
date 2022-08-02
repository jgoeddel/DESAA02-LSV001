<?php
/** (c) Joachim Göddel . RLMS */
use App\App\Container;

require_once "init.php";
# Container
$Container = new Container;
$router = $Container->build("router");
$pfad = $_SESSION['page']['pfad'];
#$request = $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'];
$request = $_SERVER['PATH_INFO'] ?? "/";
# CRONJOBS
$router->add("emailController", "sendEmailCMDaily");