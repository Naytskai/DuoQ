<?php

class User {

    private $id_user;
    private $name;
    private $mail;
    private $password;
    private $date;

    //Constructor
    //-----------
    public function __construct(array $data) {
        $this->hydrate($data);
    }

    // Setters
    //--------

    public function setId_user($id_user) {
        $this->id_user = $id_user;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setMail($mail) {
        $this->mail = $mail;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    //Getters
    //-------

    public function getId_user() {
        return $this->id_user;
    }

    public function getName() {
        return $this->name;
    }

    public function getMail() {
        return $this->mail;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getDate() {
        return $this->date;
    }

    //Hydrate function
    //----------------

    public function hydrate(array $data) {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

}
