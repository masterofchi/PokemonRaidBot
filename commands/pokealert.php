<?php
// Write to log.
// debug_log('POKEALERT()');

// For debug.
// debug_log($update);
// debug_log($data);

/**
 * Mimic inline message to create pokealert from external notifier.
 */
$tz = TIMEZONE;

// Get data from message text. (remove: "/pokealert ")
$alert_data = trim(substr($update['message']['text'], 10));

// Create data array (max. 9)
$data = explode(',', $alert_data);

$user_id = $update['message']['chat']['id'];

/**
 * Info:
 * [0] = $pokemonId
 * [1] = $iv
 * [2] = $deadline
 * [3] = $lat
 * [4] = $lon
 * [5] = $pokemonName
 */

// Invalid data received.
if (count($data) < 4) {
    send_message($update['message']['chat']['id'], 'Invalid input - Parameter mismatch', []);
    exit();
}

$pokemonId = $data[0];
$iv = $data[1];
$deadline = $data[2];
$lat = $data[3];
$lon = $data[4];
$pokemonName = $data[5];
try {
    $rs = my_query("
        SELECT    COUNT(*)
        FROM      pokemon_sent
          WHERE   user_id = '{$user_id}' AND
                  pokemon_id = '{$pokemonId}' AND
                  expire_time = '{$deadline}'
         ");

    $row = $rs->fetch_row();

    // Gym already in database or new
    if (empty($row['0'])) {
        // Set text.
        $photo_url = 'http://pogomap-aux.de/buidl/pogoassets/id_' . $pokemonId . '.png';
        $text = 'Wildes Pokemon gesichtet: <b>' . $pokemonName . '</b>' . CR;
        $text .= 'IV: ' . $iv . CR;
        $text .= 'Verschwindet um: ' . $deadline . CR;
        $text .= '<a href="https://maps.google.com/?daddr=' . $lat . ',' . $lon . '">Position</a>';
        $keys = [];
        // Send the message.
        send_photo($user_id, $photo_url, $text, $keys, [
            'disable_web_page_preview' => 'true'
        ]);

        $query = '
            INSERT INTO pokemon_sent (user_id, pokemon_id, expire_time)
            VALUES (:user_id, :pokemon_id, :expire_time)
        ';

        $statement = $dbh->prepare($query);
        $statement->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $statement->bindValue(':pokemon_id', $pokemonId, PDO::PARAM_STR);
        $statement->bindValue(':expire_time', $deadline, PDO::PARAM_STR);
        $statement->execute();
    }
} catch (PDOException $exception) {

    error_log($exception->getMessage());
    $dbh = null;
    exit();
}

?>

