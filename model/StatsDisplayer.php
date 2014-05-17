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

    function displayMatches($db, $idDuo) {
        $user = unserialize($_SESSION['loggedUserObjectDuoQ']);
        $duoManager = new DuoManager($db);
        $duo = $duoManager->getDuoById($idDuo);
        $duo = new Duo(array());
        $duo = $duoManager->getDuoById($idDuo);
        $sum1Id = $duo->getPlayerOneDuo();
        $sum2Id = $duo->getPlayerTwoDuo();
        $player1 = $duoManager->getSummonerFromDb($sum1Id);
        $player2 = $duoManager->getSummonerFromDb($sum2Id);
        $matchesArray = $duoManager->getMatchesByDuo($duo->getPkDuo());
        if (empty($matchesArray)) {
            $html = "<div class=\"alert alert-warning\">There isn't yet any match for the selected duo queue</div>";
        } else {
            // each match
            for ($indexMatches = 0; $indexMatches < count($matchesArray); $indexMatches++) {
                $epoch = $matchesArray[$indexMatches]['dateMatch'];
                $timestamp = (int) substr($epoch, 0, -3);
                $gameDate = date('d F Y H:i:s', $timestamp);
                $label = '<div class="container-fluid"><div class="row"><div class="col-md-6">';
                if ($matchesArray[$indexMatches]['resultMatch'] == 1) {
                    $label = $label . ' <span class="label label-success">Win ' . $gameDate . '</span>';
                } else {
                    $label = $label . ' <span class="label label-danger">Defeat ' . $gameDate . '</span>';
                }

                $resultArray = $duoManager->getResultByMatch($matchesArray[$indexMatches]['pkMatch']);
                $label = $label . ' <span class="label label-default">' . round($matchesArray[$indexMatches]['lengthMatch'] / 60) . ' mins</span> <span class="label label-default"> Patch ' . $matchesArray[$indexMatches]['versionMatch'] . '</span>';
                $label = $label . '</div><div class="shareLabelDiv col-md-6"><span class="label label-default" id="shareGameLabel" onmouseover="$(this).tooltip(\'show\');" data-toggle="tooltip" title="Share this link with your friends">http://cypressxt.net/DuoQ/index.php?l=sharing&matchId=' . $matchesArray[$indexMatches]['pkMatch'] . '</span></div></div></div></div><div style="display: none;" id="game' . $matchesArray[$indexMatches]['pkMatch'] . '" class="data">';

                $playerNumT1 = 0;
                $playerNumT2 = 0;
                //each player -------------------------------------------------
                $playerIndex = 0;
                $finalScore = "";
                for ($indexPlayer = 0; $indexPlayer < count($resultArray); $indexPlayer++) {
                    $playGrid1;
                    $playGrid2;
                    $summoners = $duoManager->getSummonerFromDb($resultArray[$indexPlayer]['fkSummoner']);
                    if ($resultArray[$indexPlayer]['playerTeam'] == 100) {
                        $playerNumT1 ++;
                        $playGrid1 = $playGrid1 . $this->generateLine($summoners, $player1, $player2, $playerNumT1, $duoManager, $indexPlayer, $indexMatches, $matchesArray, $resultArray);
                    } else {
                        $playerNumT2 ++;
                        $playGrid2 = $playGrid2 . $this->generateLine($summoners, $player1, $player2, $playerNumT2, $duoManager, $indexPlayer, $indexMatches, $matchesArray, $resultArray);
                    }

                    if (isset($_SESSION['loggedUserObjectDuoQ']) && $duoManager->isInTheDuo($user, $duo)) {
                        $yourSummonersIdArray = $duoManager->getSummonersByUser($user);
                        if (in_array($summoners['pkSummoner'], $yourSummonersIdArray[0])) {
                            $playerIndex = $indexPlayer;
                            $finalScore = $resultArray[$indexPlayer]['champKill'] . "/" . $resultArray[$indexPlayer]['champDeath'] . "/" . $resultArray[$indexPlayer]['champAssist'];
                        }
                    } else {
                        if ($summoners['nameSummoner'] == $player1['nameSummoner'] && $indexMatches % 2 == 0) {
                            $playerIndex = $indexPlayer;
                            $finalScore = $resultArray[$indexPlayer]['champKill'] . "/" . $resultArray[$indexPlayer]['champDeath'] . "/" . $resultArray[$indexPlayer]['champAssist'];
                        } elseif ($summoners['nameSummoner'] == $player2['nameSummoner'] && $indexMatches % 2 == 1) {
                            $playerIndex = $indexPlayer;
                            $finalScore = $resultArray[$indexPlayer]['champKill'] . "/" . $resultArray[$indexPlayer]['champDeath'] . "/" . $resultArray[$indexPlayer]['champAssist'];
                        }
                    }
                }
                //--------------------------------------------------------------
                $champName = $duoManager->getChampionFromDb($resultArray[$playerIndex]['fkChampion']);
                $champName = $champName['key'];
                $splashImg = "http://ddragon.leagueoflegends.com/cdn/img/champion/splash/" . $champName . "_0.jpg";
                $yourStats = '<div class="row"><div class="col-md-6"><h1>Game ' . (count($matchesArray) - $indexMatches) . ' </h1></div><div class="col-md-6"><div class="centeredText"><h2 class="stats"><small>Your performance </small>' . $finalScore . '</h2></div></div></div>';
                $html = $html . "<div class=\"gameJumbotron\"><div class=\"gameHeader\" onclick=\"expand(" . $matchesArray[$indexMatches]['pkMatch'] . ", this)\" style=\" background-image: url(" . $splashImg . ");\">$yourStats $label<h3 class=\"blueTeam\">Blue team</h3>" . $this->generateTableHead();
                $seperator = "</table><h3 class=\"purpleTeam\">Purple team</h3>" . $this->generateTableHead();
                $html = $html . $playGrid1 . $seperator . $playGrid2 . '</table></div></div>';
                $playGrid1 = "";
                $playGrid2 = "";
            }
        }
        return $html;
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
        $line = $line . "<td class=\"trLeft sumName\">" . $summoners['nameSummoner'] . "</td>";
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
                . "<th class=\"sumNumber\">#</th>"
                . "<th class=\"trLeft sumName\">Summoner</th>"
                . "<th class=\"rank\">Rank</th>"
                . "<th class=\"champIcon\">Champion</th>"
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

    public function getTotalGamingTime($db, $idDuo) {
        $totalGamingTime = 0;
        $user = unserialize($_SESSION['loggedUserObject']);
        $duoManager = new DuoManager($db);
        if (isset($_GET['duoId'])) {
            $idDuo = $_GET['duoId'];
        } elseif (isset($_POST['duoLane'])) {
            $idDuo = $_POST['duoLane'];
        }
        $duo = new Duo(array());
        $duo = $duoManager->getDuoById($idDuo);
        $matchesArray = $duoManager->getMatchesByDuo($duo->getPkDuo());
        for ($indexMatches = 0; $indexMatches < count($matchesArray); $indexMatches++) {
            $totalGamingTime += $matchesArray[$indexMatches]['lengthMatch'] / 60;
        }
        return $this->sectoHms($totalGamingTime);
    }

    /*
     * This function return the total win number by duo
     */

    public function getTotalWins($db, $idDuo) {
        $totalWin = 0;
        $user = unserialize($_SESSION['loggedUserObject']);
        $duoManager = new DuoManager($db);
        if (isset($_GET['duoId'])) {
            $idDuo = $_GET['duoId'];
        } elseif (isset($_POST['duoLane'])) {
            $idDuo = $_POST['duoLane'];
        }
        $duo = new Duo(array());
        $duo = $duoManager->getDuoById($idDuo);
        $sum1Id = $duo->getPlayerOneDuo();
        $sum2Id = $duo->getPlayerTwoDuo();
        $player1 = $duoManager->getSummonerFromDb($sum1Id);
        $player2 = $duoManager->getSummonerFromDb($sum2Id);
        $matchesArray = $duoManager->getMatchesByDuo($duo->getPkDuo());
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

    public function getTotalDefeat($db, $idDuo) {
        $totalDef = 0;
        $user = unserialize($_SESSION['loggedUserObject']);
        $duoManager = new DuoManager($db);
        if (isset($_GET['duoId'])) {
            $idDuo = $_GET['duoId'];
        } elseif (isset($_POST['duoLane'])) {
            $idDuo = $_POST['duoLane'];
        }
        $duo = new Duo(array());
        $duo = $duoManager->getDuoById($idDuo);
        $matchesArray = $duoManager->getMatchesByDuo($duo->getPkDuo());
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

    public function getTotalDomDealt($db, $idDuo) {
        $totalDom = 0;
        $user = unserialize($_SESSION['loggedUserObject']);
        $duoManager = new DuoManager($db);
        if (isset($_GET['duoId'])) {
            $idDuo = $_GET['duoId'];
        } elseif (isset($_POST['duoLane'])) {
            $idDuo = $_POST['duoLane'];
        }
        $duo = new Duo(array());
        $duo = $duoManager->getDuoById($idDuo);
        $sum1Id = $duo->getPlayerOneDuo();
        $sum2Id = $duo->getPlayerTwoDuo();
        $player1 = $duoManager->getSummonerFromDb($sum1Id);
        $player2 = $duoManager->getSummonerFromDb($sum2Id);
        $matchesArray = $duoManager->getMatchesByDuo($duo->getPkDuo());
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

    public function getTotalGold($db, $idDuo) {
        $totalGold = 0;
        $duoManager = new DuoManager($db);
        if (isset($_GET['duoId'])) {
            $idDuo = $_GET['duoId'];
        } elseif (isset($_POST['duoLane'])) {
            $idDuo = $_POST['duoLane'];
        }
        $duo = new Duo(array());
        $duo = $duoManager->getDuoById($idDuo);
        $sum1Id = $duo->getPlayerOneDuo();
        $sum2Id = $duo->getPlayerTwoDuo();
        $player1 = $duoManager->getSummonerFromDb($sum1Id);
        $player2 = $duoManager->getSummonerFromDb($sum2Id);
        $matchesArray = $duoManager->getMatchesByDuo($duo->getPkDuo());
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

    public function sectoHms($sec, $padHours = false) {

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

}
