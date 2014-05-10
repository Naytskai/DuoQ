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

if ($_POST['methode'] == "setSumLane" && $_POST['resultId'] != "" && $_POST['laneName'] != "") {
    $duoManager = new DuoManager($db);
    $resultId = $_POST['resultId'];
    $laneName = $_POST['laneName'];
    $laneId = $duoManager->getLaneId($laneName);
    $duoManager->addLaneToResult($resultId, $laneId);
    echo $laneName;
}