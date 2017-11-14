<?php
require_once('lib/htm.php');
require_once('lib/htmUsers.php');

$tabTitle = 'Cedar - Community List(All Communities)';

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

echo '
<div class="main-column">
  <div class="post-list-outline">
    <div class="body-content" id="community-top" data-region="USA">
      <h2 class="label">All Communities</h2>
      <ul class="list community-list">';

$get_titles = $dbc->prepare('SELECT * FROM titles');
$get_titles->execute();
$titles_result = $get_titles->get_result();

while ($titles = $titles_result->fetch_assoc()){
	printTitleInfo($titles);
}