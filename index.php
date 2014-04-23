<?php

session_start();
include_once 'model/MysqlConnect.php';

$l = isset($_GET['l']) ? $_GET['l'] : false;

switch ($l) {
    case false:
        include_once('controller/cIndex.php');
        break;
    case "login":
        include_once('controller/cLogin.php');
        break;
    case "logout":
        include_once('controller/cLogout.php');
        break;
    case "parameters";
        include_once ('controller/cParameters.php');
        break;
    case "stats";
        include_once ('controller/cStats.php');
        break;
    case "duo";
        include_once ('controller/cDuo.php');
        break;
    case "addSumm";
        include_once ('controller/cAddSumm.php');
        break;
}