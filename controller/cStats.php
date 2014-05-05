<?php

include_once 'model/User.php';
include_once 'model/UserManager.php';
include_once 'model/api/LolApi.php';
include_once 'model/Duo.php';
include_once 'model/DuoManager.php';
$_SESSION['errorContext'] = "Stats";
LolApi::init($db);

if ($_SESSION['loggedUserObject']) {
    $user = unserialize($_SESSION['loggedUserObject']);
    $duoManager = new DuoManager($db);
    $idDuo = $_POST['duoLane'];
    $duo = $duoManager->getDuoById($idDuo);
    $sum1Id = $duo['playerOneDuo'];
    $sum2Id = $duo['playerTwoDuo'];
    $player1 = $duoManager->getSummonerFromDb($sum1Id);
    $player2 = $duoManager->getSummonerFromDb($sum2Id);
    $headerTitle = $player1['nameSummoner'] . " & " . $player2['nameSummoner'];
    // init all the duo's stats value ------------------------------------------
    $matches = displayMatches($db);
    $totalGameTime = getTotalGamingTime($db);
    $totalWins = getTotalWins($db);
    $totalDefeat = getTotalDefeat($db);
    $totalDomDealt = getTotalDomDealt($db);
    $totalGold = getTotalGold($db);
    $duoSelect = displayDuoLane($db);
    //--------------------------------------------------------------------------
    $pageName = "Stats";
    include_once 'view/Header.php';
    include_once 'view/vStats.php';
    include_once 'view/Footer.php';
} else {
    header('Location: /DuoQ/index.php?l=login');
}


/*
 * This function fill the dropdown list with the user's duo lane
 */

function displayDuoLane($db) {
    $user = unserialize($_SESSION['loggedUserObject']);
    $duoManager = new DuoManager($db);
    $duoArray = $duoManager->getDuoByUser($user);
    $html = '<select name="duoLane" id="duoLane" class="selectpicker" data-style="btn-info">';
    for ($i = 0; $i < count($duoArray); $i++) {
        $sum1 = $duoManager->getSummonerFromDb($duoArray[$i]['playerOneDuo']);
        $sum2 = $duoManager->getSummonerFromDb($duoArray[$i]['playerTwoDuo']);
        $html = $html . '<option value="' . $duoArray[$i]['pkDuo'] . '">' . $sum1['nameSummoner'] . " & " . $sum2['nameSummoner'] . '</option>';
    }
    return $html . '</select>';
}

/*
 * This function grab and display each match information
 */

function displayMatches($db) {
    if (isset($_POST['submitDuo'])) {
        $user = unserialize($_SESSION['loggedUserObject']);
        $duoManager = new DuoManager($db);
        $idDuo = $_POST['duoLane'];
        $duo = $duoManager->getDuoById($idDuo);
        $sum1Id = $duo['playerOneDuo'];
        $sum2Id = $duo['playerTwoDuo'];
        $player1 = $duoManager->getSummonerFromDb($sum1Id);
        $player2 = $duoManager->getSummonerFromDb($sum2Id);
        $ranked = LolApi::getDuoRankedGames($player1['nameSummoner'], $player2['nameSummoner']);
        $matchesArray = $duoManager->getMatchesByDuo($duo['pkDuo']);
        if (empty($matchesArray)) {
            $html = "<div class=\"alert alert-warning\">There isn't yet any match for the selected duo queue</div>";
        } else {
            // each match
            for ($indexMatches = 0; $indexMatches < count($matchesArray); $indexMatches++) {
                $epoch = $matchesArray[$indexMatches]['dateMatch'];
                $timestamp = (int) substr($epoch, 0, -3);
                $gameDate = date('d F Y H:i:s', $timestamp);
                if ($matchesArray[$indexMatches]['resultMatch'] == 1) {
                    $label = '<span class="label label-success">Win ' . $gameDate . '</span>';
                } else {
                    $label = '<span class="label label-danger">Defeat ' . $gameDate . '</span>';
                }
                $label = $label . ' <span class="label label-default">' . round($matchesArray[$indexMatches]['lengthMatch'] / 60) . ' mins</span> <span class="label label-default"> Patch ' . $matchesArray[$indexMatches]['versionMatch'] . '</span>';
                $html = $html . "<div class=\"jumbotron\"><h2>Game " . (count($matchesArray) - $indexMatches) . "</h2>$label<h3 class=\"blueTeam\">Blue team</h3>" . generateTableHead();
                $resultArray = $duoManager->getResultByMatch($matchesArray[$indexMatches]['pkMatch']);
                $playerNumT1 = 0;
                $playerNumT2 = 0;
                //each player -------------------------------------------------
                for ($indexPlayer = 0; $indexPlayer < count($resultArray); $indexPlayer++) {
                    $playGrid1;
                    $playGrid2;
                    $summoners = $duoManager->getSummonerFromDb($resultArray[$indexPlayer]['fkSummoner']);
                    if ($resultArray[$indexPlayer]['playerTeam'] == 100) {
                        $playerNumT1 ++;
                        $playGrid1 = $playGrid1 . generateLine($summoners, $player1, $player2, $playerNumT1, $duoManager, $indexPlayer, $indexMatches, $matchesArray, $resultArray);
                    } else {
                        $playerNumT2 ++;
                        $playGrid2 = $playGrid2 . generateLine($summoners, $player1, $player2, $playerNumT1, $duoManager, $indexPlayer, $indexMatches, $matchesArray, $resultArray);
                    }
                    $seperator = "</table><h3 class=\"purpleTeam\">Purple team</h3>" . generateTableHead();
                }
                //--------------------------------------------------------------
                $html = $html . $playGrid1 . $seperator . $playGrid2 . '</table></div>';
                $playGrid1 = "";
                $playGrid2 = "";
            }
        }
        return $html;
    }
}

/*
 * This function generate each stat's table line
 */

function generateLine($summoners, $player1, $player2, $playerNumT1, $duoManager, $indexPlayer, $indexMatches, $matchesArray, $resultArray) {
    $line = $line . "<tr>";
    if ($summoners['nameSummoner'] == $player1['nameSummoner'] || $summoners['nameSummoner'] == $player2['nameSummoner']) {
        $line = $line . "<tr class=\"yourPlayer\">";
    }
    $champName = $duoManager->getChampionFromDb($resultArray[$indexPlayer]['fkChampion']);
    $champImgName = "";
    $champUnicId = $resultArray[$indexPlayer]['fkChampion'] . rand(0, count($resultArray) * 10);
    $champImgName = clean($champName);
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

function generateTableHead() {
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

function getTotalGamingTime($db) {
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

function getTotalWins($db) {
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

function getTotalDefeat($db) {
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

function getTotalDomDealt($db) {
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

function getTotalGold($db) {
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

function clean($string) {
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
