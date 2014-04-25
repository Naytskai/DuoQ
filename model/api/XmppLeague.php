<?php

class xmppLeague {

    public static function addFriend($summonerID, $randomMessage) {
        include 'XmppConnect.php';
        include("XMPPHP/XMPP.php");
        $conn = new XMPPHP_XMPP('chat.' . $regionXmpp . '.lol.riotgames.com', 5223, $userNameXmpp, 'AIR_' . $passwordXmpp, 'xiff', 'pvp.net', $printlog = true, $loglevel = XMPPHP_Log::LEVEL_ERROR);
        $conn->autoSubscribe();

        $conn->connect();
        $breakAll = false;
        while (1) {
            $payloads = $conn->processUntil(array('session_start', 'roster_received', ''));
            foreach ($payloads as $event) {
                $pl = $event[1];
                if ($event[0] == 'session_start') {
                    /* If getRoster() is here, the output is empty Array() */
                    $conn->presence($status = "DuoQTerminal");
                    $conn->subscribe("sum" . $summonerID . "@pvp.net");
                    $conn->getRoster();
                }
                if ($event[0] == 'roster_received') {
                    $conn->getRoster();
                    $contactList = $conn->roster->getRoster();
                    if ($contactList["sum" . $summonerID . "@pvp.net"]["contact"]["subscription"] == both) {
                        $breakAll = true;
                    }
                }
            }
            if ($breakAll) {
                break;
            }
        }
        $conn->message("sum" . $summonerID . "@pvp.net", $randomMessage);
        $conn->disconnect();
        if ($breakAll) {
            return true;
        } else {
            return false;
        }
    }

}
?>