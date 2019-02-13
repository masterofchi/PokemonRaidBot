<?php 
// Shared overview
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
    ]
]];

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

// Set callback message string.
$msg_callback = getTranslation('successfully_shared');

// Answer the callback.
answerCallbackQuery($update['callback_query']['id'], $msg_callback);

// Edit the message, but disable the web preview!
edit_message($update, $msg_callback, [], ['disable_web_page_preview' => 'true']);

$chats = explode(',', SHARE_CHATS);
foreach($chats as $chat) {
    // Send the message, but disable the web preview!
    send_message($chat, $msg, $keys, ['disable_web_page_preview' => 'true']);
}
exit();
