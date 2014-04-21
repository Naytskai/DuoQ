<?php

//------------------------------------------------------------------------------
//                         Includes & variable                              
//------------------------------------------------------------------------------
include_once 'model/User.php';
include_once 'model/UserManager.php';
$error;
$errorContext = "Login | Register";
//------------------------------------------------------------------------------
//                         Check if the user is using the login form                              
//------------------------------------------------------------------------------
checkLogin($db, $error);
//------------------------------------------------------------------------------
//                         Check if the user is using the register form                              
//------------------------------------------------------------------------------
checkRegister($db, $error);
//------------------------------------------------------------------------------
//                         Check if the user is allready loged                              
//------------------------------------------------------------------------------
if ($_SESSION['loggedUserObject']) {
    $pageName = 'Loged';
    include_once 'view/Header.php';
    include_once 'view/Footer.php';
} else {
    $pageName = "Login";
    include_once 'view/Header.php';
    include_once 'view/vLogin.php';
    include_once 'view/Footer.php';
}
//------------------------------------------------------------------------------
//                         Check and display form errors                              
//------------------------------------------------------------------------------
checkErrors($error);

//------------------------------------------------------------------------------
//                         Locals methodes                              
//------------------------------------------------------------------------------

/*
 * Check the register form and create the new user if all the informations
 * are correct
 */
function checkRegister($db, $error) {
    if (isset($_POST['submitRegister'])) {
        $newUserName = $_POST['newUserName'];
        $newMail = $_POST['newMail'];
        $newPass = $_POST['newPassword'];
        $newPassConf = $_POST['newPasswordConf'];
        $isFormCorrect = false;
        $userManager = new UserManager($db);

        // check if the username is valid and don't exist 
        if ($newUserName != "" && !$userManager->isUserNameTaken($newMail)) {
            
        } else {
            $error = $error."<br> Username invalid";
            return false;
        }

        //check if the password is valid and is equals to the passwordCheck
        if ($newPass != "" && $newPass == $newPassConf) {
            
        } else {
            $error = $error."<br> Passwords invalid";
            return false;
        }

        //check if the mail@ is valid and don't exist
        if ($newMail != "" && !$userManager->isMailTaken($newMail)) {
            
        } else {
            $error = $error."<br> @Mail invalid";
            return false;
        }

        $data = array('name' => $newUserName,
            'mail' => $newMail,
            'password' => sha1($newPass));
        $user = new User($data);
        $userManager->add($user);
    }
}

/*
 * Check the login form & if informations are correct, create a session's 
 * variable with the loged user
 */

function checkLogin($db, $error) {
    if (isset($_POST['submitLogin'])) {
        $userEmai = $_POST['mail'];
        $userPassword = $_POST['password'];
        $userManager = new UserManager($db);
        $data = array('mail' => $userEmai,
            'password' => sha1($userPassword));
        $user = new User($data);
        $user = $userManager->getUserByLoginForm($user);
        if (!$userManager->isUserExist($user)) {
            $error = $error."<br> Login informations invalid";
            return false;
        } else {
            $_SESSION['loggedUserObject'] = serialize($user);
        }
    }
}

function checkErrors($error){
    if($error!=""){
        include_once 'view/Modal.php';
    }
}
