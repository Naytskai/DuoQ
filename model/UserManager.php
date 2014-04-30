<?php

class UserManager {

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
     * This methode add a new user in the DB
     */

    public function add(User $user) {
        $q = $this->db->prepare('INSERT INTO users SET name = :name, password = :password, mail = :mail, date = NOW()');
        $q->bindValue(':name', $user->getName(), PDO::PARAM_STR);
        $q->bindValue(':password', $user->getPassword(), PDO::PARAM_STR);
        $q->bindValue(':mail', $user->getMail(), PDO::PARAM_STR);
        $q->execute();
    }

    /*
     * This methode return a user's array obtain with a user mail's address
     */

    public function getUserByMail($userMail) {
        $q = $this->db->prepare('SELECT * FROM users WHERE mail like :mail');
        $q->bindValue(':mail', $userMail, PDO::PARAM_STR);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return new User($data);
        } else {
            return new Member(array());
        }
    }

    /*
     * 
     */

    public function getUserByLoginForm(User $user) {
        $q = $this->db->prepare('SELECT * FROM users WHERE password like :password and mail like :mail');
        $q->bindValue(':mail', $user->getMail(), PDO::PARAM_STR);
        $q->bindValue(':password', $user->getPassword(), PDO::PARAM_STR);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return new User($data);
        } else {
            return new User(array());
        }
    }

    /*
     * This methode check if the given mail allready exist
     */

    public function isMailTaken($userMail) {
        $q = $this->db->prepare('SELECT * FROM users WHERE mail like :mail');
        $q->bindValue(':mail', $userMail, PDO::PARAM_STR);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * This function chech if the user's new mail is allready taken by someone
     * else
     */

    public function isMailTakenByOther(User $user, $newMail) {
        $q = $this->db->prepare('SELECT * FROM users WHERE mail like :mail');
        $q->bindValue(':mail', $newMail, PDO::PARAM_STR);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $mailProprietary = new User($data);
            if ($mailProprietary->getId_user() != $user->getId_user()) {
                return true;
            }
        } else {
            return false;
        }
    }

    /*
     * This methode check if the given userName allready exit
     */

    public function isUserNameTaken($userName) {
        $q = $this->db->prepare('SELECT * FROM users WHERE name like :userName');
        $q->bindValue(':userName', $userName, PDO::PARAM_STR);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Check the validity of password & usermail
     */

    public function isUserExist(User $user) {
        $q = $this->db->prepare('SELECT * FROM users WHERE mail like :mail and password like :password');
        $q->bindValue(':mail', $user->getMail(), PDO::PARAM_STR);
        $q->bindValue(':password', $user->getPassword(), PDO::PARAM_STR);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * This methode add a new Summonner if it dosen't already exist
     */

    public function addSummonner($SumName, $SumId) {
        $q = $this->db->prepare('INSERT INTO `summoners`(`pkSummoner`, `nameSummoner`) VALUES (:SumId,:SumName)');
        $q->bindValue(':SumId', $SumId, PDO::PARAM_STR);
        $q->bindValue(':SumName', $SumName, PDO::PARAM_STR);
        $this->db->beginTransaction();
        $q->execute();
        $lastId = $this->db->lastInsertId();
        $this->db->commit();

        return $lastId;
    }

    /*
     * This function link a summoner and a web account
     */

    public function linkSummonerUser(User $user, $sumId) {
        $q = $this->db->prepare('SELECT * FROM `r_user_summoners` WHERE `fk_user` =:userId and `fk_summoner` = :sumId ');
        $q->bindValue(':userId', $user->getId_user(), PDO::PARAM_STR);
        $q->bindValue(':sumId', $sumId, PDO::PARAM_STR);
        $this->db->beginTransaction();
        $q->execute();
        $this->db->commit();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return false;
        } else {
            $q2 = $this->db->prepare('INSERT INTO `r_user_summoners`(`fk_user`, `fk_summoner`) VALUES (:userId,:sumId)');
            $q2->bindValue(':userId', $user->getId_user(), PDO::PARAM_STR);
            $q2->bindValue(':sumId', $sumId, PDO::PARAM_STR);
            $this->db->beginTransaction();
            $q2->execute();
            $this->db->commit();
        }
    }

    /*
     * this function get all the league of legends account linked to the web
     * $user account
     */

    public function getSummonerByUser(User $user) {
        $SumArray = array();
        $q = $this->db->prepare('SELECT * FROM `r_user_summoners` inner join summoners on fk_summoner = pkSummoner WHERE `fk_user` = :idUser');
        $q->bindValue(':idUser', $user->getId_user(), PDO::PARAM_STR);
        $this->db->beginTransaction();
        $q->execute();
        while ($data = $q->fetch(PDO::FETCH_ASSOC)) {
            $SumArray[] = $data;
        }
        $this->db->commit();
        return $SumArray;
    }

    /*
     * Update the user informations with new data
     */

    public function updateUserInfo(User $user) {
        $q = $this->db->prepare('UPDATE `users` SET `name`=:name,`mail`=:mail,`password`=:password WHERE `id_user`=:id');
        $q->bindValue(':name', $user->getName(), PDO::PARAM_STR);
        $q->bindValue(':mail', $user->getMail(), PDO::PARAM_STR);
        $q->bindValue(':password', $user->getPassword(), PDO::PARAM_STR);
        $q->bindValue(':id', $user->getId_user(), PDO::PARAM_STR);
        $q->execute();
    }

}
