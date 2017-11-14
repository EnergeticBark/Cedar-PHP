<?php
require_once('lib/htm.php');
require_once('lib/htmUsers.php');

$tabTitle = 'Cedar - Notifications';

printHeader(4);

echo '<div id="main-body">';
$get_user = $dbc->prepare('SELECT * FROM users WHERE user_id = ? LIMIT 1');
$get_user->bind_param('i', $_SESSION['user_id']);
$get_user->execute();
$user_result = $get_user->get_result();
$user = $user_result->fetch_assoc();
echo '<div id="sidebar" class="general-sidebar">';
userContent($user, "");
sidebarSetting();
echo '</div><div class="main-column"><div class="post-list-outline"><h2 class="label">Notifications</h2><div class="list news-list">';

$get_notifs = $dbc->prepare('SELECT * FROM notifs WHERE notif_to = ? AND merged IS NULL ORDER BY notif_date DESC LIMIT 50');
$get_notifs->bind_param('i', $_SESSION['user_id']);
$get_notifs->execute();
$notifs_result = $get_notifs->get_result();

if(!$notifs_result->num_rows == 0){

	while($notif = $notifs_result->fetch_array()){

		if ($notif['notif_type'] != 5){

			$user = mysqli_fetch_assoc(mysqli_query($dbc, 'SELECT * FROM users WHERE user_id = ' . $notif['notif_by']));

			if ($notif['notif_type'] == 0 || $notif['notif_type'] == 2 || $notif['notif_type'] == 3){
				$post = mysqli_fetch_assoc(mysqli_query($dbc, 'SELECT text FROM posts WHERE id = '.$notif['notif_post'].''));
				$notifurl = '/posts/' . $notif['notif_post'];
			} elseif ($notif['notif_type'] == 1) {
				$post = mysqli_fetch_assoc(mysqli_query($dbc, 'SELECT text FROM replies WHERE reply_id = '.$notif['notif_post'].''));
				$notifurl = '/replies/' . $notif['notif_post'];
			} else {
				$notifurl = '/users/'.$user['user_name'].'/posts';
			}
		} else {
			$notifurl = '/admin_messages';
		}

		echo '<div class="news-list-content'.($notif['notif_read'] == 0 ? ' notify' : '').' trigger" tabindex="0" data-href="'.$notifurl.'">
		  <a href="/users/'. $user['user_name'] .'/posts" class="icon-container"><img src="'.($notif['notif_type'] == 5?'/img/miiverse-administrator.png':printFace($user['user_face'], 0)).'" id="icon"></a>
		<div class="body">';

		if ($notif['notif_type'] == 5){
			echo '
			<p class="title">
			  <span class="nick-name">Cedar Administration</span>
			  <span class="id-name">cedar_admin</span></p>
			<p class="text">You have received a notification from the Cedar administrators.';
		} else {

			echo ($notif['notif_type'] == 4 ? 'Followed by ' : '').'<a href="/users/'. $user['user_name'] .'/posts" class="nick-name">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'</a>';

			$find_merged_notifs = $dbc->prepare('SELECT * FROM notifs WHERE merged = ? AND notif_by != ? GROUP BY notif_by ORDER BY notif_date LIMIT 20');
			$find_merged_notifs->bind_param('ii', $notif['notif_id'], $notif['notif_by']);
			$find_merged_notifs->execute();
			$merged_notifs_result = $find_merged_notifs->get_result();
			$merged_notifs = array();

			while($merged_notifs[] = $merged_notifs_result->fetch_assoc());

			if ($merged_notifs_result->num_rows != 0){
				$user = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM users WHERE user_id = " . $merged_notifs[0]['notif_by']));

				if ($merged_notifs_result->num_rows == 1){
					echo ' and <a href="/users/'. $user['user_name'] .'/posts" class="nick-name">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'</a>';
				} elseif ($merged_notifs_result->num_rows == 2){
					echo ', <a href="/users/'. $user['user_name'] .'/posts" class="nick-name">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'</a>';
					$user = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM users WHERE user_id = " . $merged_notifs[1]['notif_by']));
					echo ', and <a href="/users/'. $user['user_name'] .'/posts" class="nick-name">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'</a>';
				} elseif ($merged_notifs_result->num_rows == 3){
					echo ', <a href="/users/'. $user['user_name'] .'/posts" class="nick-name">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'</a>';
					$user = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM users WHERE user_id = " . $merged_notifs[1]['notif_by']));
					echo ', <a href="/users/'. $user['user_name'] .'/posts" class="nick-name">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'</a> and one other person';
				} else {
					echo ', <a href="/users/'. $user['user_name'] .'/posts" class="nick-name">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'</a>';
					$user = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM users WHERE user_id = " . $merged_notifs[1]['notif_by']));
					echo ', <a href="/users/'. $user['user_name'] .'/posts" class="nick-name">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'</a>, and '.bcsub($merged_notifs_result->num_rows, 2).' others';
				}
			}

			if ($notif['notif_type'] == 0){
				echo ' gave <a href="'.$notifurl.'" class="link">your post&nbsp;('. (mb_strlen($post['text']) > 16 ? htmlspecialchars(mb_substr($post['text'],0,17), ENT_QUOTES) .'...' : htmlspecialchars($post['text'], ENT_QUOTES)) .')</a> a Yeah';
			} elseif ($notif['notif_type'] == 1){
				echo ' gave <a href="'.$notifurl.'" class="link">your Comment&nbsp;('. (mb_strlen($post['text']) > 16 ? mb_substr($post['text'],0,17) .'...' : $post['text']) .')</a> a Yeah';
			} elseif ($notif['notif_type'] == 2){
				echo ' commented on <a href="'.$notifurl.'" class="link">your post&nbsp;('. (mb_strlen($post['text']) > 16 ? htmlspecialchars(mb_substr($post['text'],0,17), ENT_QUOTES) .'...' : htmlspecialchars($post['text'], ENT_QUOTES)) .')</a>';
			} elseif ($notif['notif_type'] == 3){
				echo ' commented on <a href="'.$notifurl.'" class="link">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'\'s post&nbsp;('. (mb_strlen($post['text']) > 16 ? htmlspecialchars(mb_substr($post['text'],0,17), ENT_QUOTES) .'...' : htmlspecialchars($post['text'], ENT_QUOTES)) .')</a>';
			}

			echo '.';

			$following = mysqli_query($dbc, 'SELECT * FROM follows WHERE follow_by = '.$_SESSION['user_id'].' AND follow_to = '.$notif['notif_by'].' LIMIT 1');

			if ($notif['notif_type'] == 4 && $following->num_rows == 0){
				echo '<div class="toggle-button"><button type="button" data-user-id="'.$notif['notif_by'].'" class="follow-button button symbol relationship-button" data-community-id="" data-url-id="" data-track-label="user" data-title-id="" data-track-action="follow" data-track-category="follow">Follow</button>
				<button type="button" class="button follow-done-button relationship-button symbol none" disabled="">Follow</button></div>';
			}
		}

		echo '<span class="timestamp"> '. humanTiming(strtotime($notif['notif_date'])) .'</span></div></div>';
	}

	$dbc->query('UPDATE notifs SET notif_read = "1" WHERE notif_to = '.$_SESSION['user_id'].'');

} else {
	echo '<div id="user-page-no-content" class="no-content"><div><p>No updates.</p></div></div>';
}