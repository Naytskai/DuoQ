<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProxyApi
 *
 * @author Clément Hampaï
 */
class ProxyApi {

    private $_proxyArray;
    private $_proxyToken;
    private $_proxyPass;

    function __construct() {
        $this->_proxyArray = array("183.207.228.6:61616",
            "5.135.109.193:3128",
            "78.154.170.179:3129",
            "94.23.225.162:3128",
            "109.99.150.2:8080",
            "94.23.244.96:3128",
            "109.86.199.193:8080", 
            "95.140.225.30:2080",
            "2.95.43.207:3128", 
            "94.23.242.128:3128",
            "95.140.225.30:2080",
            "81.198.230.182:8080",
            "217.20.82.7:1080");
        $this->_proxyToken = 0;
        $this->_proxyPass = 0;
    }

    public function getProxy($delay) {
        $url = 'http://developer.riotgames.com/status';
        $proxy = $this->_proxyArray[$this->_proxyToken];
        $currentToken = $this->_proxyToken;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $delay);
        $curl_scraped_page = curl_exec($ch);
        $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($this->_proxyToken >= count($this->_proxyArray) - 1) {
            $this->_proxyToken = 0;
        } else {
            $this->_proxyToken++;
        }
        if ($info == 200) {
            return $this->_proxyArray[$currentToken]." ".$info;
        } else {
            $this->_proxyPass++;

            if ($this->_proxyPass == 2) {
                exit();
            } else {
                //echo "DEAD !!!! ".$this->_proxyArray[$currentToken]." ".$info."<br>";
                return $this->getProxy($delay);
            }
        }
        curl_close($ch);
    }

    public function get_proxyToken() {
        return $this->_proxyToken;
    }

    public function get_proxyListSize() {
        return count($this->_proxyArray);
    }

}
