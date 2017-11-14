<?php
require_once('lib/htm.php');
require_once('lib/htmUsers.php');

$tabTitle = 'Cedar - Posts from Verified Users';

printHeader(3);

echo '<div id="main-body"><div id="sidebar" class="general-sidebar">';

if(!empty($_SESSION['signed_in'])){
	$get_user = $dbc->prepare('SELECT * FROM users WHERE user_id = ? LIMIT 1');
	$get_user->bind_param('i', $_SESSION['user_id']);
	$get_user->execute();
	$user_result = $get_user->get_result();
	$user = $user_result->fetch_assoc();
	userContent($user, "");
}

sidebarSetting();
echo '</div>';

echo '<div class="main-column"><div class="post-list-outline">
  <div id="image-header-content">
    <span class="image-header-title">
      <span class="title">Posts from Verified Users</span>
      <span class="text">Get the latest news here!</span>
    </span>
    <img src="/assets/img/identified-user.png">
  </div>
  <div class="list post-list js-post-list list post-list js-post-list test-identified-post-list">';

$get_posts = $dbc->prepare('SELECT posts.* FROM posts, users WHERE posts.post_by_id = users.user_id AND users.user_level > 1 AND posts.deleted = 0 ORDER BY posts.date_time DESC');
$get_posts->execute();
$posts_result = $get_posts->get_result();

while($posts = $posts_result->fetch_array()){
    $get_title = $dbc->prepare('SELECT title_id, title_name, title_icon FROM titles WHERE title_id = ? LIMIT 1');
    $get_title->bind_param('i', $posts['post_title']);
    $get_title->execute();
    $title_result = $get_title->get_result();
    $title = $title_result->fetch_assoc();

	$get_user = $dbc->prepare('SELECT users.*, profiles.* FROM users INNER JOIN profiles ON profiles.user_id = ? WHERE users.user_id = ?');
	$get_user->bind_param('ii', $posts['post_by_id'], $posts['post_by_id']);
	$get_user->execute();
	$user_result = $get_user->get_result();
	$user = $user_result->fetch_assoc();
		
	echo '<div class="post trigger" data-href="/posts/' . $posts['id'] . '">
	  <p class="community-container">
        <a href="/titles/'. $title['title_id'] .'"><img src="'. $title['title_icon'] .'" class="community-icon">'. $title['title_name'] .'</a></p>
		<a href="/users/'. $user['user_name'] .'/posts" class="icon-container';
		
	if($user['user_level'] > 1){
		echo ' verified';
	}

	echo '"><img src="' . printFace($user['user_face'], $posts['feeling_id']) . '" id="icon"></a><div class="toggle-button">';

	$check_followed = $dbc->prepare('SELECT * FROM follows WHERE follow_by = ? AND follow_to = ? LIMIT 1');
	$check_followed->bind_param('ii', $_SESSION['user_id'], $user['user_id']);
	$check_followed->execute();
	$followed_result = $check_followed->get_result();

	if (($followed_result->num_rows == 0) && ($_SESSION['user_id'] != $user['user_id']) && !empty($_SESSION['signed_in'])){
		echo '<button type="button" data-user-id="'. $user['user_id'] .'" class="follow-button button symbol relationship-button" data-community-id="" data-url-id="" data-track-label="user" data-title-id="" data-track-action="follow" data-track-category="follow">Follow</button>
		<button type="button" class="button follow-done-button relationship-button symbol none" disabled="">Follow</button>';
	}

	echo '</div><p class="user-name"><a href="/users/'. $user['user_name'] .'/posts">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'</a></p>
	<p class="text test-profile-comment">'. $user['bio'] .'</p>
	<div id="body">';
		
	if (!empty($posts['post_image'])){
		echo '<div class="screenshot-container"><img src="' . $posts['post_image'] . '"></div>';
	}
		
		
	echo '<div id="post-body">'. (mb_strlen($posts['text']) > 199 ? htmlspecialchars(mb_substr($posts['text'],0,200), ENT_QUOTES) .'...' : htmlspecialchars($posts['text'], ENT_QUOTES)) . '</div>';
		
			
	echo '<div id="post-meta"><p class="timestamp-container"><a id="timestamp">'. humanTiming(strtotime($posts['date_time'])) .'</a></p>';
		
	$yeah_count = $dbc->prepare('SELECT COUNT(yeah_by) FROM yeahs WHERE type = "post" AND yeahs.yeah_post = ?');
	$yeah_count->bind_param('i', $posts['id']);
	$yeah_count->execute();
    $result_count = $yeah_count->get_result();
	$yeah_amount = $result_count->fetch_assoc();
					
	echo '<div class="empathy symbol"><span class="yeah-count">' . $yeah_amount['COUNT(yeah_by)'] . '</span></div>';
		
	$reply_count = $dbc->prepare('SELECT COUNT(reply_id) FROM replies WHERE reply_post = ? AND deleted = 0');
	$reply_count->bind_param('i', $posts['id']);
	$reply_count->execute();
    $result_count = $reply_count->get_result();
	$reply_amount = $result_count->fetch_assoc();
		
	echo '<div class="reply symbol"><span id="reply-count">' . $reply_amount['COUNT(reply_id)'] . '</span></div>
	</div></div></div>';
}