<?php
// Write to log.
debug_log('edit_raidlevel()');

// For debug.
//debug_log($update);
//debug_log($data);

// Get gym data via ID in arg
$city = $data['arg'];

// Back key id, action and arg
$back_id = 0;
$back_action = 'gym_by_location';
$back_arg = $data['id'];
$gym_id = $back_arg;

$rs = my_query(
    "
    UPDATE   gyms
    SET      address = '$city'
    WHERE    id = '$gym_id';
    "
);

$msg = 'Arena wurde angelegt. Bitte Namen eintragen mit /gym [ARENANAME]';
$keys = [];

// Answer callback.
answerCallbackQuery($update['callback_query']['id'], $msg);

// Edit the message.
edit_message($update, $msg, $keys);

// Exit.
exit();

