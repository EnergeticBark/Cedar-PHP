<?php
require_once('lib/htm.php');

if (!empty($_SESSION['signed_in'])) {

	$get_user = $dbc->prepare('SELECT * FROM users WHERE user_id = ? LIMIT 1');
	$get_user->bind_param('i', $_SESSION['user_id']);
	$get_user->execute();
	$user_result = $get_user->get_result();
	$user = $user_result->fetch_assoc();

	if (isset($_POST['title_id'])){
		$get_title = $dbc->prepare('SELECT * FROM titles WHERE title_id = ?');
		$get_title->bind_param('i', $_POST['title_id']);
		$get_title->execute();
		$title_result = $get_title->get_result();
		if($title_result->num_rows == 0){ exit("fuck off"); }
		$title = $title_result->fetch_array();
	}

	if(($title['perm'] == 1 && $user['user_level'] > 1) || $title['perm'] == NULL) {

		if($_SERVER['REQUEST_METHOD'] != 'POST'){

			echo '<form id="post-form" method="post" action="/postText.php" enctype="multipart/form-data"><div class="post-count-container">
			<input type="hidden" name="title_id" value="'.$title['title_id'].'">
			<div class="textarea-feedback" style="float:left;"><font color="#646464" style="font-size: 13px; padding: 0 3px 0 7px;">800</font> Characters Remaining</div></div>';

			if(!strpos($user['user_face'], "imgur") && !strpos($user['user_face'], "cloudinary")) { 
				echo '<div class="feeling-selector js-feeling-selector test-feeling-selector"><label class="symbol feeling-button feeling-button-normal checked"><input type="radio" name="feeling_id" value="0" checked=""><span class="symbol-label">normal</span></label><label class="symbol feeling-button feeling-button-happy"><input type="radio" name="feeling_id" value="1"><span class="symbol-label">happy</span></label><label class="symbol feeling-button feeling-button-like"><input type="radio" name="feeling_id" value="2"><span class="symbol-label">like</span></label><label class="symbol feeling-button feeling-button-surprised"><input type="radio" name="feeling_id" value="3"><span class="symbol-label">surprised</span></label><label class="symbol feeling-button feeling-button-frustrated"><input type="radio" name="feeling_id" value="4"><span class="symbol-label">frustrated</span></label><label class="symbol feeling-button feeling-button-puzzled"><input type="radio" name="feeling_id" value="5"><span class="symbol-label">puzzled</span></label></div>';
			}

			echo '<div class="textarea-container">
			<textarea name="text_data" class="textarea-text textarea" maxlength="800" placeholder="Share your thoughts in a post to this community."></textarea>
			</div>Image upload: <input type="file" name="image" accept="image/*">
			<div class="form-buttons">
			<input type="submit" name="submit" class="black-button post-button disabled" value="Send" disabled="">
			</div></form>';

		} else {

			$errors = array();
			$image = NULL;

			if (empty($_POST['text_data'])){
				$errors[] = 'Posts cannot be empty.';
			} elseif (mb_strlen($_POST['text_data']) > 800) { 
				$errors[] = 'Posts cannot be longer than 800 characters.';
			}

			if(empty($_POST['feeling_id']) || strval($_POST['feeling_id']) >= 6) {$_POST['feeling_id'] = 0;} 

			$img=$_FILES['image'];

			if(!empty($img['name'])){
				$filename = $img['tmp_name'];
				/*$client_id="";
				$handle = fopen($filename, "r");
				$data = fread($handle, filesize($filename));
				$pvars = array('image' => base64_encode($data));
				$timeout = 30;
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, 'https://api.imgur.com/3/image.json');
				curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
				curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Client-ID ' . $client_id));
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
				$out = curl_exec($curl);
				curl_close ($curl);
				$pms = json_decode($out,true);
				@$image=$pms['data']['link'] or $errors[] = 'Imgur upload failed';
				*/
				$handle = fopen($filename, "r");
				$data = fread($handle, filesize($filename));
				$pvars = array('file' => (exif_imagetype($filename) == 1 ? 'data:image/gif;base64,' : (exif_imagetype($filename) == 2 ? 'data:image/jpg;base64,' : (exif_imagetype($filename) == 3 ? 'data:image/png;base64,' : (exif_imagetype($filename) == 6 ? 'data:image/bmp;base64,' : '')))) . base64_encode($data),
					'api_key' => '',
					'upload_preset' => '');
				$timeout = 30;
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, 'https://api.cloudinary.com/v1_1/[site]/auto/upload');
				curl_setopt($curl, CURLOPT_TIMEOUT, $timeout);
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
				$out = curl_exec($curl);
				curl_close ($curl);
				$pms = json_decode($out,true);
				@$image=$pms['secure_url'] or $errors[] = 'Image upload failed';
			}

			if(empty($errors)){
				$id = mt_rand(0, 99999999);
				$post_text = $dbc->prepare('INSERT INTO posts (id, post_by_id, post_title, feeling_id, text, post_image, date_time) VALUES (?, ?, ?, ?, ?, ?, NOW())');
				$post_text->bind_param('iiiiss', $id, $_SESSION['user_id'], $title['title_id'], $_POST['feeling_id'], $_POST['text_data'], $image);
				$post_text->execute();

				$get_posts = $dbc->prepare('SELECT * FROM posts INNER JOIN users ON user_id = post_by_id WHERE id = ?');
				$get_posts->bind_param('i', $id);
				$get_posts->execute();
				$posts_result = $get_posts->get_result();
				$post = $posts_result->fetch_array();

				echo '<div class="post trigger" data-href="/posts/'.$post['id'].'" style="display: none;">';
				printPost($post, 0);

			} else {
				echo '<script type="text/javascript">alert("'. $errors[0] .'");</script>';
			}
		}
	}
}