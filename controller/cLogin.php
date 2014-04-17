<?php

checkLogin();
checkRegister();



if ($_SESSION) {
    
} else {
    include_once 'view/Header.php';
    include_once 'view/vLogin.php';
    include_once 'view/Footer.php';
}

function checkRegister() {
    if (isset($_POST['submitRegister'])) {
        $userName = $_POST['newUserName'];
        $newMail = $_POST['newMail'];
        $newPass = $_POST['newPassword'];
        $newPassConf = $_POST['newPasswordConf'];
    }
}

function checkLogin() {
    if (isset($_POST['submitLogin'])) {
        $userEmai = $_POST['mail'];
        $userPassword = $_POST['password'];
        
        
        
    }
}
