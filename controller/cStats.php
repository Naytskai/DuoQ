<?php

include_once 'model/User.php';
include_once 'model/UserManager.php';
include_once 'model/api/LolApi.php';
include_once 'model/Duo.php';
include_once 'model/DuoManager.php';
include_once 'model/StatsDisplayer.php';
$_SESSION['errorContext'] = "Stats";
$statsDisplay = new StatsDisplayer();
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
    $matches = displayMatches($db, $statsDisplay);
    $totalGameTime = $statsDisplay->getTotalGamingTime($db);
    $totalWins = $statsDisplay->getTotalWins($db);
    $totalDefeat = $statsDisplay->getTotalDefeat($db);
    $totalDomDealt = $statsDisplay->getTotalDomDealt($db);
    $totalGold = $statsDisplay->getTotalGold($db);
    $duoSelect = displayDuoLane($db);
    $shareURL = "http://cypressxt.net/DuoQ/index.php?l=sharing&duoId=" . $idDuo;
    //--------------------------------------------------------------------------
    $pageName = "Stats";
    include_once 'view/Header.php';
    include_once 'view/vStats.php';
    include_once 'view/Footer.php';
} else {
    $_SESSION['askedPage'] = "stats";
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

function displayMatches($db, StatsDisplayer $statsDisplay) {
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
                $label = '<div class="row"><div class="col-md-6">';
                if ($matchesArray[$indexMatches]['resultMatch'] == 1) {
                    $label = $label . ' <span class="label label-success">Win ' . $gameDate . '</span>';
                } else {
                    $label = $label . ' <span class="label label-danger">Defeat ' . $gameDate . '</span>';
                }
                $label = $label . ' <span class="label label-default">' . round($matchesArray[$indexMatches]['lengthMatch'] / 60) . ' mins</span> <span class="label label-default"> Patch ' . $matchesArray[$indexMatches]['versionMatch'] . '</span>';
                $label = $label . '</div><div class="shareLabelDiv col-md-6"><span class="label label-default" id="shareGameLabel" onmouseover="$(this).tooltip(\'show\');" data-toggle="tooltip" title="Share this link with your friends">http://cypressxt.net/DuoQ/index.php?l=sharing&matchId=' . $matchesArray[$indexMatches]['pkMatch'] . '</span></div></div>';
                $html = $html . "<div class=\"jumbotron\"><h2>Game " . (count($matchesArray) - $indexMatches) . "</h2>$label<h3 class=\"blueTeam\">Blue team</h3>" . $statsDisplay->generateTableHead();
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
                        $playGrid1 = $playGrid1 . $statsDisplay->generateLine($summoners, $player1, $player2, $playerNumT1, $duoManager, $indexPlayer, $indexMatches, $matchesArray, $resultArray);
                    } else {
                        $playerNumT2 ++;
                        $playGrid2 = $playGrid2 . $statsDisplay->generateLine($summoners, $player1, $player2, $playerNumT1, $duoManager, $indexPlayer, $indexMatches, $matchesArray, $resultArray);
                    }
                    $seperator = "</table><h3 class=\"purpleTeam\">Purple team</h3>" . $statsDisplay->generateTableHead();
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
