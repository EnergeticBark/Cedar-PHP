<?php
require_once('lib/htm.php');
require_once('lib/htmUsers.php');

if($_SERVER['REQUEST_METHOD'] != 'POST'){
	$tabTitle = 'Cedar - Community Creation';
	printHeader('');

	echo '<div id="main-body">';
	$get_user = $dbc->prepare('SELECT * FROM users INNER JOIN profiles ON profiles.user_id = users.user_id WHERE users.user_id = ? LIMIT 1');
	$get_user->bind_param('i', $_SESSION['user_id']);
	$get_user->execute();
	$user_result = $get_user->get_result();
	$user = $user_result->fetch_assoc();
	echo '<div id="sidebar" class="general-sidebar">';
	userContent($user, "");
	sidebarSetting();
	?>

	</div>
	<div class="main-column">
		<div class="post-list-outline">
			<h2 class="label">Create a Community</h2>
			<form id="account-settings-form" class="setting-form" method="post" action="/titles/new">
				<ul class="settings-list">
					<li>
						<p class="settings-label">Name your community.</p>
						<input class="textarea" placeholder="Your community name." type="text" maxlength="64" name="name" style="cursor: auto; height: auto;">
					</li>

					<li>
						<p class="settings-label">Write a description for your community.</p>
						<textarea class="textarea" name="description" maxlength="400" placeholder="Your community description."></textarea>
					</li>

					<li>
						<p class="settings-label">Upload an icon for your community.</p>
						<p class="note">Make sure the image isn\'t too big. Also make sure it\'s square shaped!<br>
						The recommended resolution is 128x128px.</p>
						<input type="file" name="title_icon" accept="image/*">
					</li>

					<li>
						<p class="settings-label">Upload a banner for your community.</p>
						<p class="note">Again, make sure the image isn\'t too big. The recommended resolution is 347x145px.</p>
						<input type="file" name="title_banner" accept="image/*">
					</li>
				</ul>
				<div class="form-buttons"><input type="submit" class="black-button apply-button" value="Create Community"></div>
			</form>
		</div>
	</div>
</div>
</div>

<?php
} else {
	$error = false;
	if (empty($_POST['name'])){
		$error= true;
	}
	if (empty($_POST['description'])){
		$error= true;
	}

	if (!$error){
		$img=$_FILES['title_icon'];
		if (empty($img['name'])) {
			$error= true;
		} else {
			$filename = $img['tmp_name'];
			$icon = uploadImage($filename);
			if ($icon == 1) {
				$error= true;
			}
		}

		$img2=$_FILES['title_banner'];
		if (empty($img2['name'])) {
			$error= true;
		} else {
			$filename = $img2['tmp_name'];
			$banner = uploadImage($filename);
			if ($banner == 1) {
				$error= true;
			}
		}

		if(!$error){
			$new_community = $dbc->prepare('INSERT INTO titles (title_id, title_name, title_desc, title_icon, title_banner, perm, type, user_made) VALUES (?,?,?,?,?,?,?,?)');
			$id = mt_rand(10000000, 99999999);
			$perm = 0;
			$type = 6;
			$user_made = 1;
			$new_community->bind_param('issssiii', $id, $_POST['name'], $_POST['description'], $icon, $banner, $perm, $type, $user_made);
			$new_community->execute();
			echo('success');
		}
	}
}
