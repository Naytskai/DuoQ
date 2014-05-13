<?php

class DuoManager {

    private $db;

//Constructor
//-----------

    public function __construct($db) {
        $this->setDb($db);
    }

//Setter
//------

    public function setDb(PDO $db) {
        $this->db = $db;
    }

    /*
     * This methode add a duo in the DB
     */

    public function add(Duo $duo) {
        $lastId;
        try {
            $q = $this->db->prepare('INSERT INTO `duo`(`playerOneDuo`, `playerTwoDuo`, `date`) VALUES (:playerOneDuo,:playerTwoDuo,NOW())');
            $q->bindValue(':playerOneDuo', $duo->getPlayerOneDuo(), PDO::PARAM_STR);
            $q->bindValue(':playerTwoDuo', $duo->getPlayerTwoDuo(), PDO::PARAM_STR);
            $this->db->beginTransaction();
            $q->execute();
            $lastId = $this->db->lastInsertId();
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollback();
        }
        return $lastId;
    }

    /*
     * This methode remove a Duo 
     */

    public function removeDuo($duoId) {
        //select and delete all match's result with the duo Id
        $info = true;
        try {
            $q2 = $this->db->prepare('SELECT * FROM `matches` WHERE `fkDuo` =:idDuo');
            $q2->bindValue(':idDuo', $duoId, PDO::PARAM_STR);
            $this->db->beginTransaction();
            $q2->execute();
            while ($data = $q2->fetch(PDO::FETCH_ASSOC)) {
                $q3 = $this->db->prepare('SELECT * FROM `results` WHERE `fkMatch` = :pkMatch');
                $q3->bindValue(':pkMatch', $data['pkMatch'], PDO::PARAM_STR);
                $q3->execute();
                while ($data2 = $q3->fetch(PDO::FETCH_ASSOC)) {
                    //remove all stuff linked to a result
                    $q4 = $this->db->prepare('DELETE FROM `stuff` WHERE `fkResult` = :idResult');
                    $q4->bindValue(':idResult', $data2['pkResult'], PDO::PARAM_STR);
                    $q4->execute();
                    //--
                }
                //remove all the duo's match
                $q5 = $this->db->prepare('DELETE FROM `results` WHERE `fkMatch` = :pkMatch');
                $q5->bindValue(':pkMatch', $data['pkMatch'], PDO::PARAM_STR);
                $q5->execute();
                //--
            }
            //remove all match from the duoId
            $q6 = $this->db->prepare('DELETE FROM `matches` WHERE `fkDuo` =:idDuo');
            $q6->bindValue(':idDuo', $duoId, PDO::PARAM_STR);
            $q6->execute();
            //--
            $this->db->commit();
        } catch (PDOException $e) {
            $info = false;
            $this->db->rollback();
        }

        //unlink the duo and the user
        try {
            $q = $this->db->prepare('DELETE FROM `r_duo_user` WHERE `fk_duo` = :idDuo');
            $q->bindValue(':idDuo', $duoId, PDO::PARAM_STR);
            $this->db->beginTransaction();
            $q->execute();
            $this->db->commit();
        } catch (PDOException $e) {
            $info = false;
            $this->db->rollback();
        }

        //remove the duo
        try {
            $q = $this->db->prepare('DELETE FROM `duo` WHERE `pkDuo` = :idDuo');
            $q->bindValue(':idDuo', $duoId, PDO::PARAM_STR);
            $this->db->beginTransaction();
            $q->execute();
            $this->db->commit();
        } catch (PDOException $e) {
            $info = false;
            $this->db->rollback();
        }
        return $info;
    }

    /*
     * This function return all the duo with the logged web user
     */

    public function getDuoByUser(User $user) {
        $duoArray = array();
        try {
            $q = $this->db->prepare('SELECT * FROM `r_duo_user` inner join duo on fk_duo = pkDuo WHERE `fk_user`=:fk_user');
            $q->bindValue(':fk_user', $user->getId_user(), PDO::PARAM_STR);
            $this->db->beginTransaction();
            $q->execute();
            while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
                $duoArray[] = $data;
            }
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollback();
        }
        return $duoArray;
    }

    public function getDuoById($duoId) {
        $duoArray = array();
        try {
            $q = $this->db->prepare('SELECT * FROM `duo` WHERE `pkDuo` = :duoId');
            $q->bindValue(':duoId', $duoId, PDO::PARAM_STR);
            $this->db->beginTransaction();
            $q->execute();
            while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
                $duoArray = $data;
            }
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollback();
            print_r($e);
        }
        return $duoArray;
    }

    /*
     * This function get all the matches by duo summoners
     */

    public function getMatchesByDuo($DuoId) {
        $matchArray = array();
        try {
            $q = $this->db->prepare('SELECT * FROM `matches` WHERE `fkDuo` =:duoID ORDER BY  `dateMatch` DESC ');
            $q->bindValue(':duoID', $DuoId, PDO::PARAM_STR);
            $this->db->beginTransaction();
            $q->execute();
            while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
                $matchArray[] = $data;
            }
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollback();
        }
        return $matchArray;
    }

    /*
     * This function return a array of match by id
     */

    public function getMatchesById($matchId) {
        $matchArray = array();
        try {
            $q = $this->db->prepare('SELECT * FROM `matches` WHERE `pkMatch` = :idMatch');
            $q->bindValue(':idMatch', $matchId, PDO::PARAM_STR);
            $this->db->beginTransaction();
            $q->execute();
            while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
                $matchArray[] = $data;
            }
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollback();
        }
        return $matchArray;
    }

    /*
     * This function return all the results by match
     */

    public function getResultByMatch($matchId) {
        $resultArray = array();
        try {
            $q = $this->db->prepare('SELECT * FROM `results` inner join tiers on fkTier = pkTier WHERE `fkMatch` = :idMatch ');
            $q->bindValue(':idMatch', $matchId, PDO::PARAM_STR);
            $this->db->beginTransaction();
            $q->execute();
            while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
                $resultArray[] = $data;
            }
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollback();
        }
        return $resultArray;
    }

    /*
     * This methode add a new Summonner if it dosen't already exist
     */

    public function addSummonner($SumName, $SumId) {
        $q = $this->db->prepare('INSERT INTO `summoners`(`pkSummoner`, `nameSummoner`) VALUES (:SumId,:SumName)');
        $q->bindValue(':SumId', $SumId, PDO::PARAM_STR);
        $q->bindValue(':SumName', $SumName, PDO::PARAM_STR);
        $q->execute();

        return $this->db->lastInsertId();
    }

    /*
     * This function get the lane id by name
     */

    public function getLaneId($laneName) {
        $q = $this->db->prepare('SELECT * FROM `lane` WHERE name like :laneName');
        $q->bindValue(':laneName', $laneName, PDO::PARAM_STR);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return $data['pkLane'];
        }
    }

    /*
     * This function get the lane name by id
     */

    public function getLaneName($laneId) {
        $q = $this->db->prepare('SELECT * FROM `lane` WHERE `pkLane` = :laneId');
        $q->bindValue(':laneId', $laneId, PDO::PARAM_STR);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return $data['name'];
        }
    }

    /*
     * This function add a lane to a game result
     */

    public function addLaneToResult($resultId, $laneId) {
        try {
            $q = $this->db->prepare('UPDATE `results` SET `fkLane`=:idLane WHERE `pkResult` = :idResult');
            $q->bindValue(':idLane', $laneId, PDO::PARAM_STR);
            $q->bindValue(':idResult', $resultId, PDO::PARAM_STR);
            $this->db->beginTransaction();
            $q->execute();
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollback();
        }
    }

    /*
     * Get summ from db by id
     */

    public function getSummonerFromDb($SumId) {
        try {
            $q = $this->db->prepare('SELECT * FROM `summoners` WHERE `pkSummoner` =:sumId');
            $q->bindValue(':sumId', $SumId, PDO::PARAM_STR);
            $this->db->beginTransaction();
            $q->execute();
            $data = $q->fetch(PDO::FETCH_ASSOC);
            $summoner = $data;
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollback();
        }
        return $summoner;
    }

    public function getChampionFromDb($champId) {
        try {
            $q = $this->db->prepare('SELECT * FROM `champions` WHERE `pkChampion` = :champId');
            $q->bindValue(':champId', $champId, PDO::PARAM_STR);
            $this->db->beginTransaction();
            $q->execute();
            $data = $q->fetch(PDO::FETCH_ASSOC);
            $matchArray = $data['nameChampion'];
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollback();
        }
        return $matchArray;
    }

    /*
     * This function link users and duo queues
     */

    public function linkDuoAndUser(User $user, $duoId) {
        $q = $this->db->prepare('SELECT * FROM `r_duo_user` WHERE `fk_user`= :userId and `fk_duo` = :idDuo');
        $q->bindValue(':userId', $user->getId_user(), PDO::PARAM_STR);
        $q->bindValue(':idDuo', $duoId, PDO::PARAM_STR);
        $this->db->beginTransaction();
        $q->execute();
        $this->db->commit();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return false;
        } else {
            $q2 = $this->db->prepare('INSERT INTO `r_duo_user`(`fk_user`, `fk_duo`) VALUES (:fk_user,:fk_duo)');
            $q2->bindValue(':fk_user', $user->getId_user(), PDO::PARAM_STR);
            $q2->bindValue(':fk_duo', $duoId, PDO::PARAM_STR);
            $this->db->beginTransaction();
            $q2->execute();
            $this->db->commit();
        }
    }

    /*
     * This function return an array with all the registered duo
     */

    public function getAllDuo() {
        $duoArray = array();
        try {
            $q = $this->db->prepare('SELECT * FROM `duo`');
            $this->db->beginTransaction();
            $q->execute();
            $data = $q->fetch(PDO::FETCH_ASSOC);
            while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
                $duoArray[] = new Duo($data);
            }
            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollback();
        }
        return $duoArray;
    }

    /*
     * This function convert an int to a roman numeral
     */

    public function romanNumerals($num) {
        $n = intval($num);
        $res = '';

        /*         * * roman_numerals array  ** */
        $roman_numerals = array(
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1);

        foreach ($roman_numerals as $roman => $number) {
            /*             * * divide to get  matches ** */
            $matches = intval($n / $number);

            /*             * * assign the roman char * $matches ** */
            $res .= str_repeat($roman, $matches);

            /*             * * substract from the number ** */
            $n = $n % $number;
        }

        /*         * * return the res ** */
        return $res;
    }

}
