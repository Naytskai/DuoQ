<?php

session_start();
//------------------------------------------------------------------------------
//                         Includes & variables                            
//------------------------------------------------------------------------------
include_once '../model/User.php';
include_once '../model/UserManager.php';
include_once '../model/api/LolApi.php';
include_once '../model/Duo.php';
include_once '../model/DuoManager.php';
include_once '../model/MysqlConnect.php';
include_once '../model/AjaxSecrets.php';
LolApi::init($db);
if ($_POST['function'] == "setSumLane" && $_POST['resultId'] != "" && $_POST['laneName'] != "") {
    if ($_SESSION['loggedUserObjectDuoQ']) {
        $duoManager = new DuoManager($db);
        $resultId = $_POST['resultId'];
        $laneName = $_POST['laneName'];
        $laneId = $duoManager->getLaneId($laneName);
        $duoManager->addLaneToResult($resultId, $laneId);
        echo $laneName;
    } else {
        echo "You need to be logged first";
    }
}

if ($_POST['function'] == "unlinkSum" && $_POST['sumId'] != "") {
    if ($_SESSION['loggedUserObjectDuoQ']) {
        $loggedUser = new User(array());
        $loggedUser = unserialize($_SESSION['loggedUserObjectDuoQ']);
        $userManager = new UserManager($db);
        $sumId = $_POST['sumId'];
        $userManager->unLinkSummonerUser($loggedUser, $sumId);
        echo "ok";
    } else {
        echo "You need to be logged first";
    }
}

if ($_POST['function'] == "removeDuo" && $_POST['duoId'] != "") {
    if ($_SESSION['loggedUserObjectDuoQ']) {
        $loggedUser = new User(array());
        $loggedUser = unserialize($_SESSION['loggedUserObjectDuoQ']);
        $duoManager = new DuoManager($db);
        $duoId = $_POST['duoId'];
        $info = $duoManager->removeDuo($duoId);
        echo $info;
    } else {
        echo "You need to be logged first";
    }
}

if ($_POST['methode'] == "refreshAllDuo" && $_POST['token'] == $ajaxToken) {
    $duoManager = new DuoManager($db);
    $duoArray = $duoManager->getAllDuo();
    for ($i = 0; $i < count($duoArray); $i++) {
        $duo = new Duo(array());
        $duo = $duoArray[$i];
        $sum1 = $duoManager->getSummonerFromDb($duo->getPlayerOneDuo());
        $sum2 = $duoManager->getSummonerFromDb($duo->getPlayerTwoDuo());
        LolApi::getDuoRankedGames($sum1, $sum2);
    }
    echo "refresh ended";
}

if ($_POST['methode'] == "refreshAllChampions" && $_POST['token'] == $ajaxToken) {
    LolApi::getChampions();
    echo "refresh champions ended";
}