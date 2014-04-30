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
    
    private static $_db;
    
    static function init($db)
    {
        self::$_db = $db;
    }
    
    
    static function getDuoResults($idDuo)
    {
        $q = self::$_db->prepare('SELECT * FROM results
            INNER JOIN matches ON pkMatch = fkMatch
            INNER JOIN duo ON pkDuo = fkDuo
            INNER JOIN summoners ON pkSummoner = fkSummoner
            INNER JOIN champions ON pkChampion = fkChampion
            INNER JOIN tiers ON pkTier = fkTier
            WHERE fkDuo = :fkDuo');
       
        $q->bindValue(':fkDuo', $idDuo, PDO::PARAM_INT);
        
        $q->execute();
       
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    
}
