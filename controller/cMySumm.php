<?php

//------------------------------------------------------------------------------
//                         Includes & variables                            
//------------------------------------------------------------------------------
include_once 'model/User.php';
include_once 'model/UserManager.php';
include_once 'model/api/LolApi.php';
$_SESSION['errorContext'] = "My game account";



if (isset($_SESSION['loggedUserObjectDuoQ'])) {
    $pageName = "My game account";
    include_once 'view/Header.php';
    $accountTable = displaySummonersByAccount($db);
    checkErrors();
    include_once 'view/vSummAccount.php';
    include_once 'view/Footer.php';
} else {
    $_SESSION['askedPage'] = "mySum";
    header('Location: /DuoQ/index.php?l=login');
}


/*
 * This function format database info to html table
 */

function displaySummonersByAccount($db) {
    $userManager = new UserManager($db);
    $user = unserialize($_SESSION['loggedUserObjectDuoQ']);
    $sumArray = $userManager->getSummonerByUser($user);
    $html = '<table class="table table-condensed"><tr><th>Summoner\'s name</th><th>Summoner\'s id</th><th>Action</th></tr>';
    for ($i = 0; $i < count($sumArray); $i++) {
        $button = '<td><button type="button" style="width:70%;" onclick="requestAjaxUnLinkSum(' . $sumArray[$i]['pkSummoner'] . ', this)" class="btn btn-danger btn-xs">remove</button></td>';
        $html = $html . '<tr id="' . $sumArray[$i]['pkSummoner'] . '">' . '<td>' . $sumArray[$i]['nameSummoner'] . '</td>' . '<td>' . $sumArray[$i]['pkSummoner'] . '</td>' . $button . '</tr>';
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
