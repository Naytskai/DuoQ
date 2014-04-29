<?php

include_once 'model/User.php';
include_once 'model/UserManager.php';
include_once 'model/api/LolApi.php';
include_once 'model/Duo.php';
include_once 'model/DuoManager.php';
$_SESSION['errorContext'] = "New duo queue";
LolApi::init($db);

if ($_SESSION['loggedUserObject']) {
    $user = unserialize($_SESSION['loggedUserObject']);
    $duoSelect = displayDuoLane($db);
    $matches = displaymatches($db);
    $pageName = "Stats";
    include_once 'view/Header.php';
    include_once 'view/vStats.php';
    include_once 'view/Footer.php';
} else {
    header('Location: /DuoQ/index.php?l=login');
}

function displayDuoLane($db) {
    $user = unserialize($_SESSION['loggedUserObject']);
    $duoManager = new DuoManager($db);
    $duoArray = $duoManager->getDuoByUser($user);
    $html = '<select name="duoLane" id="duoLane" class="selectpicker">';
    for ($i = 0; $i < count($duoArray); $i++) {
        $sum1 = LolApi::getSummonerById($duoArray[$i]['playerOneDuo']);
        $sum2 = LolApi::getSummonerById($duoArray[$i]['playerTwoDuo']);
        $html = $html . '<option value="' . $duoArray[$i]['idDuo'] . '">' . $sum1['name'] . " & " . $sum2['name'] . '</option>';
    }
    return $html . '</select>';
}

function displaymatches($db) {
    if (isset($_POST['submitDuo'])) {
        $user = unserialize($_SESSION['loggedUserObject']);
        $duoManager = new DuoManager($db);
        $idDuo = $_POST['duoLane'];
        $duo = $duoManager->getDuoById($idDuo);
        $sum1Id = $duo['playerOneDuo'];
        $sum2Id = $duo['playerTwoDuo'];
        $player1 = LolApi::getSummonerById($sum1Id);
        $player2 = LolApi::getSummonerById($sum2Id);
        //$ranked = LolApi::getDuoRankedGames($player1, $player2);
        $matchesArray = $duoManager->getMatchesByDuo("19447356", "19440032");
        print_r($matchesArray);
        if (empty($matchesArray)) {
            $matchesArray = "There isn't yet any match for the selected duo queue";
        }
        return $matchesArray;
    }
}
