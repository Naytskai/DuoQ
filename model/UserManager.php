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
            return new Member(array());
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
     * Update the user informations with new data
     */
    public function updateUserInfo(User $user){
        $q = $this->db->prepare('UPDATE `users` SET `name`=:name,`mail`=:mail,`password`=:password WHERE `id_user`=:id');
        $q->bindValue(':name', $user->getName(), PDO::PARAM_STR);
        $q->bindValue(':mail', $user->getMail(), PDO::PARAM_STR);
        $q->bindValue(':password', $user->getPassword(), PDO::PARAM_STR);
        $q->bindValue(':id', $user->getId_user(), PDO::PARAM_STR);
        $q->execute();
    }

}
