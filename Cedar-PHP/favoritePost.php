<?php
require_once('lib/htm.php');
	
if (!empty($_SESSION['signed_in']) && isset($id) && checkPostExists($id) && checkPostCreator($id, $_SESSION['user_id'])){
	$yeah = $dbc->prepare('UPDATE profiles SET fav_post = ? WHERE user_id = ?');
    $yeah->bind_param('ii', $id, $_SESSION['user_id']);
    $yeah->execute();
 
	echo 'success';
} else {
    $yeah = $dbc->prepare('UPDATE profiles SET fav_post = NULL WHERE user_id = ?');
    $yeah->bind_param('i', $_SESSION['user_id']);
    $yeah->execute();

    echo '{"success":1}';
}