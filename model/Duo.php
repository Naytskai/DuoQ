<?php

class Duo {

    private $pkDuo;
    private $playerOneDuo;
    private $playerTwoDuo;
    private $date;
    private $updatedDate;

    //Constructor
    //-----------
    public function __construct(array $data) {
        $this->hydrate($data);
    }

    // Setters
    //--------

    public function setPkDuo($idDuo) {
        $this->pkDuo = $idDuo;
    }

    public function setPlayerOneDuo($playerOneDuo) {
        $this->playerOneDuo = $playerOneDuo;
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

    public function setUpdatedDate($updatedDate) {
        $this->updatedDate = $updatedDate;
    }

    //Getters
    //-------

    public function getPkDuo() {
        return $this->pkDuo;
    }

    public function getPlayerOneDuo() {
        return $this->playerOneDuo;
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

    public function getUpdatedDate() {
        return $this->updatedDate;
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
