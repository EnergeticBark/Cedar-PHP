<?php
require_once('lib/htm.php');

if (!empty($_SESSION['signed_in'])) {

	if (isset($_POST['postId']) && isset($_POST['yeahType'])) {

		if (!checkPostCreator($_POST['postId'], $_SESSION['user_id'])) {
			$yeah = $dbc->prepare('DELETE FROM yeahs WHERE yeah_post = ? AND type = ? AND yeah_by = ?');
			$yeah->bind_param('isi', $_POST['postId'], $_POST['yeahType'], $_SESSION['user_id']);
			$yeah->execute();
			echo 'success';
		}
	}
}
