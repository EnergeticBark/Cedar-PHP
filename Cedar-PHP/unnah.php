<?php
require_once('lib/htm.php');

if (empty($_SESSION['signed_in']) || checkPostCreator($_POST['postId'], $_SESSION['user_id']) || !(isset($_POST['postId']) && isset($_POST['nahType']))) {
	exit();
}

$nah = $dbc->prepare('DELETE FROM nahs WHERE nah_post = ? AND type = ? AND nah_by = ?');
$nah->bind_param('iii', $_POST['postId'], $_POST['nahType'], $_SESSION['user_id']);
$nah->execute();

echo 'success';