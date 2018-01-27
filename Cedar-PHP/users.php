<?php
require_once('lib/htm.php');
require_once('lib/htmUsers.php');

$get_user = $dbc->prepare('SELECT * FROM users INNER JOIN profiles ON profiles.user_id = users.user_id WHERE user_name = ? LIMIT 1');
$get_user->bind_param('s', $action);
$get_user->execute();
$user_result = $get_user->get_result();

if ($user_result->num_rows == 0){
	printHeader('');
	noUser();
} else {

	$user = $user_result->fetch_assoc();

	if(!((isset($_GET['offset']) && is_numeric($_GET['offset'])) && isset($_GET['dateTime']))){

		$tabTitle = 'Cedar - '. htmlspecialchars($user['nickname'], ENT_QUOTES) .'\'s Profile';

		if (empty($_SESSION['signed_in']) || $_SESSION['user_id'] == $user['user_id']) {printHeader(1);} else {printHeader('');}

		echo '<script>var loadOnScroll=true;</script><div id="main-body"><div id="sidebar" class="user-sidebar">';

		userContent($user, "posts");

		userSidebarSetting($user, 1);

		userInfo($user);

		echo '</div><div class="main-column"><div class="post-list-outline">
		<h2 class="label">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'\'s Posts</h2>
		<div class="list post-list js-post-list" data-next-page-url="/users/'. $user['user_name'] .'/posts?offset=1&dateTime='.date("Y-m-d H:i:s").'">';

		if (!empty($_SESSION['signed_in']) && $user['user_id'] == $_SESSION['user_id']) {
			$get_posts = $dbc->prepare('SELECT * FROM posts INNER JOIN titles ON title_id = post_title WHERE post_by_id = ? AND deleted < 2 ORDER BY date_time DESC LIMIT 25');
		} else {
			$get_posts = $dbc->prepare('SELECT * FROM posts INNER JOIN titles ON title_id = post_title WHERE post_by_id = ? AND deleted = 0 ORDER BY date_time DESC LIMIT 25');
		}
		$get_posts->bind_param('i', $user['user_id']);

	} else {

		$offset = ($_GET['offset'] * 25);
		$dateTime = htmlspecialchars($_GET['dateTime']);
		if ($user['user_id'] == $_SESSION['user_id']) {
			$get_posts = $dbc->prepare('SELECT * FROM posts INNER JOIN titles ON title_id = post_title WHERE post_by_id = ? AND date_time < ? AND deleted < 2 ORDER BY date_time DESC LIMIT 25 OFFSET ?');
		} else {
			$get_posts = $dbc->prepare('SELECT * FROM posts INNER JOIN titles ON title_id = post_title WHERE post_by_id = ? AND date_time < ? AND deleted = 0 ORDER BY date_time DESC LIMIT 25 OFFSET ?');
		}
		$get_posts->bind_param('isi', $user['user_id'], $dateTime, $offset);
	}

	$get_posts->execute();
	$posts_result = $get_posts->get_result();

	if(!$posts_result->num_rows == 0){

		echo '<div id="user-page-no-content" class="none"></div>';

		while($posts = $posts_result->fetch_array()){

			echo '<div data-href="/posts/'. $posts['id'] .'" class="post post-subtype-default trigger">
			<p class="community-container">

			<a class="test-community-link" href="/titles/'. $posts['title_id'] .'"><img src="'. $posts['title_icon'] .'" class="community-icon">'. $posts['title_name'] .'</a></p>';

			printPost(array_merge($posts, $user), 1);
		}

	} else {
		if(!(isset($_GET['offset']) && is_numeric($_GET['offset']) && isset($_GET['dateTime']))){
			echo '
			<div id="user-page-no-content" class="no-content">
			  <div>
			    <p>No posts have been made yet.</p>
			  </div>
			</div>';
		}
	}
}