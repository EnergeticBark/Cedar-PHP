<?php
require_once('lib/htm.php');

if (!empty($_SESSION['signed_in'])) {
	if ($_SESSION['user_id'] != $_POST['userId']){
		if(isset($_POST['userId']) && isset($_POST['followType'])) {

			if($_POST['followType'] == 'follow'){
				$yeah = $dbc->prepare('INSERT INTO follows (follow_by, follow_to) VALUES (?, ?)');
				$yeah->bind_param('ii', $_SESSION['user_id'], $_POST['userId']);
				$yeah->execute();

				notify($_POST['userId'], 4, NULL);

				echo 'success';

			} else {

				$yeah = $dbc->prepare('DELETE FROM follows WHERE follow_by = ? AND follow_to = ?');
				$yeah->bind_param('ii', $_SESSION['user_id'], $_POST['userId']);
				$yeah->execute();
				echo 'success';
			}
		}
	}
}