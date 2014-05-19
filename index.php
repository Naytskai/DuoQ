<?php

session_start();
include_once getenv('APP_DUOQ_ROOT_PATH') . '/model/MysqlConnect.php';
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
    case "settings";
        include_once ('controller/cSettings.php');
        break;
    case "stats";
        include_once ('controller/cStats.php');
        break;
    case "newDuo";
        include_once ('controller/cNewDuo.php');
        break;
    case "duo";
        include_once ('controller/cDuo.php');
        break;
    case "addSumm";
        include_once ('controller/cAddSumm.php');
        break;
    case "confSum";
        include_once ('controller/cConfSumm.php');
        break;
    case "mySum";
        include_once ('controller/cMySumm.php');
        break;
    case "sharing";
        include_once ('controller/cSharing.php');
        break;
}