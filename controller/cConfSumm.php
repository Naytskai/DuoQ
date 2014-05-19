<?php

//------------------------------------------------------------------------------
//                         Includes & variables                            
//------------------------------------------------------------------------------
include_once 'model/User.php';
include_once 'model/UserManager.php';
include_once 'model/api/LolApi.php';
$_SESSION['errorContext'] = "Confirm the chat secret";



if (isset($_SESSION['loggedUserObjectDuoQ'])) {
    $pageName = "Confirm Account";
    checkConfSummForm($db);
    include_once 'view/Header.php';
    include_once 'view/vValidateSumm.php';
    checkErrors();
    include_once 'view/Footer.php';
} else {
    header('Location: /DuoQ/index.php?l=login');
}

function checkConfSummForm($db) {
    if (isset($_POST['submitSummConf'])) {
        $secretField = $_POST['sumSecret'];

        // check if the secret field is not empty
        if ($secretField != "") {
            
        } else {
            $_SESSION['errorForm'] = $_SESSION['errorForm'] . "<br>Invalid summoner's chat secret code";
            return false;
        }
        if ($_SESSION['chatSecret'] == $secretField) {
            $user = unserialize($_SESSION['loggedUserObjectDuoQ']);
            $sumId = $_SESSION['sumId'];
            $userManager = new UserManager($db);
            $userManager->linkSummonerUser($user, $sumId);
            $_SESSION['chatSecret'] = "";
        }
    }
    
    if($_SESSION['chatSecret'] == ""){
        header('Location: /DuoQ/index.php?l=mySum');
    }
}

function displaySummonersByAccount($db) {
    $userManager = new UserManager($db);
    $user = unserialize($_SESSION['loggedUserObjectDuoQ']);
    $sumArray = $userManager->getSummonerByUser($user);
    $html = '<table class="table table-condensed"><tr><th>Summoner\'s name</th><th>Summoner\'s id</th></tr>';
    for ($i = 0; $i < count($sumArray); $i++) {
        $html = $html . '<tr>' . '<td>' . $sumArray[$i]['nameSummoner'] . '</td>' . '<td>' . $sumArray[$i]['idSummoner'] . '</td>' . '</tr>';
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
