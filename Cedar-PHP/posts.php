<?php
require_once('lib/htm.php');

$search_post = $dbc->prepare('SELECT * FROM posts WHERE posts.id = ? LIMIT 1');
$search_post->bind_param('i', $id);
$search_post->execute();
$post_result = $search_post->get_result();

if ($post_result->num_rows == 0) {
    printHeader(0);
    exit('<title>Cedar - Error</title><div class="no-content track-error" data-track-error="404"><div><p>The post could not be found.</p></div></div>');
}

    $post = $post_result->fetch_assoc();

    $get_user = $dbc->prepare('SELECT * FROM users INNER JOIN profiles ON users.user_id = profiles.user_id WHERE users.user_id = ?');
    $get_user->bind_param('i', $post['post_by_id']);
    $get_user->execute();
    $user_result = $get_user->get_result();
    $user = $user_result->fetch_assoc();

    $tabTitle = 'Cedar - '. htmlspecialchars($user['nickname'], ENT_QUOTES) .'\'s post';

    printHeader('');

if ($post['deleted'] == 1 && $post['post_by_id'] != $_SESSION['user_id']) {
    echo '<div class="no-content track-error" data-track-error="deleted"><div><p class="deleted-message">
            Deleted by administrator.<br>
            Post ID: '. $post['id'] .'
          </p></div></div>';
} elseif ($post['deleted'] == 2) {
    echo '<div class="no-content track-error" data-track-error="deleted"><div><p>Deleted by poster.</p></div></div>';
} else {
    echo '<div class="main-column"><div class="post-list-outline"><div id="post-main">';

    $get_title = $dbc->prepare('SELECT * FROM titles WHERE title_id = ? LIMIT 1');
    $get_title->bind_param('i', $post['post_title']);
    $get_title->execute();
    $title_result = $get_title->get_result();
    $title = $title_result->fetch_assoc();

    echo '<meta property="og:title" content="Post to '. $title['title_name'] .' - Cedar">
		<meta property="og:url" content="https://suckmyass.000webhostapp.com/posts/'. $post['id'] .'">
		<meta property="og:description" content="'. htmlspecialchars($user['nickname'], ENT_QUOTES) .' : '. (mb_strlen($post['text']) > 46 ? htmlspecialchars(mb_substr($post['text'], 0, 47)) .'...' : htmlspecialchars($post['text'], ENT_QUOTES)) .' - Cedar">

		<header class="community-container">
		  <h1 class="community-container-heading">
		    <a href="/titles/'. $title['title_id'] .'"><img src="'. $title['title_icon'] .'" class="community-icon">'. $title['title_name'] .'</a>
		  </h1>
		</header>

		<div id="user-content">
		  <a href="/users/'. $user['user_name'] .'/posts" class="icon-container'. ($user['user_level'] > 1 ? ' verified' : '') .'">
		    <img src="'. printFace($user['user_face'], $post['feeling_id']) .'" id="icon">
		  </a>
		  <div class="user-name-content">
			<p class="user-name">
			  <a href="/users/'. $user['user_name'] .'/posts" '.(isset($user['name_color']) ? 'style="color: '. $user['name_color'] .'"' : '').'>'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'</a><span id="user-id">'. $user['user_name'] .'</span></p><p class="timestamp-container"><span class="timestamp">'. humanTiming(strtotime($post['date_time'])) .'</span></p></div></div><div id="main-post-body">';

    if ($post['deleted'] == 1) {
        echo '<p class="deleted-message">
            Deleted by administrator.<br>
            Post ID: '. $post['id'] .'
          </p>';
    }

    if (!empty($post['post_image'])) {
        echo '<div class="screenshot-container still-image"><img src="'. $post['post_image'] .'"></div>';
    }

    $post['text'] = htmlspecialchars($post['text'], ENT_QUOTES);

    $post['text'] = preg_replace('|([\w\d]*)\s?(https?://([\d\w\.-]+\.[\w\.]{2,6})[^\s\]\[\<\>]*/?)|i', '$1 <a href="$2" target="_blank" class="post-link">$2</a>', $post['text']);

    echo '<div id="post-body">'. nl2br($post['text'], ENT_QUOTES) .'</div><div id="post-meta">';

    $yeah_count = $dbc->prepare('SELECT COUNT(yeah_by) FROM yeahs WHERE type = "post" AND yeah_post = ?');
    $yeah_count->bind_param('i', $post['id']);
    $yeah_count->execute();
    $result_count = $yeah_count->get_result();
    $yeah_amount = $result_count->fetch_assoc();

    $nah_count = $dbc->prepare('SELECT COUNT(nah_by) FROM nahs WHERE type = 0 AND nah_post = ?');
    $nah_count->bind_param('i', $post['id']);
    $nah_count->execute();
    $result_count = $nah_count->get_result();
    $nah_amount = $result_count->fetch_assoc();

    $yeahs = $yeah_amount['COUNT(yeah_by)'] - $nah_amount['COUNT(nah_by)'];

        echo '<button class="yeah symbol';

    if (!empty($_SESSION['signed_in']) && checkYeahAdded($post['id'], 'post', $_SESSION['user_id'])) {
        echo ' yeah-added';
    }

    echo '"';

    if (empty($_SESSION['signed_in']) || checkPostCreator($post['id'], $_SESSION['user_id'])) {
        echo ' disabled ';
    }

    echo 'id="'. $post['id'] .'" data-track-label="post"><span class="yeah-button-text">';

    if (!empty($_SESSION['signed_in']) && checkYeahAdded($post['id'], 'post', $_SESSION['user_id'])) {
        echo 'Unyeah';
    } else {
        echo 'Yeah!';
    }

    echo '</span></button>';







    echo '<button class="nah symbol';

    if (!empty($_SESSION['signed_in']) && checkNahAdded($post['id'], 0, $_SESSION['user_id'])) {
        echo ' nah-added';
    }

    echo '"';

    if (empty($_SESSION['signed_in']) || checkPostCreator($post['id'], $_SESSION['user_id'])) {
        echo ' disabled ';
    }

    echo 'id="'. $post['id'] .'" data-track-label="0"><span class="nah-button-text">';

    if (!empty($_SESSION['signed_in']) && checkNahAdded($post['id'], 0, $_SESSION['user_id'])) {
        echo 'Un-nah.';
    } else {
        echo 'Nah...';
    }

    echo '</span></button>';


    echo '<div class="empathy symbol" yeahs="'. $yeah_amount['COUNT(yeah_by)']  .'" nahs="'. $nah_amount['COUNT(nah_by)']  .'" title="'. $yeah_amount['COUNT(yeah_by)'] .' '. ($yeah_amount['COUNT(yeah_by)'] == 1 ? 'Yeah' : 'Yeahs') .' / '. $nah_amount['COUNT(nah_by)'] .' '. ($nah_amount['COUNT(nah_by)'] == 1 ? 'Nah' : 'Nahs') .'"><span class="yeah-count">'. $yeahs .'</span></div>';

    $reply_count = $dbc->prepare('SELECT COUNT(reply_id) FROM replies WHERE reply_post = ? AND deleted = 0');
    $reply_count->bind_param('i', $post['id']);
    $reply_count->execute();
    $result_count = $reply_count->get_result();
    $reply_amount = $result_count->fetch_assoc();

    echo '<div class="reply symbol"><span id="reply-count">'. $reply_amount['COUNT(reply_id)'] .'</span></div>
		</div></div></div>';

    //yeah content

    if ($post['deleted'] != 1) {
        $get_user = $dbc->prepare('SELECT * FROM users WHERE user_id = ?');
        $get_user->bind_param('i', $_SESSION['user_id']);
        $get_user->execute();
        $user_result = $get_user->get_result();
        $user = $user_result->fetch_assoc();

        if (empty($yeah_amount['COUNT(yeah_by)'])) {
            echo '<div id="yeah-content" class="none">';
        } else {
            echo '<div id="yeah-content">' ;
        }

        if (!checkYeahAdded($post['id'], 'post', $_SESSION['user_id'])) {
            echo '<a href="/users/'. $user['user_name'] .'/posts" class="icon-container visitor'. ($user['user_level'] > 1 ? ' verified' : '') .'" style="display: none;">
				<img src="'. printFace($user['user_face'], $post['feeling_id']) .'" id="icon"></a>';
        } else {
            echo '<a href="/users/'. $user['user_name'] .'/posts" class="icon-container visitor'. ($user['user_level'] > 1 ? ' verified' : '') .'">
				<img src="'. printFace($user['user_face'], $post['feeling_id']) .'" id="icon"></a>';
        }

        if (!empty($_SESSION['signed_in'])) {
            $yeahs_by = $dbc->prepare('SELECT * FROM users, yeahs WHERE users.user_id = yeahs.yeah_by AND yeahs.yeah_post = ? AND NOT users.user_id = ? ORDER BY yeahs.yeah_id DESC LIMIT 14');
            $yeahs_by->bind_param('ii', $post['id'], $_SESSION['user_id']);
        } else {
            $yeahs_by = $dbc->prepare('SELECT * FROM users, yeahs WHERE users.user_id = yeahs.yeah_by AND yeahs.yeah_post = ? ORDER BY yeahs.yeah_id DESC LIMIT 14');
            $yeahs_by->bind_param('i', $post['id']);
        }
        $yeahs_by->execute();
        $yeahs_by_result = $yeahs_by->get_result();

        while ($yeah_by = $yeahs_by_result->fetch_array()) {
            echo '<a href="/users/'. $yeah_by['user_name'] .'/posts" class="icon-container'. ($yeah_by['user_level'] > 1 ? ' verified' : '') .'">
				  <img src="'. printFace($yeah_by['user_face'], $post['feeling_id']) .'" id="icon"></a>';
        }

        echo '</div>';

        //edit button

        echo '<div class="buttons-content">';

        if ($post['post_by_id'] == $_SESSION['user_id']) {
            echo '<button type="button" class="symbol button edit-button edit-post-button" data-modal-open="#edit-post-page">
				<span class="symbol-label">Edit</span></button>';
        }

        echo '</div>';

        //comments
        echo '<div id="reply-content"><h2 class="reply-label">Comments</h2><ul class="list reply-list test-reply-list">';
        $search_replies = $dbc->prepare('SELECT * FROM replies INNER JOIN users ON user_id = reply_by_id INNER JOIN profiles ON users.user_id = profiles.user_id WHERE reply_post = ? AND deleted < 2 ORDER BY date_time ASC');
        $search_replies->bind_param('i', $id);
        $search_replies->execute();
        $replies_result = $search_replies->get_result();

        if ($replies_result->num_rows == 0) {
            echo '<div class="no-reply-content"><div><p>This post has no comments.</p></div></div>';
        } else {
            while ($replies = $replies_result->fetch_array()) {
                echo '<li class="post'. ($replies['reply_by_id'] == $post['post_by_id']?' my' : '') .' trigger" data-href="/replies/'. $replies['reply_id'] .'">';
                printReply($replies);
            }
        }

        echo '</ul></div><h2 class="reply-label">Add a comment</h2>';

        include 'postReply.php';

        echo '
			<div id="edit-post-page" class="dialog none" data-modal-types="edit-post">
	          <div class="dialog-inner">
	            <div class="window">
	              <h1 class="window-title">Edit Post</h1>
	              <div class="window-body">
	                <form method="post" class="edit-post-form" action="">
	                  <input type="hidden" name="token" value="2wdaCleDbc7i8JOwRK8_vw">
	                  <p class="select-button-label">Select an action:</p>
	                  <select name="edit-type">
	                    <option value="" selected="">Select an option.</option>
	                    '. (isset($post['post_image']) ? '<option value="screenshot-profile-post" data-action="/posts/'. $post['id'] .'/image.set_profile_post">Set Image as Favorite Post</option>' : ''). '
	                    <option value="edit" data-action="" data-track-action="deletePost">Edit Post</option>
	                    <option value="delete" data-action="/deletePost.php?postId='. $post['id'] .'&postType=post" data-track-action="deletePost">Delete</option>
	                  </select>
	                  <div class="form-buttons">
	                    <input type="button" class="olv-modal-close-button gray-button" value="Cancel">
	                    <input type="submit" class="post-button black-button disabled" value="Submit" disabled="">
	                  </div>
	                </form>
	              </div>
	            </div>
	          </div>
	        </div>

	        </div></div></div>';
    }
}
