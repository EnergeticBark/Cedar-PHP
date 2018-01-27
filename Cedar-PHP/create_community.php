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
						<input class="textarea" placeholder="do what the text above says." type="text" maxlength="64" name="name" style="cursor: auto; height: auto;">
					</li>

					<li>
						<p class="settings-label">Write a description for your community.</p>
						<textarea id="profile-text" class="textarea" name="profile_comment" maxlength="400" placeholder="ok"></textarea>
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
}
