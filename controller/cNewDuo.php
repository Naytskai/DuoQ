<?php

//------------------------------------------------------------------------------
//                         Includes & variables                            
//------------------------------------------------------------------------------
include_once getenv('APP_DUOQ_ROOT_PATH') . '/model/User.php';
include_once getenv('APP_DUOQ_ROOT_PATH') . '/model/UserManager.php';
include_once getenv('APP_DUOQ_ROOT_PATH') . '/model/api/LolApi.php';
include_once getenv('APP_DUOQ_ROOT_PATH') . '/model/Duo.php';
include_once getenv('APP_DUOQ_ROOT_PATH') . '/model/DuoManager.php';
$_SESSION['errorContext'] = "New duo queue";
LolApi::init($db);


if (isset($_SESSION['loggedUserObjectDuoQ'])) {
    $pageName = "New Duo";
    $sumSelect = displaySummonerSelect($db);
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/Header.php';
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/vNewDuo.php';
    checkFormDuo($db);
    checkErrors();
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/Footer.php';
} else {
    $_SESSION['askedPage'] = "duo";
    header('Location: /DuoQ/index.php?l=login');
}

function checkFormDuo($db) {
    if (isset($_POST['submitDuo'])) {
        $duoManager = new DuoManager($db);
        $mySumName = $_POST['sumName'];
        $matesSumName = $_POST['matesSumName'];
        $mySumName = str_replace(' ', '', $mySumName);
        $matesSumName = str_replace(' ', '', $matesSumName);
        $myLane = "";
        $matesLane = "";

        // check if the 2 Summoner's names are given
        if ($mySumName != "" && $matesSumName != "") {
            
        } else {
            $_SESSION['errorForm'] = $_SESSION['errorForm'] . "<br>Invalid summoner's / mate name<br>";
            return false;
        }

        // check if the 2 Summoner's names exist
        if (LolApi::getSummonerIdByName($mySumName) != "" && LolApi::getSummonerIdByName($matesSumName) != "") {
            
        } else {
            $_SESSION['errorForm'] = $_SESSION['errorForm'] . "<br>Your or your mate summoner's name dosen't exist";
            return false;
        }

        $mySumId = LolApi::getSummonerIdByName($mySumName);
        $matesSumId = LolApi::getSummonerIdByName($matesSumName);

        $data = array('playerOneDuo' => $mySumId,
            'playerOneLaneId' => $myLane,
            'playerTwoDuo' => $matesSumId,
            'playerTwoLaneId' => $matesLane);
        $duo = new Duo($data);
        $user = unserialize($_SESSION['loggedUserObjectDuoQ']);
        $duoManager->addSummonner($matesSumName, $matesSumId);
        $duoId = $duoManager->add($duo);
        $duoManager->linkDuoAndUser($user, $duoId);
    }
}

/*
 * This function generate an html select with the user's summoners
 */

function displaySummonerSelect($db) {
    $userManager = new UserManager($db);
    $user = unserialize($_SESSION['loggedUserObjectDuoQ']);
    $SumArray = $userManager->getSummonerByUser($user);
    $html = '<select name="sumName" id="sumName" class="selectpicker">';
    for ($i = 0; $i < count($SumArray); $i++) {
        $html = $html . '<option value="' . $SumArray[$i]['nameSummoner'] . '">' . $SumArray[$i]['nameSummoner'] . '</option>';
    }
    return $html . '</select>';
}

/*
 * This function check if errors append and display them
 */

function checkErrors() {
    if ($_SESSION['errorForm'] != "") {
        include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/Modal.php';
    }
}
