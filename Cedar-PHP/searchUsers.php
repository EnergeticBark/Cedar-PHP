<?php
require_once('lib/htm.php');
require_once('lib/htmUsers.php');

if(!isset($_GET['query'])){
	echo 'cant be empty fago';
} else {

	if (strlen($_GET['query']) < 1 || strlen($_GET['query']) > 16) {
		echo 'either too big or too small lmao'; 
	} else {

		if(isset($_GET['offset']) && is_numeric($_GET['offset'])){
			$offset = ($_GET['offset'] * 50);

			$get_searched_users = $dbc->prepare('SELECT * FROM users INNER JOIN profiles ON profiles.user_id = users.user_id WHERE user_name LIKE ? ESCAPE "/" OR nickname LIKE ? ESCAPE "/" ORDER BY user_name DESC LIMIT 50 OFFSET ?');
			$get_searched_users->bind_param('ssi', $query, $query, $offset);
			$get_searched_users->execute();
			$searched_users_result = $get_searched_users->get_result();
		} else {

			$tabTitle = 'Cedar - Search Users';
			printHeader('');

			echo '<script>var loadOnScroll=true;</script>
			<div id="main-body">';

			if(!empty($_SESSION['signed_in'])){
				$get_user = $dbc->prepare('SELECT * FROM users WHERE user_id = ? LIMIT 1');
				$get_user->bind_param('i', $_SESSION['user_id']);
				$get_user->execute();
				$user_result = $get_user->get_result();
				$user = $user_result->fetch_assoc();
				echo '<div id="sidebar" class="general-sidebar">';
				userContent($user, "");
				echo '</div>'; 
			}

			echo '<div class="main-column"><div class="post-list-outline"><h2 class="label">Search Users</h2>
			<form class="search user-search" action="/users" method="GET">
			  <input type="text" name="query" value="'.htmlspecialchars($_GET['query'], ENT_QUOTES).'" placeholder="Seth, CedarSeth, etc." minlength="1" maxlength="16">
			  <input type="submit" value="q" title="Search">
			</form>';

			$query = '%/'. htmlspecialchars($_GET['query']) .'%';

			$get_searched_users = $dbc->prepare('SELECT * FROM users INNER JOIN profiles ON profiles.user_id = users.user_id WHERE user_name LIKE ? ESCAPE "/" OR nickname LIKE ? ESCAPE "/" ORDER BY user_name DESC LIMIT 50');
			$get_searched_users->bind_param('ss', $query, $query);
			$get_searched_users->execute();
			$searched_users_result = $get_searched_users->get_result();

			if ($searched_users_result->num_rows == 0){
				echo '<div class="search-user-content no-content search-content">
				  <div class="search-content no-title-content">
				    <p>"'.htmlspecialchars($_GET['query'], ENT_QUOTES).'" could not be found.<br>
				    Select Retry Search if you want to try again.</p>
				  </div>
				</div>';
			} else {
				echo '<div class="search-user-content search-content">
				  <p class="user-found note">Found: '.htmlspecialchars($_GET['query'], ENT_QUOTES).'</p>
				    <div class="list">
				      <ul id="searched-user-list" class="list-content-with-icon-and-text arrow-list" data-next-page-url="/users?query='.$_GET['query'].'&offset=1">';
			}
		}

		while ($users = $searched_users_result->fetch_assoc()){

			echo '<li class="trigger" data-href="/users/'.$users['user_name'].'/posts">
			  <a href="/users/'.$users['user_name'].'/posts" class="icon-container"><img src="'.printFace($users['user_face'], 0).'" id="icon"></a>

			  <div class="body">
				<p class="title">
      			  <span class="nick-name"><a href="/users/'.$users['user_name'].'/posts">'.$users['nickname'].'</a></span>
      			  <span class="id-name">'.$users['user_name'].'</span>
    			</p>
    			<p class="text">'.$users['bio'].'</p>
    		  </div>
    		</li>';
    	}
    }
}