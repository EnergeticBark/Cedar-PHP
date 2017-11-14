<?php
require_once('lib/htm.php');

if (empty($_SESSION['signed_in'])) {
	return;
}

$get_user = $dbc->prepare('SELECT user_face FROM users WHERE user_id = ?');
$get_user->bind_param('i', $_SESSION['user_id']);
$get_user->execute();
$user_result = $get_user->get_result();
$user = $user_result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {

	echo '<form id="post-form" method="post" action="/posts/'.$post['id'].'/replies" enctype="multipart/form-data">
	  <div class="post-count-container">
	    <div class="textarea-feedback" style="float:left;">
	      <font color="#646464" style="font-size: 13px; padding: 0 3px 0 7px;">800</font> Characters Remaining
	    </div>
	  </div>';

	if (!strpos($user['user_face'], "imgur") && !strpos($user['user_face'], "cloudinary")) {
		echo '<div class="feeling-selector js-feeling-selector test-feeling-selector"><label class="symbol feeling-button feeling-button-normal checked"><input type="radio" name="feeling_id" value="0" checked=""><span class="symbol-label">normal</span></label><label class="symbol feeling-button feeling-button-happy"><input type="radio" name="feeling_id" value="1"><span class="symbol-label">happy</span></label><label class="symbol feeling-button feeling-button-like"><input type="radio" name="feeling_id" value="2"><span class="symbol-label">like</span></label><label class="symbol feeling-button feeling-button-surprised"><input type="radio" name="feeling_id" value="3"><span class="symbol-label">surprised</span></label><label class="symbol feeling-button feeling-button-frustrated"><input type="radio" name="feeling_id" value="4"><span class="symbol-label">frustrated</span></label><label class="symbol feeling-button feeling-button-puzzled"><input type="radio" name="feeling_id" value="5"><span class="symbol-label">puzzled</span></label></div>';
	}

	echo '<div class="textarea-container"><textarea name="text_data" class="textarea-text textarea" maxlength="800" placeholder="Add a comment here."></textarea></div>Image upload: <input type="file" name="image" accept="image/*"><div class="form-buttons"><input type="submit" name="submit" class="black-button post-button disabled" value="Send" disabled=""></div></form>';
} else {
	$errors = array();
	$image = NULL;

	if (empty($_POST['text_data'])) {
		$errors[] = 'Post text cannot be empty.';
	} elseif (mb_strlen($_POST['text_data']) > 800) { 
		$errors[] = 'Replies cannot be longer than 800 characters.';
	} else {
		$text = $_POST['text_data'];
	}

	if(empty($_POST['feeling_id']) || strval($_POST['feeling_id']) >= 6) {
		$_POST['feeling_id'] = 0;
	} 

	$img=$_FILES['image'];

	if(!empty($img['name'])){
		$filename = $img['tmp_name'];

		//imageUpload() returns 1 if it fails and the image URL if successful
		$image = uploadImage($filename);
		if ($image == 1) {
			$errors[] = 'Image upload failed';
		}
	}

	if (empty($errors)) {
		$text = htmlspecialchars($text, ENT_QUOTES);
		$reply_id = mt_rand(0, 99999999);

		$post_reply = $dbc->prepare('INSERT INTO replies (reply_id, reply_post, reply_by_id, feeling_id, text, reply_image) VALUES (?, ?, ?, ?, ?, ?)');
		$post_reply->bind_param('iiiiss', $reply_id, $id, $_SESSION['user_id'], $_POST['feeling_id'], $text, $image);
		$post_reply->execute();

		$search_post = $dbc->prepare('SELECT * FROM posts WHERE id = ?');
		$search_post->bind_param('i', $id);
		$search_post->execute();
		$post_result = $search_post->get_result();
		$post = $post_result->fetch_assoc();

		if ($_SESSION['user_id'] == $post['post_by_id']) {
			$notif_getcomments = $dbc->prepare('SELECT reply_by_id FROM replies WHERE reply_post = ? AND reply_by_id != ? AND deleted = 0 GROUP BY reply_by_id');
			$notif_getcomments->bind_param('ii', $id, $_SESSION['user_id']);
			$notif_getcomments->execute();
			$result_notif_getcomments = $notif_getcomments->get_result();

			while ($notif_comments = mysqli_fetch_assoc($result_notif_getcomments)) {
				notify($notif_comments['reply_by_id'], 3, $id);
			}
		} else {
			notify($post['post_by_id'], 2, $id);
		}

		$search_reply = $dbc->prepare('SELECT * FROM replies INNER JOIN users ON user_id = reply_by_id WHERE reply_id = ?');
		$search_reply->bind_param('i', $reply_id);
		$search_reply->execute();
		$reply_result = $search_reply->get_result();
		$reply = $reply_result->fetch_assoc();
		
		echo '<li class="post'. ($reply['reply_by_id'] == $post['post_by_id'] ? ' my' : '') .' trigger" data-href="/replies/'.$reply['reply_id'].'" style="display: none;">';
		printReply($reply);

	} else {
		echo '<script type="text/javascript">alert("'. $errors[0] .'");</script>';
	}
}