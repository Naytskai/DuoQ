<?php

include_once 'model/User.php';
if ($_SESSION['loggedUserObject']) {
    $user = unserialize($_SESSION['loggedUserObject']);
    $pageName = "Stats";
    include_once 'view/Header.php';
    include_once 'view/vStats.php';
    include_once 'view/Footer.php';
} else {
    header('Location: /DuoQ/index.php?l=login');
}