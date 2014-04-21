<?php

//------------------------------------------------------------------------------
//                         Includes & variables                            
//------------------------------------------------------------------------------
include_once 'model/User.php';
include_once 'model/UserManager.php';
$errors;
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
            $userManager = new UserManager($db);
            $user = new User(array());
            $user = unserialize($_SESSION['loggedUserObject']);

            // check if the username is valid and don't exist 
            if ($newUserName != "" && !$userManager->isUserNameTaken($newMail)) {
                
            } else {
                $errors = $errors . "</br>usernameProb";
                return false;
            }

            //check if the password is valid and is equals to the passwordCheck
            if ($newPassword != "" && $newPassword == $newPasswordConf) {
                
            } else {
                $errors = $errors . "</br>passProb";
                return false;
            }

            //check if the mail@ is valid and don't allready exist
            if ($newMail != "" && !$userManager->isMailTakenByOther($user, $newMail)) {
                
            } else {
                $errors = $errors . "</br>mailProb";
                return false;
            }
            $user->setName($newUserName);
            $user->setMail($newMail);
            $user->setPassword(sha1($newPassword));
            $userManager->updateUserInfo($user);
            $_SESSION['loggedUserObject'] = serialize($user);
        }
    }
}

?>