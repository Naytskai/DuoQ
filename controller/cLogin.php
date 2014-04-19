<?php
//------------------------------------------------------------------------------
//                         Includes                              
//------------------------------------------------------------------------------
include_once 'model/User.php';
include_once 'model/UserManager.php';
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
//                         Locals methodes                              
//------------------------------------------------------------------------------

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
            $isFormCorrect = true;
        } else {
            return false;
        }

        //check if the password is valid and is equals to the passwordCheck
        if ($newPass != "" && $newPass == $newPassConf) {
            $isFormCorrect = true;
        } else {
            return false;
        }

        //check if the mail@ is valid and don't exist
        if ($newMail != "" && !$userManager->isMailTaken($newMail)) {
            $isFormCorrect = true;
        } else {
            return false;
        }

        $data = array('name' => $newUserName,
            'mail' => $newMail,
            'password' => sha1($newPass));
        $user = new User($data);
        $userManager->add($user);
    }
}

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
            return false;
        }else{
            $_SESSION['loggedUserObject'] = serialize($user);
        }
    }
}
