<?php

class Duo {

    private $idDuo;
    private $playerOneDuo;
    private $playerOneLaneId;
    private $playerTwoDuo;
    private $playerTwoLaneId;
    private $date;

    //Constructor
    //-----------
    public function __construct(array $data) {
        $this->hydrate($data);
    }

    // Setters
    //--------

    public function setIdDuo($idDuo) {
        $this->idDuo = $idDuo;
    }

    public function setPlayerOneDuo($playerOneDuo) {
        $this->playerOneDuo = $playerOneDuo;
    }

    public function setPlayerOneLaneId($playerOneLaneId) {
        $this->playerOneLaneId = $playerOneLaneId;
    }

    public function setPlayerTwoDuo($playerTwoDuo) {
        $this->playerTwoDuo = $playerTwoDuo;
    }

    public function setPlayerTwoLaneId($playerTwoLaneId) {
        $this->playerTwoLaneId = $playerTwoLaneId;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    //Getters
    //-------

    public function getIdDuo() {
        return $this->idDuo;
    }

    public function getPlayerOneDuo() {
        return $this->playerOneDuo;
    }

    public function getPlayerOneLaneId() {
        return $this->playerOneLaneId;
    }

    public function getPlayerTwoDuo() {
        return $this->playerTwoDuo;
    }

    public function getPlayerTwoLaneId() {
        return $this->playerTwoLaneId;
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
