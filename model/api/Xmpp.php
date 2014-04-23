<?php

include_once("XMPPHP/XMPP.php");

class Xmpp {

    //--------------------------------------------------------------------------
    //                         Attributes                            
    //--------------------------------------------------------------------------
    private $userName;
    private $password;
    private $region;
    private $contactList;
    private $connexion;

    //--------------------------------------------------------------------------
    //                         Constructor                            
    //--------------------------------------------------------------------------

    function __construct($userName, $password, $region) {
        $this->userName = $userName;
        $this->password = $password;
        $this->region = $region;
        $this->connexion = new XMPPHP_XMPP('chat.' . $region . '.lol.riotgames.com', 5223, $userName, 'AIR_' . $password, 'xiff', 'pvp.net', $printlog = true, $loglevel = XMPPHP_Log::LEVEL_ERROR);
        $this->connexion->connect();
        $this->refreshFriendList();
    }

    //--------------------------------------------------------------------------
    //                         Methodes                            
    //--------------------------------------------------------------------------


    /*
     * This function catch the friend list of the account used to instant this 
     * class
     */
    public function refreshFriendList() {
        $firstContact = "";
        $pass = 0;
        $totalRound = 1;
        while ($end_loop <= $totalRound) {
            $payloads = $this->connexion->processUntil(array('end_stream', 'session_start', 'roster_received', 'presence'));
            foreach ($payloads as $event) {
                $pl = $event[1];
                switch ($event[0]) {

                    case 'session_start':
                        $this->connexion->getRoster();
                        $this->connexion->presence('');
                        break;

                    case 'presence':
                        if ($pass == 0) {
                            $firstContact = $payloads[0][1]['from'];
                        }
                        $currentContact = $payloads[0][1]['from'];
                        if (!in_array($currentContact, $this->contactList)) {
                            $this->contactList[] = $currentContact;
                            $totalRound++;
                        }
                        if ($currentContact == $firstContact) {
                            $end_loop++;
                        }
                        $pass++;
                        break;
                }
            }
        }
    }

    /*
     * This function add the summoner on the friend list
     */

    public function subscribe($SummonerID) {
        $this->connexion->subscribe($SummonerID);
    }

    /*
     * This function check if the $SumId given is a friend of the account used 
     * to instant this class 
     */

    public function isMyFriend($SummonerID) {
        if (in_array("sum" . $SummonerID . "@pvp.net", $this->contactList)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * This function send a message to the given summoner
     */

    public function sendMessage($SummonerID, $message) {
        $this->connexion->message('sum' . $SummonerID . '@pvp.net', $message);
    }

    /*
     * this function recreate the xmpp connexion and reconnect it
     */

    public function reconnect() {
        $this->connexion = new XMPPHP_XMPP('chat.' . $this->region . '.lol.riotgames.com', 5223, $this->userName, 'AIR_' . $this->password, 'xiff', 'pvp.net', $printlog = true, $loglevel = XMPPHP_Log::LEVEL_ERROR);
        $this->connexion->connect();
    }

    /*
     * this function disconnect this class from the xmpp server
     */

    public function disconnect() {
        $this->connexion->disconnect();
    }

    //--------------------------------------------------------------------------
    //                         Getters & Setters                            
    //--------------------------------------------------------------------------

    public function getUserName() {
        return $this->userName;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getRegion() {
        return $this->region;
    }

    public function getContactList() {
        return $this->contactList;
    }

    public function getConnexion() {
        return $this->connexion;
    }

    public function setUserName($userName) {
        $this->userName = $userName;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setRegion($region) {
        $this->region = $region;
    }

    public function setConnexion($connexion) {
        $this->connexion = $connexion;
    }

}
