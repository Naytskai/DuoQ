<?php

class LolApi
{  
    private static $_db;
    private static $_token;
//    private static $_ptoken;
    private static $_keys;
    private static $_curl;
    private static $_req;
    private static $_refresh;
    private static $_limit;
//    private static $_proxyList;
//    private static $_failProxy;
    

    static function setToken($value)
    {
        $end = count(self::$_keys);
        
        if($value < ($end - 1))
        {
            self::$_token = $value;
        }
        elseif($value == count(self::$_keys))
        {
            self::$_token = 0;
        }
    }
    
//    static function setPToken($value)
//    {
//        if($value < count(self::$_proxyList))
//        {
//            self::$_ptoken = $value;
//        }
//        elseif($value == count(self::$_proxyList))
//        {
//            self::$_ptoken = 0;
//        }
//    }
    
    static function setKeys($value)
    {
        self::$_keys = $value;
    }
    
    static function setCurl($value)
    {
        self::$_curl = $value;
    }

    static function getCurl()
    {
        return self::$_curl;
    }    
    
    
    static function getToken()
    {
        return self::$_token;   
    }
    
//    static function getPToken()
//    {
//        return self::$_ptoken;   
//    }
    
    static function getKeys()
    {
        return self::$_keys;
    }
    
    
    static function changeToken()
    {
        self::setToken(self::getToken()+1);
    }
    
//    static function changePToken()
//    {
//        self::setPToken(self::getPToken()+1);
//    }

    static function init($db,$keys)
    {
        self::$_db = $db;
        self::$_req = 0;
        self::$_refresh = 0;
        self::$_limit = 0;
//        self::$_failProxy = null;
//        self::$_proxyList = array("88.80.113.1:3128",
//                                    "94.228.205.2:8080",
//                                    "78.109.137.225:3128",
//                                    "95.31.42.89:3128",
//                                    "213.181.73.145:80",
//                                    "95.170.133.86:3128",
//                                    "94.137.239.19:81",
//                                    "94.228.205.33:8080",
//                                    "46.181.135.215:3128",
//                                    "46.147.166.49:3128",
//                                    "109.194.65.175:3128",
//                                    "78.29.9.104:3128",
//                                    "94.126.17.68:3128",
//                                    "195.103.219.126:8080",
//                                    "82.209.198.2:1080",
//                                    "88.80.113.1:3128",
//                                    "94.214.105.237:80",
//                                    "74.105.146.94:41643",
//                                    "195.103.219.102:8080",
//                                    "176.35.77.154:3128",
//                                    "202.133.56.185:80",
//                                    "195.138.83.32:81",
//                                    "85.185.45.254:80");
        
        self::setToken(0);
//        self::setPToken(0);
        
        self::setKeys($keys);
        
        self::setCurl(curl_init());

        curl_setopt(self::getCurl(), CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt(self::getCurl(), CURLOPT_FOLLOWLOCATION, 1);
//        curl_setopt(self::getCurl(), CURLOPT_HEADER, 1);
        
        
//        self::loadkeys();
    }
    
    
    static function loadkeys()
   {
       $q = self::$_db->prepare('SELECT `key` FROM `keys`');
      
       $q->execute();
       $data = $q->fetchAll(PDO::FETCH_NUM);
       
       $keys = null;
       
       foreach ($data as $array)
       {
           foreach ($array as $key)
           {
               $keys[] = $key;
           }
       }
       $keys = null;
       
       self::$_keys = $keys;
       
   }
   
   static function execUrl($url)
   {
      do
        {
//            $proxy = self::$_proxyList[self::getPToken()];
            curl_setopt(self::getCurl(), CURLOPT_URL, $url);
//            curl_setopt(self::getCurl(), CURLOPT_PROXY, $proxy);
        
            self::$_req = self::$_req + 1;
            
            $data = curl_exec(self::getCurl());
        
            $info = curl_getinfo(self::$_curl,CURLINFO_HTTP_CODE);
            
            if($info != 200)
            {
                
                if($info == 429)
                {
                    self::$_limit = self::$_limit + 1;
//                        self::changePToken();

                    if(self::getToken() == count(self::getKeys()) - 1)
                    {
                        sleep(10);
                        self::$_refresh = self::$_refresh + 1;
                    }

                }

                if($info != 429)
                {
//                        array_push(self::$_failProxy,self::$_proxyList[self::getPToken()]."=>".$info);

//                        self::changePToken();
                    echo $info;
                    echo "<br/>";
                    echo "Total Request : ".self::$_req;
                    echo "<br/>";
                    echo "429 status : ".self::$_limit;
                    echo "<br/>";
                    echo "Refresh : ".self::$_refresh;
                    echo "<br/>";
//                        echo "Proxy IP : ".self::$_proxyList[self::getPToken()];
//                        echo "<br/>";
                    exit();
                }     
            }
        }
        while ($info != 200);
        
        self::changeToken();
        
        return $data;
        
   }
   
   static function getChampions()
   {
       $url = 'http://prod.api.pvp.net/api/lol/static-data/euw/v1.1/champion?api_key='.self::$_keys[self::getToken()];

        
        $data = json_decode(self::execUrl($url),true);
        
        $champions = null;
        
        foreach ($data['data'] as $champion)
        {
            $champions[$champion['id']] = $champion['name'];
        }
        return $champions;
   }
   
   
   static function getChampionById($id)
   {
        $url = 'http://prod.api.pvp.net/api/lol/static-data/euw/v1.1/champion/'.$id.'?api_key='.self::$_keys[self::getToken()];

        
        $data = json_decode(self::execUrl($url),true);
        
        return $data['name'];
        
        
   }
    
    static function getSummonerIdByName($name)
    {
        $url = 'http://prod.api.pvp.net/api/lol/euw/v1.4/summoner/by-name/'.$name.'?api_key='.self::$_keys[self::getToken()];
        
        
        $data = json_decode(self::execUrl($url),true);
        
        return array_values($data)[0]['id'];
        
    }
    
    
    static function getLeagueInfo($summonerId)
    {
        $url = 'http://prod.api.pvp.net/api/lol/euw/v2.3/league/by-summoner/'.$summonerId.'/entry?api_key='.self::$_keys[self::getToken()];
        
        $data = json_decode(self::execUrl($url),true);
        
        
        $info = null;
        foreach ($data as $ranked)
        {
            if($ranked["queueType"] == "RANKED_SOLO_5x5")
            {
                $info["tier"] = $ranked["tier"];
                $info["rank"] = $ranked["rank"]; 
            }
        }
        
        return $info;
    }
    
    
    static function getSummonerById($id)
    {
        $url = 'http://prod.api.pvp.net/api/lol/euw/v1.4/summoner/'.$id.'?api_key='.self::$_keys[self::getToken()];
        
        $data = json_decode(self::execUrl($url),true);
                
        return array_values($data)[0];
    }
    
    static function getRankedStatsBySummonerAndChamp($idSummoner,$IdChampion)
    {
        $url = 'http://prod.api.pvp.net/api/lol/euw/v1.3/stats/by-summoner/'.$idSummoner.'/ranked?season=SEASON4&api_key='.self::$_keys[self::getToken()];
        
        
        $data = json_decode(self::execUrl($url),true);
        
        $data["champions"];
        
        $championStats = null;
        
        foreach ($data["champions"] as $champion)
        {
            if($champion["id"] == $IdChampion)
            {
                $championStats['win'] = $champion['stats']['totalSessionsWon'];
                $championStats['lose'] = $champion['stats']['totalSessionsLost'];
                $championStats['totalKills'] = $champion['stats']['totalChampionKills'];
                $championStats['totalDeaths'] = $champion['stats']['totalDeathsPerSession'];
                $championStats['totalAssists'] = $champion['stats']['totalAssists'];
                $championStats['maxKills'] = $champion['stats']['maxChampionsKilled'];
                $championStats['maxDeaths'] = $champion['stats']['maxNumDeaths'];
                
                break;
            }
        }
        
        return $championStats;
    }
    
    
    static function getCurrentPatch()
    {
        $url = 'http://prod.api.pvp.net/api/lol/static-data/euw/v1.2/realm?api_key='.self::$_keys[self::getToken()];
        
        
        $data = json_decode(self::execUrl($url),true);
                
        return $data["v"];
    }
    


    static function getRecentRankedGamesBySummonerId($id)
    {
        $url = 'http://prod.api.pvp.net/api/lol/euw/v1.3/game/by-summoner/' .$id .'/recent?api_key='.self::$_keys[self::getToken()];

        $data = $data = json_decode(self::execUrl($url),true);

        $rankedGames = null;

        foreach ($data['games'] as $games)
        {
            if($games["subType"]=="RANKED_SOLO_5x5")
            {
                $rankedGames[] = $games;
            }
        }

        return $rankedGames;
    }
    
    static function getRecentRankedGameBySummonerIdAndMatch($id,$match)
    {
        $url = 'http://prod.api.pvp.net/api/lol/euw/v1.3/game/by-summoner/' .$id .'/recent?api_key='.self::$_keys[self::getToken()];


        $data = json_decode(self::execUrl($url),true);


        foreach ($data['games'] as $games)
        {
            if(($games["subType"] == "RANKED_SOLO_5x5") && ($games["gameId"] == $match))
            {
                return $games;
            }
        }
    }
    
    static function matchExist($matchId)
    {
        
       $q = self::$_db->prepare('SELECT idMatch FROM matches WHERE idMatch = :idMatch');
       
       $q->bindValue(':idMatch', $matchId, PDO::PARAM_INT);
       
       $q->execute();
       
       $result = $q->fetch(PDO::FETCH_ASSOC);
       
       return isset($result)?false:true;
    }
    
    static function insertMatch($gameData)
    {
        
       $q = self::$_db->prepare('INSERT INTO `matches`(`idMatch`, `dateMatch`, `lengthMatch`, `resultMatch`, `versionMatch`, `playerOneMatch`, `playerTwoMatch`)
                                 VALUES (:idMatch,:dateMatch,:lengthMatch,:resultMatch,:versionMatch,:playerOneMatch,:playerTwoMatch)');
       
       $q->bindValue(':idMatch', $gameData['gameId'], PDO::PARAM_INT);
       $q->bindValue(':dateMatch', $gameData['gameDate'], PDO::PARAM_INT);
       $q->bindValue(':lengthMatch', $gameData['gameLength'], PDO::PARAM_INT);
       $q->bindValue(':resultMatch', $gameData['gameResult'], PDO::PARAM_STR);
       $q->bindValue(':versionMatch', $gameData['gameVersion'], PDO::PARAM_STR);
       $q->bindValue(':playerOneMatch', $gameData['playerOne'], PDO::PARAM_INT);
       $q->bindValue(':playerTwoMatch', $gameData['playerTwo'], PDO::PARAM_INT);
       
       $q->execute();
       
       return $q;
    }
    
    static function insertResult($stats)
    {
        
       $q = self::$_db->prepare('INSERT INTO `results`(`idSummoner`, `nameSummoner`, `tierSummoner`, `divisionSummoner`, `champLevel`, `playerTeam`, `champKill`, `champDeath`, `champAssist`, `champCS`, `champGold`, `champDamage`, `champTotalK`, `champTotalD`, `champTotalA`, `champTotalWin`, `champTotalLose`, `champTotalMaxKill`, `champTotalMaxDeath`, `idMatch`)
                                 VALUES (:idSummoner,:nameSummoner,:tierSummoner,:divisionSummoner,:champLevel,:playerTeam,:champKill,:champDeath,:champAssist,:champCS,:champGold,:champDamage,:champTotalK,:champTotalD,:champTotalA,:champTotalWin,:champTotalLose,:champTotalMaxKill,:champTotalMaxDeath,:idMatch)');
       
       $q->bindValue(':idSummoner',$stats['summonerId'], PDO::PARAM_INT);
       $q->bindValue(':nameSummoner',$stats['summonerName'], PDO::PARAM_STR);
       $q->bindValue(':tierSummoner',$stats['tier'], PDO::PARAM_STR);
       $q->bindValue(':divisionSummoner',$stats['rank'], PDO::PARAM_STR);
       $q->bindValue(':champLevel',$stats['level'], PDO::PARAM_INT);
       $q->bindValue(':playerTeam',$stats['team'], PDO::PARAM_INT);
       $q->bindValue(':champKill',$stats['kill'], PDO::PARAM_INT);
       $q->bindValue(':champDeath',$stats['death'], PDO::PARAM_INT);
       $q->bindValue(':champAssist',$stats['assist'], PDO::PARAM_INT);
       $q->bindValue(':champCS',$stats['cs'], PDO::PARAM_INT);
       $q->bindValue(':champGold',$stats['gold'], PDO::PARAM_INT);
       $q->bindValue(':champDamage',$stats['ddtc'], PDO::PARAM_INT);
       $q->bindValue(':champTotalK',$stats['totalKills'], PDO::PARAM_INT);
       $q->bindValue(':champTotalD',$stats['totalDeaths'], PDO::PARAM_INT);
       $q->bindValue(':champTotalA',$stats['totalAssists'], PDO::PARAM_INT);
       $q->bindValue(':champTotalWin',$stats['totalWin'], PDO::PARAM_INT);
       $q->bindValue(':champTotalLose',$stats['totalLose'], PDO::PARAM_INT);
       $q->bindValue(':champTotalMaxKill',$stats['maxKills'], PDO::PARAM_INT);
       $q->bindValue(':champTotalMaxDeath',$stats['maxDeaths'], PDO::PARAM_INT);
       $q->bindValue(':idMatch',$stats['matchId'], PDO::PARAM_INT);
       
       
       $q->execute();
       
       return $q;
    }
    
    static function getDuoRankedGames($player1,$player2)
    {
        
        $id1 = self::getSummonerIdByName($player1);
        $id2 = self::getSummonerIdByName($player2);
        
        $player1RankedGames = self::getRecentRankedGamesBySummonerId($id1);
        
        $player1Games = null;
        foreach ($player1RankedGames as $gameId)
        {
            $player1Games[] = $gameId['gameId'];
        }
        
        
        $player2RankedGames = self::getRecentRankedGamesBySummonerId($id2);
        
        $player2Games = null;
        foreach ($player2RankedGames as $gameId)
        {
            $player2Games[] = $gameId['gameId'];
        }
        
        $duoGames = array_intersect($player1Games,$player2Games);
        
//        $duoGames = null;
//        foreach ($player1RankedGames as $games)
//        {
//            foreach ($duo as $id)
//            {
//                if($games['gameId'] == $id)
//                {
//                    $duoGames[] = $games;
//                }
//            }
//        }
        
        
        $playersId = null;
        $playersIdPerGames = null;
        
        foreach ($duoGames as $gameId)
        {
            $playersId = null;
            $playersId[] = $id1;
            
            $games = self::getRecentRankedGameBySummonerIdAndMatch($id1,$gameId);
            
            foreach ($games['fellowPlayers'] as $players)
            {
                $playersId[] = $players['summonerId'];
            }
            $playersIdPerGames[$gameId] = $playersId;
        }
        
//        $plop = $playersIdPerGames;
        
        $team = null;
        $stats = null;
        
        
//        $champions = self::getChampions();
        
        
        foreach ($playersIdPerGames as $key => $gameId)
        {
//            $playersIdPerGames = null;

            
            if(!self::matchExist($key))
            {
                $playerMatchData = self::getRecentRankedGameBySummonerIdAndMatch($id1,$key);
                $statsData = $playerMatchData['stats'];

                $gameData['gameId'] = $playerMatchData['gameId'];
                $gameData['gameLength'] = $statsData['timePlayed'];
                $gameData['gameDate'] = $playerMatchData['createDate'];
                $gameData['gameResult'] = $statsData['win'];
                $gameData['gameVersion'] = self::getCurrentPatch();      
                $gameData['playerOne'] = $id1;                         
                $gameData['playerTwo'] = $id2;
                
                self::insertMatch($gameData);
                
            }
            else
            {
                break;
            }
            
            
            foreach ($gameId as $idPlayer)
            {
                $stats = null;
                $playerMatchData = self::getRecentRankedGameBySummonerIdAndMatch($idPlayer,$key);
                $statsData = $playerMatchData['stats'];
                $summonerInfo = self::getSummonerById($idPlayer);
                $championId = $playerMatchData['championId'];
                $leagueInfo = self::getLeagueInfo($idPlayer);
                
                $rawData = self::getRankedStatsBySummonerAndChamp($idPlayer,$championId);

                $stats['matchId'] = $key;
                $stats['summonerId'] = $idPlayer;
                $stats['summonerName'] = $summonerInfo['name'];
                $stats['championName'] = self::getChampionById($championId);
                $stats['level'] = $statsData['level'];
                $stats['kill'] = isset($statsData['championsKilled'])?$statsData['championsKilled']:0;
                $stats['death'] = isset($statsData['numDeaths'])?$statsData['numDeaths']:0;
                $stats['assist'] = isset($statsData['assists'])?$statsData['assists']:0;
                $stats['cs'] = isset($statsData['minionsKilled'])?$statsData['minionsKilled']:0;
                $stats['gold'] = isset($statsData['goldEarned'])?$statsData['goldEarned']:0;
                $stats['ddtc'] = isset($statsData['totalDamageDealtToChampions'])?$statsData['totalDamageDealtToChampions']:0;
                $stats['team'] = $statsData['team'];
                $stats['tier'] = $leagueInfo['tier'];
                $stats['rank'] = $leagueInfo['rank'];
                $stats['totalWin'] = $rawData['win'];
                $stats['totalLose'] = $rawData['lose'];
                $stats['totalKills'] = $rawData['win'];
                $stats['totalDeaths'] = $rawData['totalDeaths'];
                $stats['totalAssists'] = $rawData['totalAssists'];
                $stats['maxKills'] = $rawData['maxKills'];
                $stats['maxDeaths'] = $rawData['maxDeaths'];
                
                self::insertResult($stats);
                        
            }
            
        }
        
        echo "Total Request : ".self::$_req;
        echo "<br/>";
        echo "429 status : ".self::$_limit;
        echo "<br/>";
        echo "Refresh : ".self::$_refresh;
        echo "<br/>";
//        echo "<pre>";
//        var_dump(self::$_failProxy);
//        echo "</pre>";
        

    }
}
