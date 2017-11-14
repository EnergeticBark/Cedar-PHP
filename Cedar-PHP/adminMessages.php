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
echo '</div><div class="main-column"><div class="post-list-outline"><h2 class="label">Messages Exchanged with Cedar Administration</h2><div class="list admin-messages">';

$get_admin_messages = $dbc->prepare('SELECT * FROM admin_messages WHERE admin_to = ? ORDER BY admin_date DESC LIMIT 50');
$get_admin_messages->bind_param('i', $_SESSION['user_id']);
$get_admin_messages->execute();
$admin_messages_result = $get_admin_messages->get_result();

while($admin_message = $admin_messages_result->fetch_array()){
	echo '<div class="post scroll other'.($admin_message['admin_read'] == 0 ? ' notify' : '').'">
        <p class="timestamp-container">
          <span class="timestamp">'.humanTiming(strtotime($admin_message['admin_date'])).'</span>
        </p>
        <div class="post-body">
          <p class="post-content">';

          switch ($admin_message['admin_type']) {
        case 0:
          echo $admin_message['admin_text'];
          break;
        case 1:
          echo 'Your '.($admin_message['is_reply'] == 0 ? 'post' : 'reply').' was identified as spam, so it was removed. Continued violations may result in restrictions on your use of Cedar.';
          break;
        case 2:
          echo 'Your '.($admin_message['is_reply'] == 0 ? 'post' : 'reply').' contained sexually explicit content, so it was removed. Continued violations may result in restrictions on your use of Cedar.';
          break;
        }


          echo '</p>
          <div id="post-meta">
            <a href="/'.($admin_message['is_reply'] == 0 ? 'posts' : 'replies').'/'.$admin_message['admin_post'].'">View '.($admin_message['is_reply'] == 0 ? 'Post' : 'Reply').'</a>
          </div>
          
        </div>
      </div>';
}

$dbc->query('UPDATE admin_messages SET admin_read = "1" WHERE admin_to = '.$_SESSION['user_id'].'');