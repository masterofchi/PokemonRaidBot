<?php
// Write to log.
debug_log('vote_level()');

// For debug.
//debug_log($update);
//debug_log($data);

// Get action.
$action = $data['arg'];
$user_id = $data['id'];

// Up-vote.
if ($action == 'up') {
    // Increase users level.
    my_query(
        "
        UPDATE    users
        SET       level = IF(level = 0, 30, level+1)
          WHERE   user_id = {$user_id}
            AND   level < 40
        "
    );
}

// Down-vote.
if ($action == 'down') {
    // Decrease users level.
    my_query(
        "
        UPDATE    users
        SET       level = level-1
          WHERE   user_id = {$user_id}
            AND   level > 5
        "
    );
}
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

// Send vote response.
edit_message($update, $msg, $keys);

exit();
