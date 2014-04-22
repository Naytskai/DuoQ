<?php

//------------------------------------------------------------------------------
//                         Includes & variables                            
//------------------------------------------------------------------------------
include_once 'model/User.php';
include_once 'model/api/LolApi.php';
LolApi::init($db);


checkFormDuo();

if ($_SESSION['loggedUserObject']) {
    $user = unserialize($_SESSION['loggedUserObject']);
    $userName = $user->getName();
    $pageName = "New Duo";
    include_once 'view/Header.php';
    include_once 'view/vNewDuo.php';
    include_once 'view/Footer.php';
} else {
    header('Location: /DuoQ/index.php?l=login');
}

function checkFormDuo() {
    if (isset($_POST['submitDuo'])) {
        $mySumName = $_POST['sumName'];
        $matesSumName = $_POST['matesSumName'];
        $myLane = $_POST['lane'];
        $matesLane = $_POST['mateslane'];

        // $mySumId = LolApi::getSummonerIdByName($mySumName);
        // $matesSumId = LolApi::getSummonerIdByName($matesSumName);
    }
}
