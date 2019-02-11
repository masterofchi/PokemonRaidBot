<?php
// Write to log.
debug_log('vote_team()');

// For debug.
//debug_log($update);
//debug_log($data);

// Update team in users table.
$team = $data['arg'];
$user_id = $update['callback_query']['from']['id'];
my_query(
    "
    UPDATE    users
    SET    team = '{$team}'
    WHERE   user_id = {$user_id}
    "
);

$keys = [[
    [
        'text'          => TEAM_B,
        'callback_data' => $user_id.':vote_team2:mystic'
    ],
    [
        'text'          => TEAM_R,
        'callback_data' => $user_id.':vote_team2:valor'
    ],
    [
        'text'          => TEAM_Y,
        'callback_data' => $user_id.':vote_team2:instinct'
    ],
    [
        'text'          => 'Lvl +',
        'callback_data' => $user_id.':vote_level2:up'
    ],
    [
        'text'          => 'Lvl -',
        'callback_data' => $user_id.':vote_level2:down'
    ],
    [
        'text'          => 'Fertig',
        'callback_data' => ':exit:1'
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
switch($team){
    case 'mystic':
        $team = TEAM_B;
        break;
    case 'valor':
        $team = TEAM_R;
        break;
    case 'instinct':
        $team = TEAM_Y;
        break;
}

// Set message.
$msg = '<b>' . 'Bitte konfiguriere dein Team('. $team .') und Level(' . $level . ')</b>';

// Change message string.
$callback_msg = getTranslation('vote_updated');
// Answer the callback.
answerCallbackQuery($update['callback_query']['id'], $callback_msg, true);
// Edit the message.
edit_message($update, $msg, $keys, ['disable_web_page_preview' => 'true'], true);

exit();
