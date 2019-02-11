<?php
// Write to log.
debug_log('USER()');

// For debug.
//debug_log($update);
//debug_log($data);
$user_id = $update['message']['from']['id'];

$keys = [[
    [
        'text'          => '+ ' . TEAM_B,
        'callback_data' => $user_id.':vote_team2:mystic'
    ],
    [
        'text'          => '+ ' . TEAM_R,
        'callback_data' => $user_id.':vote_team2:valor'
    ],
    [
        'text'          => '+ ' . TEAM_Y,
        'callback_data' => $user_id.':vote_team2:instinct'
    ],
    [
        'text'          => 'Lvl +',
        'callback_data' => $user_id.':vote_level:up'
    ],
    [
        'text'          => 'Lvl -',
        'callback_data' => $user_id.'vote_level:down'
    ],
    [
        'text'          => 'Fertig',
        'callback_data' => 'exit:1'
    ]
    
]];

try {
    
    // Update gym name in raid table.
    $query = '
        SELECT level,team
        FROM users
        WHERE
            user_id LIKE :user_id
        LIMIT 1
    ';
    $statement = $dbh->prepare( $query );
    $statement->bindValue(':user_id', $user_id, PDO::PARAM_STR);
    $statement->execute();
    while ($row = $statement->fetch()) {
        
        $level = $row['level'];
        $team = $row['team'];
    }
}
catch (PDOException $exception) {
    
    error_log($exception->getMessage());
    $dbh = null;
    exit;
}

// Set message.
$msg = '<b>' . 'Bitte konfiguriere dein Team('. $team .') und Level(' . $level . ')</b>';

// Send message.
send_message($update['message']['chat']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);
?>