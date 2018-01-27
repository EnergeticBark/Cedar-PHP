<?php
require_once('lib/htm.php');

if (empty($_SESSION['signed_in'])) {
	exit();
}

if (!(isset($_POST['postId']) && isset($_POST['nahType']))) {
	exit();
}

if (!((checkPostExists($_POST['postId']) && $_POST['nahType'] == 0) || (checkReplyExists($_POST['postId']) && $_POST['nahType'] == 1))) {
	exit();
}
            
if ($_POST['nahType'] == 0) {
	if (!checkPostCreator($_POST['postId'], $_SESSION['user_id'])) {
		$nah = $dbc->prepare('INSERT INTO nahs (nah_post, type, nah_by) VALUES (?, ?, ?)');
		$nah->bind_param('iii', $_POST['postId'], $_POST['nahType'], $_SESSION['user_id']);
		$nah->execute();

		$check_yeah = $dbc->prepare('SELECT * FROM yeahs WHERE yeah_post = ? AND type = "post" AND yeah_by = ?');
		$check_yeah->bind_param('ii', $_POST['postId'], $_SESSION['user_id']);
		$check_yeah->execute();
		$yeah_result = $check_yeah->get_result();

		if (!$yeah_result->num_rows == 0) {
			$delete_yeah = $dbc->prepare('DELETE FROM yeahs WHERE yeah_post = ? AND type = "post" AND yeah_by = ?');
			$delete_yeah->bind_param('ii', $_POST['postId'], $_SESSION['user_id']);
			$delete_yeah->execute();
		}

		echo 'success';
	}
} else {
	if (!checkReplyCreator($_POST['postId'], $_SESSION['user_id'])) {
		$nah = $dbc->prepare('INSERT INTO nahs (nah_post, type, nah_by) VALUES (?, ?, ?)');
		$nah->bind_param('iii', $_POST['postId'], $_POST['nahType'], $_SESSION['user_id']);
		$nah->execute();

		$check_yeah = $dbc->prepare('SELECT * FROM yeahs WHERE yeah_post = ? AND type = "reply" AND yeah_by = ?');
		$check_yeah->bind_param('ii', $_POST['postId'], $_SESSION['user_id']);
		$check_yeah->execute();
		$yeah_result = $check_yeah->get_result();

		if (!$yeah_result->num_rows == 0) {
			$delete_yeah = $dbc->prepare('DELETE FROM yeahs WHERE yeah_post = ? AND type = "reply" AND yeah_by = ?');
			$delete_yeah->bind_param('ii', $_POST['postId'], $_SESSION['user_id']);
			$delete_yeah->execute();
		}

		echo 'success';
	}
}