<?php
require_once('lib/connect.php');
if (!empty($_SESSION['signed_in'])) {
    $get_notifs = $dbc->prepare('SELECT count(notif_id) FROM notifs WHERE notif_to = ? AND merged IS NULL AND notif_read = 0 ORDER BY notif_date DESC LIMIT 25');
    $get_notifs->bind_param('i', $_SESSION['user_id']);
    $get_notifs->execute();
    $notifs_result = $get_notifs->get_result();
    $notif = $notifs_result->fetch_assoc();

    $update_online = $dbc->prepare('UPDATE profiles SET last_online = NOW() WHERE user_id = ?');
    $update_online->bind_param('i', $_SESSION['user_id']);
    $update_online->execute();

    echo json_encode(array('success' => 1, 'notifs' => array('unread_count' => $notif['count(notif_id)'])), JSON_FORCE_OBJECT);
}
