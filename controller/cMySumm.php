<?php

//------------------------------------------------------------------------------
//                         Includes & variables                            
//------------------------------------------------------------------------------
include_once 'model/User.php';
include_once 'model/UserManager.php';
include_once 'model/api/LolApi.php';
$_SESSION['errorContext'] = "My game account";



if ($_SESSION['loggedUserObject']) {
    $pageName = "My game account";
    include_once 'view/Header.php';
    $accountTable = displaySummonersByAccount($db);
    checkErrors();
    include_once 'view/vSummAccount.php';
    include_once 'view/Footer.php';
} else {
    header('Location: /DuoQ/index.php?l=login');
}


/*
 * This function format database info to html table
 */

function displaySummonersByAccount($db) {
    $userManager = new UserManager($db);
    $user = unserialize($_SESSION['loggedUserObject']);
    $sumArray = $userManager->getSummonerByUser($user);
    $html = '<table class="table table-condensed"><tr><th>Summoner\'s name</th><th>Summoner\'s id</th></tr>';
    for ($i = 0; $i < count($sumArray); $i++) {
        $html = $html . '<tr>' . '<td>' . $sumArray[$i]['nameSummoner'] . '</td>' . '<td>' . $sumArray[$i]['pkSummoner'] . '</td>' . '</tr>';
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
