<?php

//------------------------------------------------------------------------------
//                         Includes & variables                            
//------------------------------------------------------------------------------
include_once 'model/User.php';
include_once 'model/UserManager.php';
include_once 'model/Duo.php';
include_once 'model/DuoManager.php';
include_once 'model/api/LolApi.php';
$_SESSION['errorContext'] = "My duo";



if (isset($_SESSION['loggedUserObjectDuoQ'])) {
    $pageName = "My Duo";
    include_once 'view/Header.php';
    $duoTable = displayDuoByAccount($db);
    checkErrors();
    include_once 'view/vYourDuo.php';
    include_once 'view/Footer.php';
} else {
    $_SESSION['askedPage'] = "duo";
    header('Location: /DuoQ/index.php?l=login');
}


/*
 * This function format database info to html table
 */

function displayDuoByAccount($db) {
    $duoManager = new DuoManager($db);
    $user = unserialize($_SESSION['loggedUserObjectDuoQ']);
    $duoArray = $duoManager->getDuoByUser($user);
    $html = '<table class="table table-condensed"><tr><th>Summoner\'s name #1</th><th></th><th>Summoner\'s name #2</th><th>Action</th></tr>';
    for ($i = 0; $i < count($duoArray); $i++) {
        $button = '<td><button type="button" style="width:70%;" onclick="requestAjaxRemoveDuo(' . $duoArray[$i]['pkDuo'] . ', this)" class="btn btn-danger btn-xs">remove</button></td>';
        $sum1 = $duoManager->getSummonerFromDb($duoArray[$i]['playerOneDuo']);
        $sum2 = $duoManager->getSummonerFromDb($duoArray[$i]['playerTwoDuo']);
        $html = $html . '<tr id="' . $duoArray[$i]['pkDuo'] . '">' . '<td>' . $sum1['nameSummoner'] . '</td><td> & </td>' . '<td>' . $sum2['nameSummoner'] . '</td>' . $button . '</tr>';
    }
    $html = $html . '</table>';
    return $html;
}

/*
 * This function check if errors append and display them
 */

function checkErrors() {
    if ($_SESSION['errorForm'] != "") {
        include_once 'view/Modal.php';
    }
}
