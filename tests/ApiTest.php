<?php

include_once getenv('APP_DUOQ_ROOT_PATH') . '/model/MysqlConnect.php';
include '../model/api/LolApi.php';
LolApi::init($db);




echo "##########################################################################" . PHP_EOL;
echo "                  getSummonerById" . PHP_EOL;
echo "##########################################################################" . PHP_EOL;
print_r(LolApi::getSummonerById("19469066"));
echo PHP_EOL;

echo "##########################################################################" . PHP_EOL;
echo "                  getSummonerIdByName" . PHP_EOL;
echo "##########################################################################" . PHP_EOL;
print_r(LolApi::getSummonerIdByName("CýpressXt"));
echo PHP_EOL;

echo "##########################################################################" . PHP_EOL;
echo "                  getLeagueInfo" . PHP_EOL;
echo "##########################################################################" . PHP_EOL;
print_r(LolApi::getLeagueInfo("19469066"));
echo PHP_EOL;

echo "##########################################################################" . PHP_EOL;
echo "                  getDivisionId" . PHP_EOL;
echo "##########################################################################" . PHP_EOL;
print_r(LolApi::getDivisionId("V"));
echo PHP_EOL;

echo "##########################################################################" . PHP_EOL;
echo "                  getTierId" . PHP_EOL;
echo "##########################################################################" . PHP_EOL;
print_r(LolApi::getTierId("GOLD"));
echo PHP_EOL;

echo "##########################################################################" . PHP_EOL;
echo "                  getChampions" . PHP_EOL;
echo "##########################################################################" . PHP_EOL;
echo count(LolApi::getChampions());
echo PHP_EOL;

echo "##########################################################################" . PHP_EOL;
echo "                  getChampionById" . PHP_EOL;
echo "##########################################################################" . PHP_EOL;
print_r(LolApi::getChampionById('103'));
echo PHP_EOL;

echo "##########################################################################" . PHP_EOL;
echo "                  getRecentRankedGamesBySummonerId" . PHP_EOL;
echo "##########################################################################" . PHP_EOL;
echo count(LolApi::getRecentRankedGamesBySummonerId("19469066"));
echo PHP_EOL;

echo "##########################################################################" . PHP_EOL;
echo "                  getRecentRankedGamesBySummonerId" . PHP_EOL;
echo "##########################################################################" . PHP_EOL;
print_r(LolApi::getDuoRankedGames("Gardok4", "CýpressXt"));
echo PHP_EOL;