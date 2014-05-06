<?php

include_once 'model/User.php';
include_once 'model/UserManager.php';
include_once 'model/api/LolApi.php';
include_once 'model/Duo.php';

class StatsDisplayer {
    /*
     * This function generate each stat's table line
     */

    public function generateLine($summoners, $player1, $player2, $playerNumT1, $duoManager, $indexPlayer, $indexMatches, $matchesArray, $resultArray) {
        $line = $line . "<tr>";
        if ($summoners['nameSummoner'] == $player1['nameSummoner'] || $summoners['nameSummoner'] == $player2['nameSummoner']) {
            $line = $line . "<tr class=\"yourPlayer\">";
        }
        $champName = $duoManager->getChampionFromDb($resultArray[$indexPlayer]['fkChampion']);
        $champImgName = "";
        $champUnicId = $resultArray[$indexPlayer]['fkChampion'] . rand(0, count($resultArray) * 10);
        $champImgName = $this->clean($champName);
        $line = $line . "<td>" . $playerNumT1 . "</td>";
        $line = $line . "<td class=\"trLeft\">" . $summoners['nameSummoner'] . "</td>";
        $line = $line . "<td class='rank'>" . $resultArray[$indexPlayer]['nameTier'] . " " . $duoManager->romanNumerals($resultArray[$indexPlayer]['divisionSummoner']) . "</td>";
        $line = $line . "<td><img id=\"" . $champUnicId . "\" src=\"http://ddragon.leagueoflegends.com/cdn/" . $matchesArray[$indexMatches]['versionMatch'] . "/img/champion/" . $champImgName . ".png\" alt=\"Smiley face\" height=\"30\" width=\"30\" onmouseover=\"$('#$champUnicId').tooltip('show');\" data-toggle=\"tooltip\" title=\"" . $champName . "\"></td>";
        $line = $line . "<td class=\"killDeathAssist\">" . $resultArray[$indexPlayer]['champKill'] . "</td>";
        $line = $line . "<td class=\"killDeathAssist\">" . $resultArray[$indexPlayer]['champDeath'] . "</td>";
        $line = $line . "<td class=\"killDeathAssist\">" . $resultArray[$indexPlayer]['champAssist'] . "</td>";
        $line = $line . "<td class=\"creeps\">" . $resultArray[$indexPlayer]['champCS'] . "</td>";
        $line = $line . "<td class=\"gold\">" . round($resultArray[$indexPlayer]['champGold'] / 1000, 1) . " k </td>";
        $line = $line . "<td>" . "</td>";
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
                . "<th class=\"trLeft\">Summoner's name</th>"
                . "<th class=\"rank\">Rank</th>"
                . "<th>Champion</th>"
                . "<th class=\"killDeathAssist\">Kill</th>"
                . "<th class=\"killDeathAssist\">Death</th>"
                . "<th class=\"killDeathAssist\">Assist</th>"
                . "<th class=\"creeps\">Creeps</th>"
                . "<th class=\"gold\">Gold</th>"
                . "<th></th>"
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
        $idDuo = $_POST['duoLane'];
        $duo = $duoManager->getDuoById($idDuo);
        $matchesArray = $duoManager->getMatchesByDuo($duo['pkDuo']);
        for ($indexMatches = 0; $indexMatches < count($matchesArray); $indexMatches++) {
            $totalGamingTime += $matchesArray[$indexMatches]['lengthMatch'] / 60;
        }
        return $totalGamingTime . " mins";
    }

    /*
     * This function return the total win number by duo
     */

    public function getTotalWins($db) {
        $totalWin = 0;
        $user = unserialize($_SESSION['loggedUserObject']);
        $duoManager = new DuoManager($db);
        $idDuo = $_POST['duoLane'];
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
        $idDuo = $_POST['duoLane'];
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
        $idDuo = $_POST['duoLane'];
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
        $idDuo = $_POST['duoLane'];
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

    public function clean($string) {
        if ($string != "Wukong" && $string != "Twisted Fate" && $string != "Lee Sin") {
            $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
            $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
            $string = strtolower($string);
            $string = ucfirst($string);
        } else {

            if ($string == "Wukong") {
                $string = "MonkeyKing";
            } else if ($string == "Twisted Fate") {
                $string = "TwistedFate";
            } else if ($string == "Lee Sin") {
                $string = "LeeSin";
            }
            $string = ucwords($string);
        }
        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

}
