<?php

class LolApi
{  
    private static $_db;
    private static $_key;
    private static $_curl;
    private static $_req;
    private static $_refresh;
    private static $_limit;
    private static $_token;
    

    static function setToken($value)
    {
        $end = count(self::$_key);
        
        if($value < ($end - 1))
        {
            self::$_token = $value;
        }
        elseif($value == count(self::$_key))
        {
            self::$_token = 0;
        }
    }
    
    static function setKey($value)
    {
        self::$_key = $value;
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
    
    static function getKey()
    {
        return self::$_key;
    }
    
    
    static function changeToken()
    {
        self::setToken(self::getToken()+1);
    }

    static function init($db)
    {
        self::$_db = $db;
        self::$_req = 0;
        self::$_refresh = 0;
        self::$_limit = 0;
        self::setKey(0);
        self::setKey("af29f802-7266-4157-b1ea-3f35c4c84465");
        self::setCurl(curl_init());
        curl_setopt(self::getCurl(), CURLOPT_RETURNTRANSFER, 1);
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
       
       self::$_key = $keys;
       
   }
   
   static function execUrl($url)
   {
      do
        {
            curl_setopt(self::getCurl(), CURLOPT_URL, $url);
        
            self::$_req = self::$_req + 1;
            
            $data = curl_exec(self::getCurl());
        
            $info = curl_getinfo(self::$_curl,CURLINFO_HTTP_CODE);
            
            if($info != 200)
            {
                
                if($info == 429)
                {
                    self::$_limit = self::$_limit + 1;
//                        self::changePToken();

                    if(self::getToken() == count(self::getKey()) - 1)
                    {
                        sleep(10);
                        self::$_refresh = self::$_refresh + 1;
                    }

                }

                if($info != 429)
                {
                    return $info;
                    echo "<br/>";
                    echo "Total Request : ".self::$_req;
                    echo "<br/>";
                    echo "429 status : ".self::$_limit;
                    echo "<br/>";
                    echo "Refresh : ".self::$_refresh;
                    echo "<br/>";
                    exit();
                }     
            }
        }
        while ($info != 200);
        
//        self::changeToken();
        
        return $data;
        
   }
   
   static function getTierId($tier)
   {
        $q = self::$_db->prepare('SELECT * FROM `tiers`');

        $q->execute();

        $rawResult = $q->fetchAll(PDO::FETCH_NUM);
       
        $result = null;
        $result[] = 0;
        foreach ($rawResult as $name)
        {
            $result[$name[0]] = $name[1];
        }
        
        
        
        $id = array_search($tier,$result);
        
        return $id;
   }

   static function getDivisionId($division)
   {
       $divisionArray[] = 0;
       $divisionArray[] = "I";
       $divisionArray[] = "II";
       $divisionArray[] = "III";
       $divisionArray[] = "IV";
       $divisionArray[] = "V";
       
       $id = array_search($division,$divisionArray,true);
       
       return $id;
       
   }
   

   static function getChampions()
   {
       $url = 'http://prod.api.pvp.net/api/lol/static-data/euw/v1.1/champion?api_key='.self::getKey();

        
        $data = json_decode(self::execUrl($url),true);
        
        $champions = null;
        
        foreach ($data['data'] as $champion)
        {
//            $champions[$champion['id']] = $champion['name'];
            
            $q = self::$_db->prepare('INSERT INTO `champions`(`pkChampion`, `nameChampion`)
                                      VALUES (:championID,:championName)');
            
            $q->bindValue(':championID', $champion['id'], PDO::PARAM_INT);
            $q->bindValue(':championName', $champion['name'], PDO::PARAM_STR);
            
            $q->execute();
            
            
        }
//        return $champions;
   }
   
   
   static function getChampionById($id)
   {
        $url = 'http://prod.api.pvp.net/api/lol/static-data/euw/v1.1/champion/'.$id.'?api_key='.self::getKey();

        
        $data = json_decode(self::execUrl($url),true);
        
        return $data['name'];
        
        
   }
    
    static function getSummonerIdByName($name) {
        $url = 'http://prod.api.pvp.net/api/lol/euw/v1.4/summoner/by-name/' . $name . '?api_key=' . self::getKey();
        $data = json_decode(self::execUrl($url), true);
        $va = array_values($data);
        $value = $va[0]['id'];
        return $value;
    }
    
    
    static function getLeagueInfo($summonerId)
    {
        $url = 'http://prod.api.pvp.net/api/lol/euw/v2.3/league/by-summoner/'.$summonerId.'/entry?api_key='.self::getKey();
        
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
    
    static function getSummonerById($id) {
        $url = 'http://prod.api.pvp.net/api/lol/euw/v1.4/summoner/' . $id . '?api_key=' . self::getKey();
    
        $data = json_decode(self::execUrl($url), true);
        $va = array_values($data);
        $value = $va[0];
        return $value; 
    }
    
    static function getRankedStatsBySummonerAndChamp($idSummoner,$IdChampion)
    {
        $url = 'http://prod.api.pvp.net/api/lol/euw/v1.3/stats/by-summoner/'.$idSummoner.'/ranked?season=SEASON4&api_key='.self::getKey();
        
        
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
        $url = 'http://prod.api.pvp.net/api/lol/static-data/euw/v1.2/realm?api_key='.self::getKey();
        
        
        $data = json_decode(self::execUrl($url),true);
                
        return $data["v"];
    }
    


    static function getRecentRankedGamesBySummonerId($id)
    {
        $url = 'http://prod.api.pvp.net/api/lol/euw/v1.3/game/by-summoner/' .$id .'/recent?api_key='.self::getKey();

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
        $url = 'http://prod.api.pvp.net/api/lol/euw/v1.3/game/by-summoner/' .$id .'/recent?api_key='.self::getKey();


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
        
       $q = self::$_db->prepare('SELECT pkMatch FROM matches WHERE pkMatch = :idMatch');
       
       $q->bindValue(':idMatch', $matchId, PDO::PARAM_INT);
       
       $q->execute();
       
       $result = $q->fetch(PDO::FETCH_ASSOC);
       
       if($result == false)
       {
           return false;
       }
       elseif (is_array($result))
       {
           return true;
       }
       else
       {
           return false;
       }
    }
    
    static function insertSummoner($summonerId,$summonerName)
    {
        $q = self::$_db->prepare('SELECT `pkSummoner` FROM `summoners` WHERE pkSummoner = :summonerId');
        
        $q->bindValue(':summonerId', $summonerId, PDO::PARAM_INT);
        
        $q->execute();
        
        $result = $q->fetch(PDO::FETCH_ASSOC);
        
        if(!is_array($result))
        {
            $q = self::$_db->prepare('INSERT INTO `summoners`(`pkSummoner`, `nameSummoner`)
                                      VALUES (:idSummoner,:nameSummoner)');
            
            $q->bindValue(':idSummoner', $summonerId, PDO::PARAM_INT);
            $q->bindValue(':nameSummoner', $summonerName, PDO::PARAM_STR);
            
            $q->execute();
        }
    }

    static function insertMatch($gameData)
    {
        
       $q = self::$_db->prepare('INSERT INTO `matches`(`pkMatch`, `dateMatch`, `lengthMatch`, `resultMatch`, `versionMatch`, `fkDuo`)
                                 VALUES (:idMatch,:dateMatch,:lengthMatch,:resultMatch,:versionMatch,:fkDuo)');
       
       $q->bindValue(':idMatch', $gameData['gameId'], PDO::PARAM_INT);
       $q->bindValue(':dateMatch', $gameData['gameDate'], PDO::PARAM_INT);
       $q->bindValue(':lengthMatch', $gameData['gameLength'], PDO::PARAM_INT);
       $q->bindValue(':resultMatch', $gameData['gameResult'], PDO::PARAM_STR);
       $q->bindValue(':versionMatch', $gameData['gameVersion'], PDO::PARAM_STR);
       $q->bindValue(':fkDuo', $gameData['duo'], PDO::PARAM_INT);
       
       $q->execute();
       
       return $q;
    }
    
    static function insertResult($stats)
    {
        
        
        self::insertSummoner($stats['summonerId'], $stats['summonerName']);
        
        
        $stats['tier'] = self::getTierId(ucfirst(strtolower($stats['tier'])));
        $stats['rank'] = self::getDivisionId($stats['rank']);
        
        
        
        $q = self::$_db->prepare('INSERT INTO `results`(`fkSummoner`, `fkTier`, `divisionSummoner`, `fkChampion`, `championLevel`, `summonerSpell1`, `summonerSpell2`, `playerTeam`, `champKill`, `champDeath`, `champAssist`, `champCS`, `champGold`, `champDamage`, `champTotalK`, `champTotalD`, `champTotalA`, `champTotalWin`, `champTotalLose`, `champTotalMaxKill`, `champTotalMaxDeath`, `fkMatch`)
                                 VALUES (:idSummoner,:tierSummoner,:divisionSummoner,:champID,:champLevel,:spell1,:spell2,:playerTeam,:champKill,:champDeath,:champAssist,:champCS,:champGold,:champDamage,:champTotalK,:champTotalD,:champTotalA,:champTotalWin,:champTotalLose,:champTotalMaxKill,:champTotalMaxDeath,:idMatch)');
       
        try
        {
            $q->bindValue(':idSummoner',$stats['summonerId'], PDO::PARAM_INT);
            $q->bindValue(':tierSummoner',$stats['tier'], PDO::PARAM_STR);
            $q->bindValue(':divisionSummoner',$stats['rank'], PDO::PARAM_STR);
            $q->bindValue(':champID',$stats['championId'], PDO::PARAM_INT);
            $q->bindValue(':champLevel',$stats['level'], PDO::PARAM_INT);
            $q->bindValue(':spell1',$stats['sumSpell1'], PDO::PARAM_INT);
            $q->bindValue(':spell2',$stats['sumSpell2'], PDO::PARAM_INT);
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

            self::$_db->beginTransaction();
            $q->execute();
            $idResult = self::$_db->lastInsertId();
            self::$_db->commit();
            
            
        }
        catch (PDOException $e)
        {
            self::$_db->rollback();
        }
        
        
        
        
        foreach ($stats['items'] as $itemId)
        {
            $q = self::$_db->prepare('INSERT INTO `stuff`(`fkResult`,`fkItem`)
                                      VALUES (:idResult,:idItem)');
            
            $q->bindValue(':idResult',$idResult, PDO::PARAM_INT);
            $q->bindValue(':idItem',$itemId, PDO::PARAM_INT);
            
            
            $q->execute();
        }
    }
    
    static function getItems($stats)
    {
        $items = null;
        for ($i = 0; $i < 8; $i++)
        {
            if(isset($stats["item$i"]))
            {
                $items[] = $stats["item$i"];
            }
        }
        
        return $items;
    }
    
    
    static function getDuoId($player1,$player2)
    {
        $q = self::$_db->prepare('SELECT pkDuo FROM duo
                                  WHERE (playerOneDuo = :playerOne AND playerTwoDuo = :playerTwo)
                                  OR (playerOneDuo = :playerTwo AND playerTwoDuo = :playerOne)');
       
        $q->bindValue(':playerOne', $player1, PDO::PARAM_INT);
        $q->bindValue(':playerTwo', $player2, PDO::PARAM_INT);
        
       
        $q->execute();
       
        $result = $q->fetch(PDO::FETCH_ASSOC);
        
        return $result['pkDuo'];
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
        
        
        $stats = null;

        foreach ($playersIdPerGames as $key => $gameId)
        {
            if(!self::matchExist($key))
            {
                $playerMatchData = self::getRecentRankedGameBySummonerIdAndMatch($id1,$key);
                
                
                
                $statsData = $playerMatchData['stats'];

                $gameData['gameId'] = $playerMatchData['gameId'];
                $gameData['gameLength'] = $statsData['timePlayed'];
                $gameData['gameDate'] = $playerMatchData['createDate'];
                $gameData['gameResult'] = $statsData['win'];
                $gameData['gameVersion'] = self::getCurrentPatch();      
                $gameData['duo'] = self::getDuoId($id1,$id2);
                
                self::insertMatch($gameData);
                
                foreach ($gameId as $idPlayer)
                {
                    $stats = null;
                    $playerMatchData = self::getRecentRankedGameBySummonerIdAndMatch($idPlayer,$key);
                    
                    if(is_null($playerMatchData))
                    {
                        break;
                    }
                    
                    $statsData = $playerMatchData['stats'];
                    $summonerInfo = self::getSummonerById($idPlayer);
                    $championId = $playerMatchData['championId'];
                    $leagueInfo = self::getLeagueInfo($idPlayer);

                    $rawData = self::getRankedStatsBySummonerAndChamp($idPlayer,$championId);

                    $stats['matchId'] = $key;
                    $stats['summonerId'] = $idPlayer;
                    $stats['summonerName'] = $summonerInfo['name'];
                    $stats['championName'] = self::getChampionById($championId);
                    $stats['championId'] = $championId;
                    $stats['level'] = $statsData['level'];
                    $stats['sumSpell1'] = $playerMatchData['spell1'];
                    $stats['sumSpell2'] = $playerMatchData['spell2'];
                    $stats['kill'] = isset($statsData['championsKilled'])?$statsData['championsKilled']:0;
                    $stats['death'] = isset($statsData['numDeaths'])?$statsData['numDeaths']:0;
                    $stats['assist'] = isset($statsData['assists'])?$statsData['assists']:0;
                    $stats['cs'] = isset($statsData['minionsKilled'])?$statsData['minionsKilled']:0;
                    $stats['gold'] = isset($statsData['goldEarned'])?$statsData['goldEarned']:0;
                    $stats['ddtc'] = isset($statsData['totalDamageDealtToChampions'])?$statsData['totalDamageDealtToChampions']:0;
                    $stats['team'] = $statsData['team'];
                    $stats['tier'] = isset($leagueInfo['tier'])?$leagueInfo['tier']:0;
                    $stats['rank'] = isset($leagueInfo['rank'])?$leagueInfo['rank']:0;
                    $stats['totalWin'] = $rawData['win'];
                    $stats['totalLose'] = $rawData['lose'];
                    $stats['totalKills'] = $rawData['totalKills'];
                    $stats['totalDeaths'] = $rawData['totalDeaths'];
                    $stats['totalAssists'] = $rawData['totalAssists'];
                    $stats['maxKills'] = $rawData['maxKills'];
                    $stats['maxDeaths'] = $rawData['maxDeaths'];
                    $stats['items'] = self::getItems($statsData);
                    
                    self::insertResult($stats); 
                }
            }
        }
        
        echo "Total Request : ".self::$_req;
        echo "<br/>";
        echo "429 status : ".self::$_limit;
        echo "<br/>";
        echo "Refresh : ".self::$_refresh;
        echo "<br/>";
    }
}
