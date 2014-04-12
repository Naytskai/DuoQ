<?php


$l = isset($_GET['l']) ? $_GET['l'] : false;

switch ($l) {
    case false:
        include_once('controller/cIndex.php');
        break;
    case "index":
        include_once('controller/cIndex.php');
        break;
    case "download";
        include_once ('controller/cDownload.php');
        break;
    case "about";
        include_once ('controller/cAbout.php');
        break;
}