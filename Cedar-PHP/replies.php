<?php
require_once('lib/htm.php');
printHeader('');

$search_reply = $dbc->prepare('SELECT * FROM replies WHERE reply_id = ? LIMIT 1');
$search_reply->bind_param('i', $id);
$search_reply->execute();
$reply_result = $search_reply->get_result();

if (!$reply_result->num_rows == 0){

	$reply = $reply_result->fetch_assoc();

	$search_post = $dbc->prepare('SELECT * FROM posts WHERE posts.id = ? LIMIT 1');
    $search_post->bind_param('i', $reply['reply_post']);
    $search_post->execute();
    $post_result = $search_post->get_result();
	$post = $post_result->fetch_assoc();

	$get_user = $dbc->prepare('SELECT user_name, nickname, user_face, user_level FROM users WHERE users.user_id = ?');
    $get_user->bind_param('i', $post['post_by_id']);
    $get_user->execute();
    $user_result = $get_user->get_result();
    $user = $user_result->fetch_assoc();

    echo '<div id="main-body">';

    if ($reply['deleted'] == 1 && $reply['reply_by_id'] != $_SESSION['user_id']) {
        echo '<div class="no-content track-error" data-track-error="deleted"><div><p class="deleted-message">
            Deleted by administrator.<br>
            Reply ID: '.$reply['reply_id'].'
          </p></div></div>';
    } elseif ($reply['deleted'] == 2){
	    echo '<div class="no-content track-error" data-track-error="deleted"><div><p>Deleted by the author of the comment.</p></div></div>';
    } else {

    	$get_title = $dbc->prepare('SELECT title_id, title_name, title_icon FROM titles WHERE title_id = ? LIMIT 1');
    	$get_title->bind_param('i', $post['post_title']);
    	$get_title->execute();
    	$title_result = $get_title->get_result();
    	$title = $title_result->fetch_assoc();

    	echo '
    	<div class="main-column"><div class="post-list-outline">
    	  <a class="post-permalink-button info-ticker" href="/posts/'. $post['id'] .'">
    	    <span class="icon-container"><img src="'. printFace($user['user_face'], $post['feeling_id']) .'" id="icon"></span>
    	    <span>View <span class="post-user-description">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'\'s post ('. (mb_strlen($post['text']) > 17 ? htmlspecialchars(mb_substr($post['text'],0,17), ENT_QUOTES) . '...' : htmlspecialchars($post['text'], ENT_QUOTES)) .')</span> for this comment.</span>
    	  </a>
    	</div>
    	<div class="post-list-outline">
    	  <div id="post-main" class="reply-permalink-post">
    	    <p class="community-container">
    	      <a href="/titles/'. $title['title_id'] .'">
    	        <img src="'. $title['title_icon'] .'" class="community-icon">'. $title['title_name'] .'</a></p>
              <div id="user-content">';

        $get_user = $dbc->prepare('SELECT user_name, nickname, user_face, user_level FROM users WHERE users.user_id = ?');
        $get_user->bind_param('i', $reply['reply_by_id']);
        $get_user->execute();
        $user_result = $get_user->get_result();
        $user = $user_result->fetch_assoc();

        echo '<title>Cedar - '. htmlspecialchars($user['nickname'], ENT_QUOTES) .'\'s Comment</title>
        <a href="/users/'. $user['user_name'] .'/posts" class="icon-container'.($user['user_level']>1?' verified':'').'"><img src="'. printFace($user['user_face'], $reply['feeling_id']) .'" id="icon"></a>
        <div class="user-name-content">
          <p class="user-name"><a href="/users/'. $user['user_name'] .'/posts">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'</a></p>
          <p class="timestamp-container">
            <span class="timestamp">'. humanTiming(strtotime($reply['date_time'])) .'</span>
          </p>
        </div>
      </div>';

      if ($reply['deleted'] == 1) {
        echo '<p class="deleted-message">
            Deleted by administrator.<br>
            Reply ID: '.$reply['reply_id'].'
          </p>';
    }

      echo '<div id="body">
        <p class="reply-content-text">'.nl2br($reply['text']).'</p>';

        if (!empty($reply['reply_image'])){
        	echo '<div class="screenshot-container still-image"><img src="'. $reply['reply_image'] .'"></div>';
        }

        echo '<div id="post-meta">';

        //yeahs
        $yeah_count = $dbc->prepare('SELECT COUNT(yeah_by) FROM yeahs WHERE type = "reply" AND yeah_post = ?');
        $yeah_count->bind_param('i', $reply['reply_id']);
        $yeah_count->execute();
        $result_count = $yeah_count->get_result();
        $yeah_amount = $result_count->fetch_assoc();

        echo '
        <button class="yeah symbol '. (checkYeahAdded($reply['reply_id'], 'reply', $_SESSION['user_id']) ? 'yeah-added' : '') .'" '. (!empty($_SESSION['signed_in']) && !checkReplyCreator($reply['reply_id'], $_SESSION['user_id']) ? '' : 'disabled') .' id="'. $reply['reply_id'] .'" data-track-label="reply">
          <span class="yeah-button-text">'. (checkYeahAdded($reply['reply_id'], 'reply', $_SESSION['user_id']) ? 'Unyeah' : 'Yeah!') .'</span>
        </button>

        <div class="empathy symbol">
          <span class="yeah-count">' . $yeah_amount['COUNT(yeah_by)'] . '</span>
		</div>
	  </div>';

	    //yeah content
		$get_user = $dbc->prepare('SELECT user_face FROM users WHERE users.user_id = ?');
		$get_user->bind_param('s', $_SESSION['user_id']);
		$get_user->execute();
		$user_result = $get_user->get_result();
		$user = $user_result->fetch_assoc();

		if(empty($yeah_amount['COUNT(yeah_by)'])){
			echo '<div id="yeah-content" class="none">';
		} else {
			echo '<div id="yeah-content">';
		}

		if(!checkYeahAdded($reply['reply_id'], 'reply', $_SESSION['user_id'])){
			echo '
			<div class="icon-container visitor" style="display: none;">
			  <img src="'. printFace($user['user_face'], $reply['feeling_id']) .'" id="icon">
			</div>';
		} else {
			echo '<div class="icon-container visitor"><img src="'. printFace($user['user_face'], $reply['feeling_id']) .'" id="icon"></div>';
		}

		if (!empty($_SESSION['signed_in'])) {
			$yeahs_by = $dbc->prepare('SELECT user_face, user_name FROM users, yeahs WHERE users.user_id = yeahs.yeah_by AND yeahs.yeah_post = ? AND NOT users.user_id = ? LIMIT 14');
			$yeahs_by->bind_param('ii', $reply['reply_id'], $_SESSION['user_id']);
		} else {
			$yeahs_by = $dbc->prepare('SELECT user_face, user_name FROM users, yeahs WHERE users.user_id = yeahs.yeah_by AND yeahs.yeah_post = ?');
			$yeahs_by->bind_param('i', $reply['reply_id']);
		}

		$yeahs_by->execute();
		$yeahs_by_result = $yeahs_by->get_result();

		while($yeah_by = $yeahs_by_result->fetch_array()){
			echo '
			<a href="/users/'. $yeah_by['user_name'] .'/posts" class="icon-container">
			  <img src="' . printFace($yeah_by['user_face'], $reply['feeling_id']) . '" id="icon">
			</a>';
		}

		echo '</div>';

        if ($reply['deleted'] == 0) {
            echo '<div id="post-meta">'. (checkReplyCreator($reply['reply_id'], $_SESSION['user_id']) ? '<button type="button" class="symbol button edit-button edit-reply-button" data-modal-open="#edit-post-page"><span class="symbol-label">Edit</span></button>' : '') .'</div>';
        }

	  echo '</div>
	  <div id="edit-post-page" class="dialog none" data-modal-types="edit-post">
	    <div class="dialog-inner">
	      <div class="window">
	        <h1 class="window-title">Edit Comment</h1>
	        <div class="window-body">
	          <form method="post" class="edit-post-form" action="">
	            <p class="select-button-label">Select an action:</p>
	            <select name="edit-type">
	              <option value="" selected="">Select an option.</option>
	              <option value="spoiler" data-action="">Set as Spoiler</option>
	              <option value="delete" data-action="/deletePost.php?postId='. $reply['reply_id'] .'&postType=reply" data-track-action="deletePost">Delete</option>
				</select>
				<div class="form-buttons">
				  <input type="button" class="olv-modal-close-button gray-button" value="Cancel">
				  <input type="submit" class="post-button black-button disabled" value="Submit" disabled="">
				</div>
			  </form>
            </div>
          </div>
        </div>
      </div>';
    }
}