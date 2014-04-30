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
    $html = '<select name="duoLane" id="duoLane" class="selectpicker">';
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
        $sum1Id = $duo[0]['playerOneDuo'];
        $sum2Id = $duo[0]['playerTwoDuo'];
        $player1 = LolApi::getSummonerById($sum1Id);
        $player2 = LolApi::getSummonerById($sum2Id);
        $ranked = LolApi::getDuoRankedGames($player1, $player2);
        $matchesArray = $duoManager->getMatchesByDuo($sum1Id, $sum2Id);
        if (empty($matchesArray)) {
            $html = "<div class=\"alert alert-warning\">There isn't yet any match for the selected duo queue</div>";
        } else {
            // eatch match
            for ($indexMatches = 0; $indexMatches < count($matchesArray); $indexMatches++) {
                $epoch = $matchesArray[$indexMatches]['dateMatch'];
                $timestamp = (int) substr($epoch, 0, -3);
                $gameDate = date('d F Y h:i:s', $timestamp);
                if ($matchesArray[$indexMatches]['resultMatch'] == 1) {
                    $label = '<span class="label label-success">Win on ' . $gameDate . '</span>';
                } else {
                    $label = '<span class="label label-danger">Loss on ' . $gameDate . '</span>';
                }
                $html = $html . "<div class=\"jumbotron\"><h2>Game " . ($indexMatches + 1) . "</h2>$label<h3>Team 1</h3><table class=\"table table-hover\">"
                        . "<tr>"
                        . "<th>#</th>"
                        . "<th>Summoner's name</th>"
                        . "<th>Champion</th>"
                        . "<th>Kill</th>"
                        . "<th>Death</th>"
                        . "<th>Assist</th>"
                        . "<th>Creeps</th>"
                        . "<th>Gold</th>"
                        . "<th class=\"centeredText\">Game version</th>"
                        . "</tr>";
                $resultArray[] = $duoManager->getResultByMatch($matchesArray[$indexMatches]['idMatch']);
                $playerNumT1 = 0;
                $playerNumT2 = 0;
                for ($indexPlayer = 0; $indexPlayer < count($resultArray[0]); $indexPlayer++) {
                    $playGrid1;
                    $playGrid2;
                    $summoners = $duoManager->getSummonerFromDb($resultArray[0][$indexPlayer]['idSummoner']);
                    if ($resultArray[0][$indexPlayer]['playerTeam'] == 100) {
                        $playerNumT1 ++;
                        $playGrid1 = $playGrid1 . "<tr>";
                        $playGrid1 = $playGrid1 . "<td>" . $playerNumT1 . "</td>";
                        $playGrid1 = $playGrid1 . "<td>" . $summoners['nameSummoner'] . "</td>";
                        $playGrid1 = $playGrid1 . "<td>" . $resultArray[0][$indexPlayer]['champID'] . "</td>";
                        $playGrid1 = $playGrid1 . "<td>" . $resultArray[0][$indexPlayer]['champKill'] . "</td>";
                        $playGrid1 = $playGrid1 . "<td>" . $resultArray[0][$indexPlayer]['champDeath'] . "</td>";
                        $playGrid1 = $playGrid1 . "<td>" . $resultArray[0][$indexPlayer]['champAssist'] . "</td>";
                        $playGrid1 = $playGrid1 . "<td>" . $resultArray[0][$indexPlayer]['champCS'] . "</td>";
                        $playGrid1 = $playGrid1 . "<td>" . $resultArray[0][$indexPlayer]['champGold'] / 1000 . "k </td>";
                        $playGrid1 = $playGrid1 . "<td class=\"centeredText\">" . $matchesArray[$indexMatches]['versionMatch'] . "</td>";
                        $playGrid1 = $playGrid1 . "</tr>";
                    } else {
                        $playerNumT2 ++;
                        $playGrid2 = $playGrid2 . "<tr $team>";
                        $playGrid2 = $playGrid2 . "<td>" . $playerNumT2 . "</td>";
                        $playGrid2 = $playGrid2 . "<td>" . $summoners['nameSummoner'] . "</td>";
                        $playGrid2 = $playGrid2 . "<td>" . $resultArray[0][$indexPlayer]['champID'] . "</td>";
                        $playGrid2 = $playGrid2 . "<td>" . $resultArray[0][$indexPlayer]['champKill'] . "</td>";
                        $playGrid2 = $playGrid2 . "<td>" . $resultArray[0][$indexPlayer]['champDeath'] . "</td>";
                        $playGrid2 = $playGrid2 . "<td>" . $resultArray[0][$indexPlayer]['champAssist'] . "</td>";
                        $playGrid2 = $playGrid2 . "<td>" . $resultArray[0][$indexPlayer]['champCS'] . "</td>";
                        $playGrid2 = $playGrid2 . "<td>" . $resultArray[0][$indexPlayer]['champGold'] / 1000 . "k </td>";
                        $playGrid2 = $playGrid2 . "<td class=\"centeredText\">" . $matchesArray[$indexMatches]['versionMatch'] . "</td>";
                        $playGrid2 = $playGrid2 . "</tr>";
                    }
                    $seperator = "</table><h3>Team 2</h3><table class=\"table table-hover\">"
                            . "<tr>"
                            . "<th>#</th>"
                            . "<th>Summoner's name</th>"
                            . "<th>Champion</th>"
                            . "<th>Kill</th>"
                            . "<th>Death</th>"
                            . "<th>Assist</th>"
                            . "<th>Creeps</th>"
                            . "<th>Gold</th>"
                            . "<th class=\"centeredText\">Game version</th>"
                            . "</tr>";
                }
                $html = $html . $playGrid1 . $seperator . $playGrid2 . '</table></div>';
            }
        }
        return $html;
    }
}
