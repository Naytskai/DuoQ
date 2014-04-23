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
        $q = $this->db->prepare('INSERT INTO `duo`(`playerOneDuo`, `playerOneLaneId`, `playerTwoDuo`, `playerTwoLaneId`, `date`) VALUES (:playerOneDuo,:playerOneLaneId,:playerTwoDuo,:playerTwoLaneId,NOW())');
        $q->bindValue(':playerOneDuo', $duo->getPlayerOneDuo(), PDO::PARAM_STR);
        $q->bindValue(':playerOneLaneId', $duo->getPlayerOneLaneId(), PDO::PARAM_STR);
        $q->bindValue(':playerTwoDuo', $duo->getPlayerTwoDuo(), PDO::PARAM_STR);
        $q->bindValue(':playerTwoLaneId', $duo->getPlayerTwoLaneId(), PDO::PARAM_STR);
        $q->execute();
        return $this->db->lastInsertId();
    }

    /*
     * 
     */
    public function getDuoByUser(User $user) {
        try {
            $q = $this->db->prepare('SELECT * FROM `r_duo_user` WHERE `fk_user`=:fk_user inner join duo on fk_duo = idDuo');
            $q->bindValue(':fk_user', $user->getId_user(), PDO::PARAM_STR);
            $q->beginTransaction();
            $q->execute();
            $q->commit();
        } catch (PDOException $e) {
            $q->rollback();
        }
    }

    /*
     * This methode add a new Summonner if it dosen't already exist
     */

    public function addSummonner($SumName, $SumId) {
        $q = $this->db->prepare('INSERT INTO `summonners`(`idSummoner`, `nameSummoner`) VALUES (:SumId,:SumName)');
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
            return $data['id_lane'];
        }
    }

    /*
     * This function link users and duo queues
     */

    public function linkDuoAndUser(User $user, $duoId) {
        $q = $this->db->prepare('INSERT INTO `r_duo_user`(`fk_user`, `fk_duo`) VALUES (:fk_user,:fk_duo)');
        $q->bindValue(':fk_user', $user->getId_user(), PDO::PARAM_STR);
        $q->bindValue(':fk_duo', $duoId, PDO::PARAM_STR);
        $q->execute();
    }

}
