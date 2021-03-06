<?php
// Write to log.
debug_log('ADDGYM()');

// For debug.
// debug_log($update);
// debug_log($data);

// Get gym name.
$input = trim(substr($update['message']['text'], 7));
list ($gym_name, $lat, $lon, $address) = explode(';', $input);
$lat = str_replace(",", ".", $lat);
$lon = str_replace(",", ".", $lon);
// Write to log.
debug_log('Setting gym name to ' . $gym_name);

// Private chat type.
if ($update['message']['chat']['type'] == 'private') {

    try {

        global $db;

        // Build query to check if gym is already in database or not
        $rs = my_query("
        SELECT    COUNT(*)
        FROM      gyms
          WHERE   gym_name = '{$gym_name}'
         ");

        $row = $rs->fetch_row();

        // Gym already in database or new
        if (empty($row['0'])) {
            // insert gym in table.
            debug_log('Gym not found in database gym list! Inserting gym "' . $gym_name . '" now.');
            $query = '
            INSERT INTO gyms (gym_name, lat, lon, address, show_gym)
            VALUES (:gym_name, :lat, :lon, :address, 1)
        ';
            $message = getTranslation('gym_added');
        } else {
            // Update gyms table to reflect gym changes.
            debug_log('Gym found in database gym list! Updating gym "' . $gym_name . '" now.');
            $query = '
                UPDATE        gyms
                SET           lat = :lat,
                              lon = :lon,
                              address = :address
                WHERE      gym_name = :gym_name
            ';
            $message = getTranslation('gym_updated');
        }

        $statement = $dbh->prepare($query);
        $statement->bindValue(':gym_name', $gym_name, PDO::PARAM_STR);
        $statement->bindValue(':lat', $lat, PDO::PARAM_STR);
        $statement->bindValue(':lon', $lon, PDO::PARAM_STR);
        $statement->bindValue(':address', $address, PDO::PARAM_STR);
        $statement->execute();
    } catch (PDOException $exception) {

        error_log($exception->getMessage());
        $dbh = null;
        exit();
    }

    // Send the message.
    sendMessage($update['message']['chat']['id'], $message);
}
?>
        