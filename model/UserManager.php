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

    //Methods
    //-------

    public function add(User $user) {
        $q = $this->db->prepare('INSERT INTO user SET name = :name, password = :password, mail = :mail, date = NOW()');
        $q->bindValue(':name', $user->getName(), PDO::PARAM_STR);
        $q->bindValue(':password', $user->getPassword(), PDO::PARAM_STR);
        $q->bindValue(':mail', $user->getMail(), PDO::PARAM_STR);
        $q->execute();
    }

    public function getMembreByName($userName) {
        $q = $this->db->prepare('SELECT * FROM user WHERE name like :name');
        $q->bindValue(':name', $userName, PDO::PARAM_STR);
        $q->execute();
        $data = $q->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return new User($data);
        } else {
            return new Member(array());
        }
    }
}