<?php

    require_once 'sqlConnect.php';
    require_once 'LolApi.php';

?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <link href="style.css" rel="stylesheet" type="text/css">
        <title></title>
    </head>
    <body>
        
        <?php
        
            LolApi::init($db,$keys);
            
            echo "<pre>";
            
//            var_dump(LolApi::getSummonerById(19447356));
            
//            var_dump(LolApi::getSummonerIdByName("Naytskaï"));
            
//            var_dump(LolApi::getDuoRankedGames("Gardok4","Naytskaï"));

            echo LolApi::getDuoRankedGames("Ifrit4012","ratei");
            
//            var_dump(LolApi::getCurrentPatch());
            
//                var_dump(LolApi::getLeagueInfo(19447356));
                
//            var_dump(LolApi::getRecentRankedGameBySummonerIdAndMatch(19447356,1405318508));
            
//            LolApi::getRecentRankedGamesBySummonerId(19447356);
               
//            $champions = LolApi::getChampions();
//            
//            echo $champions[43];
            
            echo "</pre>";
            
//            foreach ($array as $games)
//            {
//                $t = 0;
//                echo "<table>";
//                
//                echo "<tr>";
//                    echo "<th>Summoner</th>";
//                    echo "<th>Champion</th>";
//                    echo "<th>level</th>";
//                    echo "<th>Kill</th>";
//                    echo "<th>Death</th>";
//                    echo "<th>Assist</th>";
//                    echo "<th>cs</th>";
//                    echo "<th>gold</th>";
//                    echo "<th>damage</th>";
//                echo "</tr>";
//                
//                foreach ($games as $game)
//                {
//                
//                    foreach ($game as $keys => $team)
//                    {
//
//                        foreach ($team as $player)
//                        {
//                            echo "<tr>";
//                 
//                            foreach ($player as $keys => $stats)
//                            {
//                                echo "<td>$stats</td>";
//                            }
//                            
//                            echo "</tr>"; 
//                            $t++;
//
//                        }
//                    }
//                }
//                
//                echo "</table>";
//            }
//   
//            echo "</pre>";
            
        ?>
        
        
    </body>
</html>
