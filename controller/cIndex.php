<?php

/**
 * Index Controller
 */
//------------------------------------------------------------------------------
//                         Controller's Attributes                              
//------------------------------------------------------------------------------

$pageName = "";


/**
 *  Check if the user is loged
 */
if (isset($_SESSION['loggedUserObjectDuoQ'])) {
    /**
     * If YES, you will see you'r last games statistics. With your recent games
     * your best games stats...
     */
    $pageName = "DuoQ";
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/Header.php';
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/vIndexLogged.php';
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/Footer.php';
} else {
    /**
     *  If NO, you will see a short descriptions of DuoQ fonctionnalites.
     */
    $pageName = "DuoQ";
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/Header.php';
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/vIndexDefault.php';
    include_once getenv('APP_DUOQ_ROOT_PATH') . '/view/Footer.php';
}


//------------------------------------------------------------------------------
//                         Data's formating Methodes                            
//------------------------------------------------------------------------------

