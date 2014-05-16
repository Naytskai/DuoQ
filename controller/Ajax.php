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
include_once '../model/StatsDisplayer.php';
LolApi::init($db);


//------------------------------------------------------------------------------
//                         AJAX PHP FUNCTIONS                            
//------------------------------------------------------------------------------

/*
 * Set a lane by result to a summoner
 */
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

/*
 * Unlink a summoner by id
 */
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

/*
 * Remove a duo by id
 */
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

/*
 * Call the lolApi for an update by duo's player
 */
if ($_POST['function'] == "updateDuo" && $_POST['sumName1'] != "" && $_POST['sumName2'] != "") {
    if ($_SESSION['loggedUserObjectDuoQ']) {
        LolApi::getDuoRankedGames($_POST['sumName1'], $_POST['sumName2']);
        echo "refresh Ranked OK";
    } else {
        echo "You need to be logged first";
    }
}

/*
 * Return all the matches by duo
 */
if ($_POST['function'] == "displayAllRefreshedDuo" && $_POST['sumName1'] != "" && $_POST['sumName2'] != "") {
    if ($_SESSION['loggedUserObjectDuoQ']) {
        $duoManager = new DuoManager($db);
        $sum1 = $duoManager->getSummonerByNameFromDb($_POST['sumName1']);
        $sum2 = $duoManager->getSummonerByNameFromDb($_POST['sumName2']);
        $duo2R = new Duo(array());
        $duo2R = $duoManager->getDuoByMembers($sum1['pkSummoner'], $sum2['pkSummoner']);
        $statsDisplay = new StatsDisplayer($db);
        $matches = $statsDisplay->displayMatches($db, $duo2R->getPkDuo());
        echo $matches;
    } else {
        echo "You need to be logged first";
    }
}

/*
 * Return the total duo gaming time
 */
if ($_POST['function'] == "displayGamingTime" && $_POST['sumName1'] != "" && $_POST['sumName2'] != "") {
    if ($_SESSION['loggedUserObjectDuoQ']) {
        $duoManager = new DuoManager($db);
        $sum1 = $duoManager->getSummonerByNameFromDb($_POST['sumName1']);
        $sum2 = $duoManager->getSummonerByNameFromDb($_POST['sumName2']);
        $duo2R = new Duo(array());
        $duo2R = $duoManager->getDuoByMembers($sum1['pkSummoner'], $sum2['pkSummoner']);
        $statsDisplay = new StatsDisplayer($db);
        $gamingTime = $statsDisplay->getTotalGamingTime($db, $duo2R->getPkDuo());
        echo $gamingTime;
    } else {
        echo "You need to be logged first";
    }
}

/*
 * Return the total duo's wins
 */
if ($_POST['function'] == "displayTotalWins" && $_POST['sumName1'] != "" && $_POST['sumName2'] != "") {
    if ($_SESSION['loggedUserObjectDuoQ']) {
        $duoManager = new DuoManager($db);
        $sum1 = $duoManager->getSummonerByNameFromDb($_POST['sumName1']);
        $sum2 = $duoManager->getSummonerByNameFromDb($_POST['sumName2']);
        $duo2R = new Duo(array());
        $duo2R = $duoManager->getDuoByMembers($sum1['pkSummoner'], $sum2['pkSummoner']);
        $statsDisplay = new StatsDisplayer($db);
        $totalWins = $statsDisplay->getTotalWins($db, $duo2R->getPkDuo());
        echo $totalWins;
    } else {
        echo "You need to be logged first";
    }
}

/*
 * Return the total duo's defeats
 */
if ($_POST['function'] == "displayTotalLoose" && $_POST['sumName1'] != "" && $_POST['sumName2'] != "") {
    if ($_SESSION['loggedUserObjectDuoQ']) {
        $duoManager = new DuoManager($db);
        $sum1 = $duoManager->getSummonerByNameFromDb($_POST['sumName1']);
        $sum2 = $duoManager->getSummonerByNameFromDb($_POST['sumName2']);
        $duo2R = new Duo(array());
        $duo2R = $duoManager->getDuoByMembers($sum1['pkSummoner'], $sum2['pkSummoner']);
        $statsDisplay = new StatsDisplayer($db);
        $totalLooses = $statsDisplay->getTotalDefeat($db, $duo2R->getPkDuo());
        echo $totalLooses;
    } else {
        echo "You need to be logged first";
    }
}

/*
 * Return the total damages dealt by duo id
 */
if ($_POST['function'] == "displayTotalDoms" && $_POST['sumName1'] != "" && $_POST['sumName2'] != "") {
    if ($_SESSION['loggedUserObjectDuoQ']) {
        $duoManager = new DuoManager($db);
        $sum1 = $duoManager->getSummonerByNameFromDb($_POST['sumName1']);
        $sum2 = $duoManager->getSummonerByNameFromDb($_POST['sumName2']);
        $duo2R = new Duo(array());
        $duo2R = $duoManager->getDuoByMembers($sum1['pkSummoner'], $sum2['pkSummoner']);
        $statsDisplay = new StatsDisplayer($db);
        $totalDoms = $statsDisplay->getTotalDomDealt($db, $duo2R->getPkDuo());
        echo $totalDoms;
    } else {
        echo "You need to be logged first";
    }
}

/*
 * Return the total gold earn by duo Id
 */
if ($_POST['function'] == "displayTotalGold" && $_POST['sumName1'] != "" && $_POST['sumName2'] != "") {
    if ($_SESSION['loggedUserObjectDuoQ']) {
        $duoManager = new DuoManager($db);
        $sum1 = $duoManager->getSummonerByNameFromDb($_POST['sumName1']);
        $sum2 = $duoManager->getSummonerByNameFromDb($_POST['sumName2']);
        $duo2R = new Duo(array());
        $duo2R = $duoManager->getDuoByMembers($sum1['pkSummoner'], $sum2['pkSummoner']);
        $statsDisplay = new StatsDisplayer($db);
        $totalGold = $statsDisplay->getTotalGold($db, $duo2R->getPkDuo());
        echo $totalGold;
    } else {
        echo "You need to be logged first";
    }
}

//------------------------------------------------------------------------------
//                         CRON PHP FUNCTIONS                            
//------------------------------------------------------------------------------
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