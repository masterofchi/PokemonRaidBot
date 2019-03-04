#!/usr/bin/env php
<?php
require_once(__DIR__ . '/../config.php');
$dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
try {
    $query = "select user_id, pokemon_id, deadline, lat, lon, iv, pokemon_name from pokeradar";
    $statement = $dbh->prepare($query);
    $statement->execute();

    while ($row = $statement->fetch()) {
        $chatId = $row['user_id']; // TG-CHATID
        $pokemonId = $row['pokemon_id'];
        $pokemonName = $row['pokemon_name'];
        $iv = $row['iv'];
        $deadline = $row['deadline'];
        $lat = $row['lat'];
        $lon = $row['lon'];

        $message = array(
            'message' => array(
                'chat' => array(
                    'id' => $chatId,
                    'type' => 'private'
                ),
                'text' => '/pokealert ' . $pokemonId .',' .$iv .',' . $deadline . ',' . $lat . ',' . $lon . ','. $pokemonName
            )
        );
        // Create json string.
        $postFields = json_encode($message, JSON_UNESCAPED_SLASHES);
        echo $postFields;
        $result = curl('https://domain.de/index.php?apikey=XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', $postFields);
            
    }
} catch (PDOException $exception) {

    error_log($exception->getMessage());
    $dbh = null;
    exit();
} finally{
    $dbh = null;
}


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