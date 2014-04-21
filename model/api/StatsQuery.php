<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of StatsQuery
 *
 * @author Naytskai
 */
class StatsQuery
{
    
    static function getDuoResults($idDuo)
    {
        $q = self::$_db->prepare('SELECT * FROM matches WHERE idMatch = :idMatch');
       
        $q->bindValue(':idMatch', $matchId, PDO::PARAM_INT);
        
        $q->execute();
       
        $result = $q->fetch(PDO::FETCH_ASSOC);
    }
    
}
