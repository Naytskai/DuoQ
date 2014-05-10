<?php

//------------------------------------------------------------------------------
//                         Includes & variables                            
//------------------------------------------------------------------------------
include_once '../model/User.php';
include_once '../model/UserManager.php';
include_once '../model/api/LolApi.php';
include_once '../model/Duo.php';
include_once '../model/DuoManager.php';
include_once '../model/MysqlConnect.php';
LolApi::init($db);
if ($_POST['methode'] == "setSumLane" && $_POST['resultId'] != "" && $_POST['laneName'] != "") {
    $duoManager = new DuoManager($db);
    $resultId = $_POST['resultId'];
    $laneName = $_POST['laneName'];
    $laneId = $duoManager->getLaneId($laneName);
    $duoManager->addLaneToResult($resultId, $laneId);
    echo $laneName;
}

if ($_POST['methode'] == "refreshAllDuo") {
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