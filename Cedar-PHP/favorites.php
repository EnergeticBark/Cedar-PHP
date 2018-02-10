<?php
require_once('lib/htm.php');
require_once('lib/htmUsers.php');

if (isset($action)) {
	$get_user = $dbc->prepare('SELECT * FROM users INNER JOIN profiles ON profiles.user_id = users.user_id WHERE user_name = ? LIMIT 1');
	$get_user->bind_param('s', $action);
	$get_user->execute();
	$user_result = $get_user->get_result();
	$user = $user_result->fetch_assoc();
	$tabTitle = 'Cedar - '. $user['nickname'] .'\'s Favorite Communities';
	printHeader('');
} else {
	$get_user = $dbc->prepare('SELECT * FROM users INNER JOIN profiles ON profiles.user_id = users.user_id WHERE users.user_id = ? LIMIT 1');
	$get_user->bind_param('i', $_SESSION['user_id']);
	$get_user->execute();
	$user_result = $get_user->get_result();
	$user = $user_result->fetch_assoc();
	$tabTitle = 'Cedar - Community List(Favorites)';
	printHeader(3);
}

$post_count = $dbc->prepare('SELECT COUNT(id) FROM posts WHERE post_by_id = ?');
$post_count->bind_param('i', $user['user_id']);
$post_count->execute();
$result_count = $post_count->get_result();
$post_amount = $result_count->fetch_assoc();

$yeah_count = $dbc->prepare('SELECT COUNT(yeah_by) FROM yeahs WHERE yeah_by = ?');
$yeah_count->bind_param('i', $user['user_id']);
$yeah_count->execute();
$result_count = $yeah_count->get_result();
$yeah_amount = $result_count->fetch_assoc();

echo '<div id="sidebar" class="user-sidebar">';

userContent($user, "Favorites");

userSidebarSetting($user, 0);

userInfo($user);

echo '</div><div class="main-column"><div class="post-list-outline">
<h2 class="label">'.(isset($action)?$user['nickname'].'\'s ':'').'Favorite Communities</h2><ul class="list community-list">';
	
$get_fav_titles = $dbc->prepare('SELECT titles.* FROM titles, favorite_titles WHERE titles.title_id = favorite_titles.title_id AND favorite_titles.user_id = ? ORDER BY favorite_titles.fav_id DESC');
$get_fav_titles->bind_param('i', $user['user_id']);
$get_fav_titles->execute();
$fav_titles_result = $get_fav_titles->get_result();

if ($fav_titles_result->num_rows == 0){
	echo '<div class="no-content"><div><p>'.(isset($action)?'No favorite communities added yet':'Tap the ☆ button on a community\'s page to have it show up as a favorite community here').'.</p></div></div>';
} else {

	echo '<ul class="list community-list">';

	while ($fav_titles = $fav_titles_result->fetch_assoc()){
		printTitleInfo($fav_titles);
	}
}