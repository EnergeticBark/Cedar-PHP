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

		printHeader('');

		echo '<script>var loadOnScroll=true;</script><div id="main-body"><div id="sidebar" class="user-sidebar">';

		userContent($user, "yeahs");

		userSidebarSetting($user, 2);

		userInfo($user);

		echo '</div>
		<div class="main-column">
		  <div class="post-list-outline">
		    <h2 class="label">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'\'s Replies</h2>
		    <div class="list post-list js-post-list" data-next-page-url="/users/'. $user['user_name'] .'/replies?offset=1&dateTime='.date("Y-m-d H:i:s").'">';

		$get_replies = $dbc->prepare('SELECT * FROM replies WHERE reply_by_id = ? AND deleted = 0 ORDER BY date_time DESC LIMIT 20');
		$get_replies->bind_param('i', $user['user_id']);

	} else {
		$offset = ($_GET['offset'] * 25);
		$dateTime = htmlspecialchars($_GET['dateTime']);
		$get_replies = $dbc->prepare('SELECT * FROM replies WHERE reply_by_id = ? AND deleted = 0 AND date_time < ? ORDER BY date_time DESC LIMIT 20 OFFSET ?');
		$get_replies->bind_param('isi', $user['user_id'], $dateTime, $offset);
	}

	$get_replies->execute();
	$replies_result = $get_replies->get_result();

	if(!$replies_result->num_rows == 0){

		while($replies = $replies_result->fetch_array()){

			$get_user_post = $dbc->prepare('SELECT users.* FROM users, posts WHERE users.user_id = posts.post_by_id AND posts.id = ? LIMIT 1');
			$get_user_post->bind_param('i', $replies['reply_post']);
			$get_user_post->execute();
			$user_post_result = $get_user_post->get_result();
			$user_post = $user_post_result->fetch_assoc();

			$get_reply_post = $dbc->prepare('SELECT * FROM posts WHERE id = ? LIMIT 1');
			$get_reply_post->bind_param('i', $replies['reply_post']);
			$get_reply_post->execute();
			$reply_post_result = $get_reply_post->get_result();
			$reply_post = $reply_post_result->fetch_assoc();

			$get_reply_user = $dbc->prepare('SELECT * FROM users WHERE user_id = ? LIMIT 1');
			$get_reply_user->bind_param('i', $replies['reply_by_id']);
			$get_reply_user->execute();
			$reply_user_result = $get_reply_user->get_result();
			$reply_user = $reply_user_result->fetch_assoc();

			echo '<div data-href="/replies/'. $replies['reply_id'] .'" class="post post-subtype-default trigger">
			  <p class="community-container">
			    <a class="test-community-link" href="/posts/'. $replies['reply_post'] .'"><img src="'. printFace($user_post['user_face'], $reply_post['feeling_id']) .'" class="community-icon">Comment on '. htmlspecialchars($user_post['nickname'], ENT_QUOTES) .'\'s Post</a>
			  </p>
			  <a href="/users/'. $reply_user['user_name'] .'/posts" class="icon-container';

			if($reply_user['user_level']>1){
				echo ' verified';
			}

			echo '"><img src="'. printFace($reply_user['user_face'], $replies['feeling_id']) .'" id="icon"></a><p class="user-name"><a href="/users/'. $reply_user['user_name'] .'/posts">'. htmlspecialchars($reply_user['nickname'], ENT_QUOTES) .'</a></p><p class="timestamp-container"><a id="timestamp">' . humanTiming(strtotime($replies['date_time'])) . '</a></p><div id="body">';

			if (!empty($replies['reply_image'])){
				echo '<div class="screenshot-container"><img src="' . $replies['reply_image'] . '"></div>';
			}

			echo '<div id="post-body">' . (mb_strlen($replies['text']) > 199 ? mb_substr($replies['text'],0,200) . '...' : $replies['text']) . '</div><div id="post-meta">';

			$yeah_count = $dbc->prepare('SELECT COUNT(yeah_by) FROM yeahs WHERE type = "reply" AND yeah_post = ?');
			$yeah_count->bind_param('i', $replies['reply_id']);
			$yeah_count->execute();
			$result_count = $yeah_count->get_result();
			$yeah_amount = $result_count->fetch_assoc();

			echo '<button class="yeah symbol '. (checkYeahAdded($replies['reply_id'], 'reply', $_SESSION['user_id']) ? 'yeah-added' : '') .'" '. (!empty($_SESSION['signed_in']) && !checkReplyCreator($replies['reply_id'], $_SESSION['user_id']) ? '' : 'disabled') .' id="'. $replies['reply_id'] .'" data-track-label="reply"><span class="yeah-button-text">'. (checkYeahAdded($replies['reply_id'], 'reply', $_SESSION['user_id']) ? 'Unyeah' : 'Yeah!') .'</span></button><div class="empathy symbol"><span class="yeah-count">' . $yeah_amount['COUNT(yeah_by)'] . '</span></div></div></div></div>';
		}

	} else {

		if(!((isset($_GET['offset']) && is_numeric($_GET['offset'])) && isset($_GET['dateTime']))){

			echo '<div id="user-page-no-content" class="no-content"><div><p>There are no replies yet.</p></div></div></div>';
		}
	}
}