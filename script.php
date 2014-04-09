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
            $urlGardok = 'http://prod.api.pvp.net/api/lol/euw/v1.4/summoner/by-name/Gardok4?api_key=af29f802-7266-4157-b1ea-3f35c4c84465';
                          
            $urlNaytskai = 'http://prod.api.pvp.net/api/lol/euw/v1.4/summoner/by-name/Naytskaï?api_key=af29f802-7266-4157-b1ea-3f35c4c84465';
            $ch = curl_init(); 
            $timeout = 5; 
             
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
            
            curl_setopt($ch, CURLOPT_URL, $urlGardok);
            $dataGardok = json_decode(curl_exec($ch),true);
            $responsecodeGardok = curl_getinfo($ch);
            
            curl_setopt($ch, CURLOPT_URL, $urlNaytskai);
            $dataNaytskai = json_decode(curl_exec($ch),true);
            $responsecodeNaytskai = curl_getinfo($ch);
            
            
            
//            echo "<pre>";
            echo($dataGardok['gardok4']['id']);
//            echo "</pre>";
            echo "<br/>";
//            echo "<pre>";
            echo($dataNaytskai['naytskaï']['id']);
//            echo "</pre>";
            
            $urlRecentGardok = 'http://prod.api.pvp.net/api/lol/euw/v1.3/game/by-summoner/' .$dataGardok['gardok4']['id'] .'/recent?api_key=af29f802-7266-4157-b1ea-3f35c4c84465';
            
            curl_setopt($ch, CURLOPT_URL, $urlRecentGardok);
            $dataRecentGardok = json_decode(curl_exec($ch),true);
            $responsecodeGardok = curl_getinfo($ch);
            
            $urlRecentGardok = 'http://prod.api.pvp.net/api/lol/euw/v1.3/game/by-summoner/' .$dataNaytskai['naytskaï']['id']. '/recent?api_key=af29f802-7266-4157-b1ea-3f35c4c84465';
            
            curl_setopt($ch, CURLOPT_URL, $urlRecentGardok);
            $dataRecentNaytskai = json_decode(curl_exec($ch),true);
            $responsecodeNaytskai = curl_getinfo($ch);
            

            echo "<br/>";
            echo "<br/>";
            

            foreach ($dataRecentGardok['games'] as $games)
            {
                if($games["subType"]=="RANKED_SOLO_5x5")
                {
                    $gardokRankedGames[] = $games['gameId'];
                    
                }
            }
            
            echo "<br/>";
            echo "<br/>";
            

            foreach ($dataRecentNaytskai['games'] as $games)
            {
                if($games["subType"]=="RANKED_SOLO_5x5")
                {
                    $naytskaiRankedGames[] = $games['gameId'];
                }
            }
            var_dump($gardokRankedGames);
            echo "<br/>";
            var_dump($naytskaiRankedGames);
            echo "<br/>";
            
            $duo = array_intersect($naytskaiRankedGames,$gardokRankedGames);
            var_dump($duo);
            
//            echo "<pre>";
//            var_dump($dataRecentGardok['games']);
//            echo "</pre>";
            
            
            foreach ($duo as $value)
            {
                echo $value;
                echo "<br/>";
                
            }
            
            curl_close($ch);
            
            
        ?>
    </body>
</html>