<?php
require_once('lib/htm.php');

if (!empty($_SESSION['signed_in'])) {

	if (isset($_POST['titleId']) && isset($_POST['favType'])){

		if ($_POST['favType'] == 'addFav'){
			$check_favorite = $dbc->prepare('SELECT * FROM favorite_titles WHERE user_id = ? AND title_id = ? LIMIT 1');
			$check_favorite->bind_param('ii', $_SESSION['user_id'], $_POST['titleId']);
			$check_favorite->execute();
			$favorite_result = $check_favorite->get_result();

			if ($favorite_result->num_rows == 0){
				$favorite = $dbc->prepare('INSERT INTO favorite_titles (user_id, title_id) VALUES (?, ?)');
				$favorite->bind_param('ii', $_SESSION['user_id'], $_POST['titleId']);
				$favorite->execute();
				echo 'success';
			} else {
				echo 'the community was already favorited lmao';
			}
		} elseif ($_POST['favType'] == 'removeFav'){
			$check_favorite = $dbc->prepare('SELECT * FROM favorite_titles WHERE user_id = ? AND title_id = ? LIMIT 1');
			$check_favorite->bind_param('ii', $_SESSION['user_id'], $_POST['titleId']);
			$check_favorite->execute();
			$favorite_result = $check_favorite->get_result();

			if (!$favorite_result->num_rows == 0){
				$favorite = $dbc->prepare('DELETE FROM favorite_titles WHERE user_id = ? AND title_id = ?');
				$favorite->bind_param('ii', $_SESSION['user_id'], $_POST['titleId']);
				$favorite->execute();
				echo 'success';
			} else {
				echo 'the community was never even favorited in the first place lmao';
			}
		}
	}
}