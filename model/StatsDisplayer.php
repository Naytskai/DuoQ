<?php

include_once 'model/User.php';
include_once 'model/UserManager.php';
include_once 'model/api/LolApi.php';
include_once 'model/Duo.php';
include_once 'model/DuoManager.php.php';

class StatsDisplayer {

    private $db;

    //Constructor
    //-----------
    public function __construct($db) {
        $this->db = $db;
    }

    /*
     * This function generate each stat's table line
     */

    public function generateLine($summoners, $player1, $player2, $playerNumT1, $duoManager, $indexPlayer, $indexMatches, $matchesArray, $resultArray) {
        $duoManager = new DuoManager($this->db);
        $line = "<tr>";
        if ($summoners['nameSummoner'] == $player1['nameSummoner'] || $summoners['nameSummoner'] == $player2['nameSummoner']) {
            $line = $line . "<tr class=\"yourPlayer\">";
        }
        $champName = $duoManager->getChampionFromDb($resultArray[$indexPlayer]['fkChampion']);
        $champName = $champName['key'];
        $version = $matchesArray[$indexMatches]['versionMatch'];
        $champUnicId = $resultArray[$indexPlayer]['fkChampion'] . rand(0, count($resultArray) * 10);
        $champImgName = "http://ddragon.leagueoflegends.com/cdn/$version/img/champion/" . $champName . ".png";
        $line = $line . "<td>" . $playerNumT1 . "</td>";
        $line = $line . "<td class=\"trLeft\">" . $summoners['nameSummoner'] . "</td>";
        $line = $line . "<td class='rank'>" . $resultArray[$indexPlayer]['nameTier'] . " " . $duoManager->romanNumerals($resultArray[$indexPlayer]['divisionSummoner']) . "</td>";
        $line = $line . "<td><img id=\"" . $champUnicId . "\" src=\"" . $champImgName . "\" alt=\"Smiley face\" height=\"30\" width=\"30\" onmouseover=\"$('#$champUnicId').tooltip('show');\" data-toggle=\"tooltip\" title=\"" . $champName . "\" class=\"img-circle\"></td>";
        $line = $line . "<td class=\"killDeathAssist\">" . $resultArray[$indexPlayer]['champKill'] . "</td>";
        $line = $line . "<td class=\"killDeathAssist\">" . $resultArray[$indexPlayer]['champDeath'] . "</td>";
        $line = $line . "<td class=\"killDeathAssist\">" . $resultArray[$indexPlayer]['champAssist'] . "</td>";
        $line = $line . "<td class=\"creeps\">" . $resultArray[$indexPlayer]['champCS'] . "</td>";
        $line = $line . "<td class=\"gold\">" . round($resultArray[$indexPlayer]['champGold'] / 1000, 1) . " k </td>";
        if ($resultArray[$indexPlayer]['fkLane'] != "") {
            $line = $line . '<td><button type="button" style="width:70%;" onclick="setLane(\'' . $summoners['nameSummoner'] . '\',\'' . $resultArray[$indexPlayer]['pkResult'] . '\',this)" class="btn btn-default btn-xs">' . $duoManager->getLaneName($resultArray[$indexPlayer]['fkLane']) . '</button></td>';
        } else {
            $line = $line . '<td><button type="button" style="width:70%;" onclick="setLane(\'' . $summoners['nameSummoner'] . '\',\'' . $resultArray[$indexPlayer]['pkResult'] . '\',this)" class="btn btn-info btn-xs">set lane</button></td>';
        }
        $line = $line . "</tr>";
        return $line;
    }

    /*
     * This function generate the team's table head
     */

    public function generateTableHead() {
        $tableHead = "<table class = \"table table-condensed\">"
                . "<tr>"
                . "<th>#</th>"
                . "<th class=\"trLeft sumName\">Summoner's name</th>"
                . "<th class=\"rank\">Rank</th>"
                . "<th>Champion</th>"
                . "<th class=\"killDeathAssist\">Kill</th>"
                . "<th class=\"killDeathAssist\">Death</th>"
                . "<th class=\"killDeathAssist\">Assist</th>"
                . "<th class=\"creeps\">Creeps</th>"
                . "<th class=\"gold\">Gold</th>"
                . "<th>Lane</th>"
                . "</tr>";
        return $tableHead;
    }

    /*
     * This function return the total gaming time by duo 
     */

    public function getTotalGamingTime($db) {
        $totalGamingTime = 0;
        $user = unserialize($_SESSION['loggedUserObject']);
        $duoManager = new DuoManager($db);
        if (isset($_GET['duoId'])) {
            $idDuo = $_GET['duoId'];
        } else {
            $idDuo = $_POST['duoLane'];
        }
        $duo = $duoManager->getDuoById($idDuo);
        $matchesArray = $duoManager->getMatchesByDuo($duo['pkDuo']);
        for ($indexMatches = 0; $indexMatches < count($matchesArray); $indexMatches++) {
            $totalGamingTime += $matchesArray[$indexMatches]['lengthMatch'] / 60;
        }
        return sectoHms($totalGamingTime);
    }

    /*
     * This function return the total win number by duo
     */

    public function getTotalWins($db) {
        $totalWin = 0;
        $user = unserialize($_SESSION['loggedUserObject']);
        $duoManager = new DuoManager($db);
        if (isset($_GET['duoId'])) {
            $idDuo = $_GET['duoId'];
        } else {
            $idDuo = $_POST['duoLane'];
        }
        $duo = $duoManager->getDuoById($idDuo);
        $sum1Id = $duo['playerOneDuo'];
        $sum2Id = $duo['playerTwoDuo'];
        $player1 = $duoManager->getSummonerFromDb($sum1Id);
        $player2 = $duoManager->getSummonerFromDb($sum2Id);
        $matchesArray = $duoManager->getMatchesByDuo($duo['pkDuo']);
        for ($indexMatches = 0; $indexMatches < count($matchesArray); $indexMatches++) {
            if ($matchesArray[$indexMatches]['resultMatch'] == 1) {
                $totalWin ++;
            }
        }
        return $totalWin;
    }

    /*
     * This function return the number of defeat by duo
     */

    public function getTotalDefeat($db) {
        $totalDef = 0;
        $user = unserialize($_SESSION['loggedUserObject']);
        $duoManager = new DuoManager($db);
        if (isset($_GET['duoId'])) {
            $idDuo = $_GET['duoId'];
        } else {
            $idDuo = $_POST['duoLane'];
        }
        $duo = $duoManager->getDuoById($idDuo);
        $matchesArray = $duoManager->getMatchesByDuo($duo['pkDuo']);
        for ($indexMatches = 0; $indexMatches < count($matchesArray); $indexMatches++) {
            if ($matchesArray[$indexMatches]['resultMatch'] != 1) {
                $totalDef ++;
            }
        }
        return $totalDef;
    }

    /*
     * This function return the total average damage by duo's user
     */

    public function getTotalDomDealt($db) {
        $totalDom = 0;
        $user = unserialize($_SESSION['loggedUserObject']);
        $duoManager = new DuoManager($db);
        if (isset($_GET['duoId'])) {
            $idDuo = $_GET['duoId'];
        } else {
            $idDuo = $_POST['duoLane'];
        }
        $duo = $duoManager->getDuoById($idDuo);
        $sum1Id = $duo['playerOneDuo'];
        $sum2Id = $duo['playerTwoDuo'];
        $player1 = $duoManager->getSummonerFromDb($sum1Id);
        $player2 = $duoManager->getSummonerFromDb($sum2Id);
        $matchesArray = $duoManager->getMatchesByDuo($duo['pkDuo']);
        for ($indexMatches = 0; $indexMatches < count($matchesArray); $indexMatches++) {
            $resultArray = $duoManager->getResultByMatch($matchesArray[$indexMatches]['pkMatch']);
            for ($indexPlayer = 0; $indexPlayer < count($resultArray); $indexPlayer++) {
                $summoners = $duoManager->getSummonerFromDb($resultArray[$indexPlayer]['fkSummoner']);
                if ($summoners['nameSummoner'] == $player1['nameSummoner'] || $summoners['nameSummoner'] == $player2['nameSummoner']) {
                    $totalDom += $resultArray[$indexPlayer]['champDamage'];
                }
            }
        }
        $totalDom = $totalDom / (2 * count($matchesArray));
        return round($totalDom);
    }

    /*
     * This function return the total average gold by duo's user
     */

    public function getTotalGold($db) {
        $totalGold = 0;
        $duoManager = new DuoManager($db);
        if (isset($_GET['duoId'])) {
            $idDuo = $_GET['duoId'];
        } else {
            $idDuo = $_POST['duoLane'];
        }
        $duo = $duoManager->getDuoById($idDuo);
        $sum1Id = $duo['playerOneDuo'];
        $sum2Id = $duo['playerTwoDuo'];
        $player1 = $duoManager->getSummonerFromDb($sum1Id);
        $player2 = $duoManager->getSummonerFromDb($sum2Id);
        $matchesArray = $duoManager->getMatchesByDuo($duo['pkDuo']);
        for ($indexMatches = 0; $indexMatches < count($matchesArray); $indexMatches++) {
            $resultArray = $duoManager->getResultByMatch($matchesArray[$indexMatches]['pkMatch']);
            for ($indexPlayer = 0; $indexPlayer < count($resultArray); $indexPlayer++) {
                $summoners = $duoManager->getSummonerFromDb($resultArray[$indexPlayer]['fkSummoner']);
                if ($summoners['nameSummoner'] == $player1['nameSummoner'] || $summoners['nameSummoner'] == $player2['nameSummoner']) {
                    $totalGold += $resultArray[$indexPlayer]['champGold'];
                }
            }
        }
        $totalGold = $totalGold / (2 * count($matchesArray));
        return round($totalGold);
    }

//    public function clean($string, $version) {
//        $urlLolCDN = "http://ddragon.leagueoflegends.com/cdn/$version/img/champion/";
//        $startString = $string;
//        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
//        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
//        $string = strtolower($string);
//        $string = ucfirst($string);
//        $string = preg_replace('/-+/', '-', $string);
//        if ($string == "Wukong") {
//            $string = "MonkeyKing";
//            return $string;
//        }
//        if ($this->testURL($urlLolCDN . $string . ".png")) {
//            return $string;
//        } else {
//            $startString = ucwords($startString);
//            $startString = str_replace(' ', '', $startString); // Replaces all spaces with hyphens.
//            $startString = preg_replace('/[^A-Za-z0-9\-]/', '', $startString); // Removes special chars.
//            $startString = ucfirst($startString);
//            return $startString;
//        }
//    }
//    /*
//     * This function test if the url path given is available
//     */
//
//    public function testURL($url) {
//        $result = true;
//        $handle = curl_init($url);
//        curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
//
//        /* Get the HTML or whatever is linked in $url. */
//        $response = curl_exec($handle);
//
//        /* Check for 404 (file not found). */
//        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
//        if ($httpCode == 404) {
//            $result = false;
//        }
//
//        curl_close($handle);
//        return $result;
//    }
}

function sectoHms($sec, $padHours = false) {

    // start with a blank string
    $hms = "";

    // do the hours first: there are 3600 seconds in an hour, so if we divide
    // the total number of seconds by 3600 and throw away the remainder, we're
    // left with the number of hours in those seconds
    $hours = intval(intval($sec) / 3600);

    // add hours to $hms (with a leading 0 if asked for)
//    $hms .= ($padHours) ? str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" : $hours . ":";
    // dividing the total seconds by 60 will give us the number of minutes
    // in total, but we're interested in *minutes past the hour* and to get
    // this, we have to divide by 60 again and then use the remainder
    $minutes = intval(($sec / 60) % 60);

    // add minutes to $hms (with a leading 0 if needed)
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":";

    // seconds past the minute are found by dividing the total number of seconds
    // by 60 and using the remainder
    $seconds = intval($sec % 60);

    // add seconds to $hms (with a leading 0 if needed)
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    // done!
    return $hms;
}
