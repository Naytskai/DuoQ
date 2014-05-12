<?php

//------------------------------------------------------------------------------
//                         Includes & variables                            
//------------------------------------------------------------------------------
include_once 'model/User.php';
include_once 'model/UserManager.php';
$_SESSION['errorContext'] = "User's settings";
//------------------------------------------------------------------------------
//                         Check if the user is using the parameter form                              
//------------------------------------------------------------------------------
checkUpdateButton($db);
//------------------------------------------------------------------------------
//                         Check if the user is already loged                              
//------------------------------------------------------------------------------
if ($_SESSION['loggedUserObjectDuoQ']) {
    $user = unserialize($_SESSION['loggedUserObjectDuoQ']);

    $username = $user->getName();
    $userMail = $user->getMail();

    $pageName = "Settings";
    include_once 'view/Header.php';
    include_once 'view/vSettings.php';
    checkErrors();
    include_once 'view/Footer.php';
} else {
    $_SESSION['askedPage'] = "settings";
    header('Location: /DuoQ/index.php?l=login');
}


/*
 * This function manager to update the DB current User with new data
 * and set the session variable with the updated user
 */

function checkUpdateButton($db) {
    if ($_SESSION['loggedUserObjectDuoQ']) {
        if (isset($_POST['submitUpdate'])) {
            $newUserName = $_POST['newUserName'];
            $newMail = $_POST['newMail'];
            $newPassword = $_POST['newPassword'];
            $newPasswordConf = $_POST['newPasswordConf'];
            $userManager = new UserManager($db);
            $user = new User(array());
            $user = unserialize($_SESSION['loggedUserObjectDuoQ']);

            // check if the username is valid and don't exist 
            if ($newUserName != "" && !$userManager->isUserNameTaken($newMail)) {
                
            } else {
                $_SESSION['errorForm'] = $_SESSION['errorForm'] . "</br>Invalid username";
                return false;
            }

            //check if the password is valid and is equals to the passwordCheck
            if ($newPassword != "" && $newPassword == $newPasswordConf) {
                
            } else {
                $_SESSION['errorForm'] = $_SESSION['errorForm'] . "</br>Invalid password or password confirmation";
                return false;
            }

            //check if the mail@ is valid and don't already exist
            if ($newMail != "" && !$userManager->isMailTakenByOther($user, $newMail)) {
                
            } else {
                $_SESSION['errorForm'] = $_SESSION['errorForm'] . "</br>Invalid @Mail or already taken";
                return false;
            }
            $user->setName($newUserName);
            $user->setMail($newMail);
            $user->setPassword(sha1($newPassword));
            $userManager->updateUserInfo($user);
            $_SESSION['loggedUserObjectDuoQ'] = serialize($user);
        }
    }
}

/*
 * This function check if errors append and display them
 */

function checkErrors() {
    if ($_SESSION['errorForm'] != "") {
        include_once 'view/Modal.php';
    }
}
