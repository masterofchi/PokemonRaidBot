#!/usr/bin/env php
<?php
$pokedex = require 'pokedex_autoraid.php';

$user = 'user';
$passwd = 'password';
$pdo = new PDO('mysql:host=XXX.XXX.XXX.XXX;dbname=dbname;charset=utf8', $user, $passwd);
$pdo->exec("SET CHARACTER SET utf8");
 
$statement = $pdo->prepare("SELECT forts.name, fort_sightings.team, forts.lat, forts.lon, raids.level, raids.time_spawn, raids.time_battle, raids.time_end, raids.pokemon_id FROM raids JOIN forts ON raids.fort_id = forts.id JOIN fort_sightings ON raids.fort_id=fort_sightings.fort_id WHERE raids.time_spawn > unix_timestamp() - 10800 AND
    forts.name IS NOT NULL ORDER BY raids.time_spawn");

$statement->execute();
while ($row = $statement->fetch()) {

    $chatName = ''; // TG-CHATNAME
    $chatId = '-'; // TG-CHATID

    $pokemonId = $row['pokemon_id'];
    if (! $pokemonId or $pokemonId == '0') {
        $pokemonId = '999' . $row['level'];
    } else
        $pokemonId = $pokemonId;

    $timeEnd = $row['time_end'];
    $timeStart = $row['time_battle'];
    $timeTillEnd = intval(($timeEnd - time()) / 60);
    $raidDuration = intval(($timeEnd - $timeStart) / 60);
    if ($timeTillEnd > $raidDuration) {
        $timeTillEnd = $raidDuration;
    }
    // Get minutes until egg hatches (when raid begins).
    $timeToHatch = intval(($timeStart - time()) / 60);
    if ($timeToHatch < 0) {
        $timeToHatch = 0;
    }
    if ($timeTillEnd <= 0)
        continue;
    $team = '';
    // Team id found.
    if (! empty($row['team'])) {
        // Switch by team id.
        switch ($row['team']) {
            case (1):
                $team = 'mystic';
                break;
            case (2):
                $team = 'valor';
                break;
            case (3):
                $team = 'instinct';
                break;
            default:
                $team = '';
        }
        // Team id is missing.
    }

    $message = array(
        'message' => array(
            'chat' => array(
                'id' => $chatId,
                'type' => 'channel'
            ),
            'from' => array(
                'id' => '4711',
                'last_name' => 'Pine',
                'first_name' => 'Prof.'
            ),
            'text' => '/raid ' . $pokemonId . ',' . $timeTillEnd . ',' . $row['name'] . ',' . $team . ',' . $timeToHatch
        )
    );
    // Create json string.
    $postFields = json_encode($message, JSON_UNESCAPED_SLASHES);
    echo $postFields;
    // Send data by curl.
    $result = curl('https://domain.de/index.php?apikey=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', $postFields);
}
$pdo = null;

/**
 * Send data by curl.
 *
 * @param $url string
 * @param $postFields string
 * @return string
 */
function curl($url, $postFields)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json"
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_HEADER, 0); // Don't return headers.
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:49.0) Gecko/20100101 Firefox/49.0');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

?>