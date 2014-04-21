<?php

//------------------------------------------------------------------------------
//                         Includes                              
//------------------------------------------------------------------------------
include_once 'model/User.php';
include_once 'model/UserManager.php';
//------------------------------------------------------------------------------
//                         Check if the user is using the parameter form                              
//------------------------------------------------------------------------------
checkUpdateButton($db);
//------------------------------------------------------------------------------
//                         Check if the user is allready loged                              
//------------------------------------------------------------------------------
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


/*
 * This function manager to update the DB current User with new data
 * and set the session variable with the updated user
 */
function checkUpdateButton($db) {
    if ($_SESSION['loggedUserObject']) {
        if (isset($_POST['submitUpdate'])) {
            $newUserName = $_POST['newUserName'];
            $newMail = $_POST['newMail'];
            $newPassword = $_POST['newPassword'];
            $newPasswordConf = $_POST['newPasswordConf'];
            $user = new User(array());
            $user = unserialize($_SESSION['loggedUserObject']);
            $user->setName($newUserName);
            $user->setMail($newMail);
            $user->setPassword(sha1($newPassword));
            $userManager = new UserManager($db);
            $userManager->updateUserInfo($user);
            $_SESSION['loggedUserObject'] = serialize($user);
        }
    }
}
