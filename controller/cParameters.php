<?php

include_once 'model/User.php';
if ($_SESSION['loggedUserObject']) {
    $user = unserialize($_SESSION['loggedUserObject']);
    
    $username = $user->getName();
    $userMail = $user->getMail();

    $pageName = "Parameters";
    include_once 'view/Header.php';
    include_once 'view/vParameters.php';
    include_once 'view/Footer.php';
} else {
    header('Location: /DuoQ/index.php?l=login');
}