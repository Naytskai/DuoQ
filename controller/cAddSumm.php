<?php

//------------------------------------------------------------------------------
//                         Includes & variables                            
//------------------------------------------------------------------------------
include_once getenv('APP_DUOQ_ROOT_PATH') . '/model/User.php';
include_once getenv('APP_DUOQ_ROOT_PATH') . '/model/UserManager.php';
include_once getenv('APP_DUOQ_ROOT_PATH') . '/model/api/LolApi.php';
include_once getenv('APP_DUOQ_ROOT_PATH') . '/model/api/XmppLeague.php';
$_SESSION['errorContext'] = "Link game account";



if (isset($_SESSION['loggedUserObjectDuoQ'])) {
    LolApi::init($db);
    checkAddSummForm($db);
    $pageName = "Link Account";
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/Header.php';
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/vAddSumm.php';
    checkErrors();
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/Footer.php';
} else {
    $_SESSION['askedPage'] = "addSumm";
    header('Location: /DuoQ/index.php?l=login');
}

function checkAddSummForm($db) {
    if (isset($_POST['submitSumm'])) {
        include_once getenv('APP_DUOQ_ROOT_PATH') . '/model/api/XmppConnect.php';
        $sumName = $_POST['sumName'];

        // check if the Summoner's names exist
        if ($sumName != "" && LolApi::getSummonerIdByName($sumName) != "") {
            
        } else {
            $_SESSION['errorForm'] = $_SESSION['errorForm'] . "<br>Your summoner's name dosen't exist";
            return false;
        }

        $mySumId = LolApi::getSummonerIdByName($sumName);
        $_SESSION['chatSecret'] = sha1(rand());
        $_SESSION['sumId'] = $mySumId;

        if (xmppLeague::addFriend($mySumId, "here is your secret: " . $_SESSION['chatSecret'])) {
            $userManager = new UserManager($db);
            $userManager->addSummonner($sumName, $mySumId);
            header('Location: /DuoQ/index.php?l=confSum');
        }
    }
}

/*
 * This function check if errors append and display them
 */

function checkErrors() {
    if ($_SESSION['errorForm'] != "") {
        include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/Modal.php';
    }
}
