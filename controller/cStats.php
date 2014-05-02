<?php

include_once 'model/User.php';
include_once 'model/UserManager.php';
include_once 'model/api/LolApi.php';
include_once 'model/Duo.php';
include_once 'model/DuoManager.php';
$_SESSION['errorContext'] = "New duo queue";
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
    $totalGameTime = getTotalGamingTime($db);
    $totalWins = getTotalWins($db);
    $totalDefeat = getTotalDefeat($db);
    $totalDomDealt = getTotalDomDealt($db);
    $totalGold = getTotalGold($db);
    $duoSelect = displayDuoLane($db);
    $matches = displayMatches($db);
    $pageName = "Stats";
    include_once 'view/Header.php';
    include_once 'view/vStats.php';
    include_once 'view/Footer.php';
} else {
    header('Location: /DuoQ/index.php?l=login');
}

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
            // eatch match
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
                $html = $html . "<div class=\"jumbotron\"><h2>Game " . (count($matchesArray) - $indexMatches) . "</h2>$label<h3 class=\"blueTeam\">Blue team</h3><table class=\"table table-condensed\">"
                        . "<tr>"
                        . "<th>#</th>"
                        . "<th class=\"trLeft\">Summoner's name</th>"
                        . "<th>Rank</th>"
                        . "<th>Champion</th>"
                        . "<th>Kill</th>"
                        . "<th>Death</th>"
                        . "<th>Assist</th>"
                        . "<th>Creeps</th>"
                        . "<th>Gold</th>"
                        . "<th></th>"
                        . "</tr>";
                $resultArray = $duoManager->getResultByMatch($matchesArray[$indexMatches]['pkMatch']);
                $playerNumT1 = 0;
                $playerNumT2 = 0;
                for ($indexPlayer = 0; $indexPlayer < count($resultArray); $indexPlayer++) {
                    $playGrid1;
                    $playGrid2;
                    $summoners = $duoManager->getSummonerFromDb($resultArray[$indexPlayer]['fkSummoner']);
                    if ($resultArray[$indexPlayer]['playerTeam'] == 100) {
                        $playerNumT1 ++;
                        if ($summoners['nameSummoner'] == $player1['nameSummoner'] || $summoners['nameSummoner'] == $player2['nameSummoner']) {
                            $playGrid1 = $playGrid1 . "<tr class=\"yourPlayer\">";
                        } else {
                            $playGrid1 = $playGrid1 . "<tr>";
                        }
                        $champName = $duoManager->getChampionFromDb($resultArray[$indexPlayer]['fkChampion']);
                        $champImgName;
                        $champUnicId = $resultArray[$indexPlayer]['fkChampion'] . rand(0, count($resultArray) * 10);
                        $champImgName = clean($champName);
                        $playGrid1 = $playGrid1 . "<td>" . $playerNumT1 . "</td>";
                        $playGrid1 = $playGrid1 . "<td class=\"trLeft\">" . $summoners['nameSummoner'] . "</td>";
                        $playGrid1 = $playGrid1 . "<td class='rank'>" . $resultArray[$indexPlayer]['nameTier'] . " " . $duoManager->romanNumerals($resultArray[$indexPlayer]['divisionSummoner']) . "</td>";
                        $playGrid1 = $playGrid1 . "<td><img id=\"" . $champUnicId . "\" src=\"http://ddragon.leagueoflegends.com/cdn/" . $matchesArray[$indexMatches]['versionMatch'] . "/img/champion/" . $champImgName . ".png\" alt=\"Smiley face\" height=\"30\" width=\"30\" onmouseover=\"$('#$champUnicId').tooltip('show');\" data-toggle=\"tooltip\" title=\"" . $champName . "\"></td>";
                        $playGrid1 = $playGrid1 . "<td>" . $resultArray[$indexPlayer]['champKill'] . "</td>";
                        $playGrid1 = $playGrid1 . "<td>" . $resultArray[$indexPlayer]['champDeath'] . "</td>";
                        $playGrid1 = $playGrid1 . "<td>" . $resultArray[$indexPlayer]['champAssist'] . "</td>";
                        $playGrid1 = $playGrid1 . "<td>" . $resultArray[$indexPlayer]['champCS'] . "</td>";
                        $playGrid1 = $playGrid1 . "<td>" . round($resultArray[$indexPlayer]['champGold'] / 1000, 1) . " k </td>";
                        $playGrid1 = $playGrid1 . "<td>" . "</td>";
                        $playGrid1 = $playGrid1 . "</tr>";
                    } else {
                        $playerNumT2 ++;
                        if ($summoners['nameSummoner'] == $player1['nameSummoner'] || $summoners['nameSummoner'] == $player2['nameSummoner']) {
                            $playGrid2 = $playGrid2 . "<tr class=\"yourPlayer\">";
                        } else {
                            $playGrid2 = $playGrid2 . "<tr>";
                        }
                        $champNameT2 = $duoManager->getChampionFromDb($resultArray[$indexPlayer]['fkChampion']);
                        $champImgNameT2;
                        $champUnicIdT2 = $resultArray[$indexPlayer]['fkChampion'] . rand(0, count($resultArray) * 10);
                        $champImgNameT2 = clean($champNameT2);
                        $playGrid2 = $playGrid2 . "<td>" . $playerNumT2 . "</td>";
                        $playGrid2 = $playGrid2 . "<td class=\"trLeft\">" . $summoners['nameSummoner'] . "</td>";
                        $playGrid2 = $playGrid2 . "<td class='rank'>" . $resultArray[$indexPlayer]['nameTier'] . " " . $duoManager->romanNumerals($resultArray[$indexPlayer]['divisionSummoner']) . "</td>";
                        $playGrid2 = $playGrid2 . "<td><img id=\"" . $champUnicIdT2 . "\" src=\"http://ddragon.leagueoflegends.com/cdn/" . $matchesArray[$indexMatches]['versionMatch'] . "/img/champion/" . $champImgNameT2 . ".png\" alt=\"Smiley face\" height=\"30\" width=\"30\" onmouseover=\"$('#$champUnicIdT2').tooltip('show');\" data-toggle=\"tooltip\" title=\"" . $champNameT2 . "\"></td>";
                        $playGrid2 = $playGrid2 . "<td>" . $resultArray[$indexPlayer]['champKill'] . "</td>";
                        $playGrid2 = $playGrid2 . "<td>" . $resultArray[$indexPlayer]['champDeath'] . "</td>";
                        $playGrid2 = $playGrid2 . "<td>" . $resultArray[$indexPlayer]['champAssist'] . "</td>";
                        $playGrid2 = $playGrid2 . "<td>" . $resultArray[$indexPlayer]['champCS'] . "</td>";
                        $playGrid2 = $playGrid2 . "<td>" . round($resultArray[$indexPlayer]['champGold'] / 1000, 1) . " k </td>";
                        $playGrid2 = $playGrid2 . "<td>" . "</td>";
                        $playGrid2 = $playGrid2 . "</tr>";
                    }
                    $seperator = "</table><h3 class=\"purpleTeam\">Purple team</h3><table class=\"table table-condensed\">"
                            . "<tr>"
                            . "<th>#</th>"
                            . "<th class=\"trLeft\">Summoner's name</th>"
                            . "<th>Rank</th>"
                            . "<th>Champion</th>"
                            . "<th>Kill</th>"
                            . "<th>Death</th>"
                            . "<th>Assist</th>"
                            . "<th>Creeps</th>"
                            . "<th>Gold</th>"
                            . "<th></th>"
                            . "</tr>";
                }
                $html = $html . $playGrid1 . $seperator . $playGrid2 . '</table></div>';
                $playGrid1 = "";
                $playGrid2 = "";
            }
        }
        return $html;
    }
}

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

function getTotalWins($db) {
    $totalWin = 0;
    $user = unserialize($_SESSION['loggedUserObject']);
    $duoManager = new DuoManager($db);
    $idDuo = $_POST['duoLane'];
    $duo = $duoManager->getDuoById($idDuo);
    $matchesArray = $duoManager->getMatchesByDuo($duo['pkDuo']);
    for ($indexMatches = 0; $indexMatches < count($matchesArray); $indexMatches++) {
        if ($matchesArray[$indexMatches]['resultMatch'] == 1) {
            $totalWin ++;
        }
    }
    return $totalWin;
}

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

function getTotalDomDealt($db) {
    $totalDom = 0;
    $user = unserialize($_SESSION['loggedUserObject']);
    $duoManager = new DuoManager($db);
    $idDuo = $_POST['duoLane'];
    $duo = $duoManager->getDuoById($idDuo);
    $matchesArray = $duoManager->getMatchesByDuo($duo['pkDuo']);
    for ($indexMatches = 0; $indexMatches < count($matchesArray); $indexMatches++) {
        $resultArray = $duoManager->getResultByMatch($matchesArray[$indexMatches]['pkMatch']);
        for ($indexPlayer = 0; $indexPlayer < count($resultArray); $indexPlayer++) {
            if ($summoners['nameSummoner'] == $player1['nameSummoner'] || $summoners['nameSummoner'] == $player2['nameSummoner']) {
                $totalDom = ($totalDom + $resultArray[$indexPlayer]['champDamage'] / count($resultArray));
            }
        }
    }
    return round($totalDom);
}

function getTotalGold($db) {
    $totalGold = 0;
    $user = unserialize($_SESSION['loggedUserObject']);
    $duoManager = new DuoManager($db);
    $idDuo = $_POST['duoLane'];
    $duo = $duoManager->getDuoById($idDuo);
    $matchesArray = $duoManager->getMatchesByDuo($duo['pkDuo']);
    for ($indexMatches = 0; $indexMatches < count($matchesArray); $indexMatches++) {
        $resultArray = $duoManager->getResultByMatch($matchesArray[$indexMatches]['pkMatch']);
        for ($indexPlayer = 0; $indexPlayer < count($resultArray); $indexPlayer++) {
            if ($summoners['nameSummoner'] == $player1['nameSummoner'] || $summoners['nameSummoner'] == $player2['nameSummoner']) {
                $totalGold = ($totalGold + $resultArray[$indexPlayer]['champGold'] / count($resultArray));
            }
        }
    }
    return round($totalGold);
}

function clean($string) {

    //

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
