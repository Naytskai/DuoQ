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
     * If YES, you gonna see you'r last games statistics. With your recent games
     * your best games stats...
     */
    $pageName = "DuoQ";
    include_once 'view/Header.php';
    include_once 'view/vIndexLoged.php';
    include_once 'view/Footer.php';
} else {
    /**
     *  If NO, you gonna see a short descriptions of DuoQ fonctionnalites.
     */
    $pageName = "DuoQ";
    include_once 'view/Header.php';
    include_once 'view/vIndexDefault.php';
    include_once 'view/Footer.php';
}


//------------------------------------------------------------------------------
//                         Data's formating Methodes                            
//------------------------------------------------------------------------------

