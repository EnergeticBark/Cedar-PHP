<?php
require_once('lib/htm.php');
printHeader(0);

$search_reply = $dbc->prepare('SELECT * FROM replies WHERE reply_id = ? LIMIT 1');
$search_reply->bind_param('i', $id);
$search_reply->execute();
$reply_result = $search_reply->get_result();

if ($reply_result->num_rows == 0) {
    exit('<title>Cedar - Error</title><div class="no-content track-error" data-track-error="404"><div><p>The reply could not be found.</p></div></div>');
}

$reply = $reply_result->fetch_assoc();

if ($reply['deleted'] == 1 && $reply['reply_by_id'] != $_SESSION['user_id']) {
    exit('<div class="no-content track-error" data-track-error="deleted"><div><p class="deleted-message">
        Deleted by administrator.<br>
        Reply ID: '. $reply['reply_id'] .'
        </p></div></div>');
} elseif ($reply['deleted'] == 2) {
    exit('<div class="no-content track-error" data-track-error="deleted"><div><p>Deleted by the author of the comment.</p></div></div>');
}

$search_post = $dbc->prepare('SELECT id, post_title, text, feeling_id, nickname, user_face FROM posts INNER JOIN users ON user_id = post_by_id WHERE id = ? LIMIT 1');
$search_post->bind_param('i', $reply['reply_post']);
$search_post->execute();
$post_result = $search_post->get_result();
$post = $post_result->fetch_assoc();

$get_title = $dbc->prepare('SELECT title_id, title_name, title_icon FROM titles WHERE title_id = ? LIMIT 1');
$get_title->bind_param('i', $post['post_title']);
$get_title->execute();
$title_result = $get_title->get_result();
$title = $title_result->fetch_assoc();

$get_user = $dbc->prepare('SELECT * FROM users INNER JOIN profiles ON profiles.user_id = users.user_id WHERE users.user_id = ?');
$get_user->bind_param('i', $reply['reply_by_id']);
$get_user->execute();
$user_result = $get_user->get_result();
$user = $user_result->fetch_assoc();

$yeah_count = $dbc->prepare('SELECT COUNT(yeah_by) FROM yeahs WHERE type = "reply" AND yeah_post = ?');
$yeah_count->bind_param('i', $reply['reply_id']);
$yeah_count->execute();
$result_count = $yeah_count->get_result();
$yeah_amount = $result_count->fetch_assoc();

$nah_count = $dbc->prepare('SELECT COUNT(nah_by) FROM nahs WHERE type = 1 AND nah_post = ?');
$nah_count->bind_param('i', $reply['reply_id']);
$nah_count->execute();
$result_count = $nah_count->get_result();
$nah_amount = $result_count->fetch_assoc();

$yeahs = $yeah_amount['COUNT(yeah_by)'] - $nah_amount['COUNT(nah_by)'];

echo '
    	<div class="main-column"><div class="post-list-outline">
    	  <a class="post-permalink-button info-ticker" href="/posts/'. $post['id'] .'">
    	    <span class="icon-container"><img src="'. printFace($post['user_face'], $post['feeling_id']) .'" id="icon"></span>
    	    <span>View <span class="post-user-description">'. htmlspecialchars($post['nickname'], ENT_QUOTES) .'\'s post ('. (mb_strlen($post['text']) > 17 ? htmlspecialchars(mb_substr($post['text'], 0, 17), ENT_QUOTES) . '...' : htmlspecialchars($post['text'], ENT_QUOTES)) .')</span> for this comment.</span>
    	  </a>
    	</div>
    	<div class="post-list-outline">
    	  <div id="post-main" class="reply-permalink-post">
    	    <p class="community-container">
    	      <a href="/titles/'. $title['title_id'] .'">
    	        <img src="'. $title['title_icon'] .'" class="community-icon">'. $title['title_name'] .'</a></p>
              <div id="user-content">
              <title>Cedar - '. htmlspecialchars($user['nickname'], ENT_QUOTES) .'\'s Comment</title>
        <a href="/users/'. $user['user_name'] .'/posts" class="icon-container'.($user['user_level'] > 1 ? ' verified' : '').'"><img src="'. printFace($user['user_face'], $reply['feeling_id']) .'" id="icon"></a>
        <div class="user-name-content">
          <p class="user-name"><a href="/users/'. $user['user_name'] .'/posts" '.(isset($user['name_color']) ? 'style="color: '. $user['name_color'] .'"' : '').'>'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'</a></p>
          <p class="timestamp-container">
            <span class="timestamp">'. humanTiming(strtotime($reply['date_time'])) .'</span>
          </p>
        </div>
      </div>';

if ($reply['deleted'] == 1) {
    echo '<p class="deleted-message">
    Deleted by administrator.<br>
    Reply ID: '. $reply['reply_id'] .'
    </p>';
}

echo '<div id="body">
<p class="reply-content-text">'.nl2br($reply['text']).'</p>';

if (!empty($reply['reply_image'])) {
    echo '<div class="screenshot-container still-image"><img src="'. $reply['reply_image'] .'"></div>';
}

//yeahs
echo '<div id="post-meta">
<button class="yeah symbol';

if (!empty($_SESSION['signed_in']) && checkYeahAdded($reply['reply_id'], 'reply', $_SESSION['user_id'])) {
    echo ' yeah-added';
}

echo '"';

if (empty($_SESSION['signed_in']) || checkReplyCreator($reply['reply_id'], $_SESSION['user_id'])) {
    echo ' disabled ';
}

echo 'id="'. $reply['reply_id'] .'" data-track-label="reply"><span class="yeah-button-text">';

if (!empty($_SESSION['signed_in']) && checkYeahAdded($reply['reply_id'], 'reply', $_SESSION['user_id'])) {
    echo 'Unyeah';
} else {
    echo 'Yeah!';
}

echo '</span></button>
<button class="nah symbol';

if (!empty($_SESSION['signed_in']) && checkNahAdded($reply['reply_id'], 1, $_SESSION['user_id'])) {
    echo ' nah-added';
}

echo '"';

if (empty($_SESSION['signed_in']) || checkReplyCreator($reply['reply_id'], $_SESSION['user_id'])) {
    echo ' disabled ';
}

echo 'id="'. $reply['reply_id'] .'" data-track-label="1"><span class="nah-button-text">';

if (!empty($_SESSION['signed_in']) && checkNahAdded($reply['reply_id'], 1, $_SESSION['user_id'])) {
    echo 'Un-nah.';
} else {
    echo 'Nah...';
}

echo '</span></button>
<div class="empathy symbol" yeahs="'. $yeah_amount['COUNT(yeah_by)']  .'" nahs="'. $nah_amount['COUNT(nah_by)']  .'" title="'. $yeah_amount['COUNT(yeah_by)'] .' '. ($yeah_amount['COUNT(yeah_by)'] == 1 ? 'Yeah' : 'Yeahs') .' / '. $nah_amount['COUNT(nah_by)'] .' '. ($nah_amount['COUNT(nah_by)'] == 1 ? 'Nah' : 'Nahs') .'"><span class="yeah-count">'. $yeahs .'</span></div></div>';

//yeah content
$get_user = $dbc->prepare('SELECT user_name, user_face, user_level FROM users WHERE users.user_id = ?');
$get_user->bind_param('s', $_SESSION['user_id']);
$get_user->execute();
$user_result = $get_user->get_result();
$user = $user_result->fetch_assoc();

if (empty($yeah_amount['COUNT(yeah_by)'])) {
    echo '<div id="yeah-content" class="none">';
} else {
    echo '<div id="yeah-content">';
}

if (!checkYeahAdded($reply['reply_id'], 'reply', $_SESSION['user_id'])) {
    echo '
    <a href="/users/'. $user['user_name'] .'/posts" class="icon-container'. ($user['user_level'] > 1 ? ' verified' : '') .' visitor" style="display: none;"><img src="'. printFace($user['user_face'], $reply['feeling_id']) .'" id="icon"></a>';
} else {
    echo '<a href="/users/'. $user['user_name'] .'/posts" class="icon-container'. ($user['user_level'] > 1 ? ' verified' : '') .' visitor">
    <img src="'. printFace($user['user_face'], $reply['feeling_id']) .'" id="icon"></a>';
}

if (!empty($_SESSION['signed_in'])) {
    $yeahs_by = $dbc->prepare('SELECT user_face, user_name, user_level FROM users, yeahs WHERE users.user_id = yeahs.yeah_by AND yeahs.yeah_post = ? AND NOT users.user_id = ? LIMIT 14');
    $yeahs_by->bind_param('ii', $reply['reply_id'], $_SESSION['user_id']);
} else {
    $yeahs_by = $dbc->prepare('SELECT user_face, user_name, user_level FROM users, yeahs WHERE users.user_id = yeahs.yeah_by AND yeahs.yeah_post = ?');
    $yeahs_by->bind_param('i', $reply['reply_id']);
}

$yeahs_by->execute();
$yeahs_by_result = $yeahs_by->get_result();

while ($yeah_by = $yeahs_by_result->fetch_array()) {
    echo '<a href="/users/'. $yeah_by['user_name'] .'/posts" class="icon-container'. ($yeah_by['user_level'] > 1 ? ' verified' : '') .'">
    <img src="' . printFace($yeah_by['user_face'], $reply['feeling_id']) . '" id="icon">
    </a>';
}

echo '</div>';

if ($reply['deleted'] == 0) {
    echo '<div id="post-meta">'. (checkReplyCreator($reply['reply_id'], $_SESSION['user_id']) ? '<button type="button" class="symbol button edit-button edit-reply-button" data-modal-open="#edit-post-page"><span class="symbol-label">Edit</span></button>' : '') .'</div>';
}

?>
</div>
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
                        <option value="delete" data-action="/deletePost.php?postId=<?php echo $reply['reply_id']; ?>&postType=reply" data-track-action="deletePost">Delete</option>
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