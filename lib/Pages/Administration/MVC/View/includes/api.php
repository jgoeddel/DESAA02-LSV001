<?php
if(isset($_COOKIE['Token'])){
    session_id($_COOKIE['Token']); //sette den token als ID
}
session_start(); //Lade session
$userAcces = $_SESSION['userAccess'];//wenn Token valide ist, stehen in $_SESSION daten drin
$responseArray = [];
$responseArray = array_merge($responseArray,[$_SERVER['REQUEST_METHOD']]);
$responseArray = array_merge($responseArray,$_GET);
$responseArray = array_merge($responseArray,$_POST);
$responseArray = array_merge($responseArray,$_COOKIE);


header('Content-Type:application/json;charset=utf-8');
echo json_encode($responseArray);