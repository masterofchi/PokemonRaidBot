<?php
// Write to log.
debug_log('POKERADAR()');

// For debug.
// debug_log($update);
// debug_log($data);

// Get gym name.
$input = trim(substr($update['message']['text'], 10));
list ($pokedex_id, $min_iv) = explode(',', $input);
$user_id = $update['message']['chat']['id'];
// Write to log.
debug_log('Setting pokeradar for user '. $user_id .' for ' . $pokedex_id . ' with IV ' . $min_iv);

// Private chat type.
if ($update['message']['chat']['type'] == 'private') {

    try {

        global $db;

        // Build query to check if gym is already in database or not
        $rs = my_query("
        SELECT    COUNT(*)
        FROM      user_pokemon
          WHERE   user_id = '{$user_id}' AND
                  pokemon_id = '{$pokedex_id}'
         ");

        $row = $rs->fetch_row();

        // Gym already in database or new
        if (empty($row['0'])) {
            // insert gym in table.
            debug_log('pokeradar-entry not found in database! Inserting now.');
            $query = '
            INSERT INTO user_pokemon (user_id, pokemon_id, min_iv)
            VALUES (:user_id, :pokemon_id, :min_iv)
        ';
            $message = 'Neuer Eintrag im Pokeradar wurde angelegt';
        } else {
            // Update gyms table to reflect gym changes.
            debug_log('pokeradar-entry found in database gym list! Updating now.');
            $query = '
                UPDATE        user_pokemon
                SET           min_iv = :min_iv
                WHERE      user_id = :user_id AND
                           pokemon_id = :pokemon_id
            ';
            $message = 'Eintrag im Pokeradar aktualisiert';
        }

        $statement = $dbh->prepare($query);
        $statement->bindValue(':user_id', $user_id, PDO::PARAM_STR);
        $statement->bindValue(':pokemon_id', $pokedex_id, PDO::PARAM_STR);
        $statement->bindValue(':min_iv', $min_iv, PDO::PARAM_STR);
        $statement->execute();
    } catch (PDOException $exception) {

        error_log($exception->getMessage());
        $dbh = null;
        exit();
    }

    // Send the message.
    sendMessage($user_id, $message);
}
?>
        