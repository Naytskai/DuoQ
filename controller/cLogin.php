<?php

//------------------------------------------------------------------------------
//                         Includes & variable                              
//------------------------------------------------------------------------------
include_once getenv('APP_DUOQ_ROOT_PATH') . '/model/User.php';
include_once getenv('APP_DUOQ_ROOT_PATH') . '/model/UserManager.php';
$_SESSION['errorContext'] = "Login & Register";
//------------------------------------------------------------------------------
//                         Check if the user is using the login form                              
//------------------------------------------------------------------------------
checkLogin($db);
//------------------------------------------------------------------------------
//                         Check if the user is using the register form                              
//------------------------------------------------------------------------------
checkRegister($db);
//------------------------------------------------------------------------------
//                         Check if the user is allready loged                              
//------------------------------------------------------------------------------
if (isset($_SESSION['loggedUserObjectDuoQ'])) {
    $pageName = 'Logged';
    header("Location: /DuoQ/index.php");
} else {
    $pageName = "Login";
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/Header.php';
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/vLogin.php';
    checkErrors();
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/Footer.php';
}

//------------------------------------------------------------------------------
//                         Locals methodes                              
//------------------------------------------------------------------------------

/*
 * Check the register form and create the new user if all the informations
 * are correct
 */
function checkRegister($db) {
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
            $_SESSION['errorForm'] = $_SESSION['errorForm'] . "<br>Invalid username";
            return false;
        }

        //check if the password is valid and is equals to the passwordCheck
        if ($newPass != "" && $newPass == $newPassConf) {
            
        } else {
            $_SESSION['errorForm'] = $_SESSION['errorForm'] . "<br>Invalid password or password confirmation";
            return false;
        }

        //check if the mail@ is valid and don't exist
        if ($newMail != "" && !$userManager->isMailTaken($newMail)) {
            
        } else {
            $_SESSION['errorForm'] = $_SESSION['errorForm'] . "<br>Invalid @Mail"
                    . "or already taken";
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

function checkLogin($db) {
    if (isset($_POST['submitLogin'])) {
        $userEmai = $_POST['mail'];
        $userPassword = $_POST['password'];
        $userManager = new UserManager($db);
        $data = array('mail' => $userEmai,
            'password' => sha1($userPassword));
        $user = new User($data);
        $user = $userManager->getUserByLoginForm($user);
        if (!$userManager->isUserExist($user)) {
            $_SESSION['errorForm'] = $_SESSION['errorForm'] . "<br> Login informations invalid";
            return false;
        } else {
            $_SESSION['loggedUserObjectDuoQ'] = serialize($user);
            if (isset($_SESSION['askedPage'])) {
                header("Location: /DuoQ/index.php?l=" . $_SESSION['askedPage']);
            }
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
