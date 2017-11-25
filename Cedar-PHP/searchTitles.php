<?php
require_once('lib/htm.php');
require_once('lib/htmUsers.php');

if(!isset($_GET['query'])){
	echo 'cant be empty fago';
} else {

	if (strlen($_GET['query']) < 2 || strlen($_GET['query']) > 20) {
		echo 'either too big or too small lmao'; 
	} else {

		$tabTitle = 'Cedar - Search Communities';

		printHeader(3);

		echo '<div id="main-body">';

		if(!empty($_SESSION['signed_in'])){
			$get_user = $dbc->prepare('SELECT * FROM users WHERE user_id = ? LIMIT 1');
			$get_user->bind_param('i', $_SESSION['user_id']);
			$get_user->execute();
			$user_result = $get_user->get_result();
			$user = $user_result->fetch_assoc();
			echo '<div id="sidebar" class="user-sidebar">';
			userContent($user, "");
			echo '</div>'; 
		}

		echo '
		<div class="main-column">
		  <div class="post-list-outline">
		    <h2 class="label">Search Communities</h2>
		    <form method="GET" action="/titles/search" class="search">
		      <input type="text" name="query" placeholder="Mario, etc." minlength="2" maxlength="20">
		      <input type="submit" value="q" title="Search">
		    </form>';

		$query = '%/'. htmlspecialchars($_GET['query']) .'%';

		$get_searched_titles = $dbc->prepare('SELECT * FROM titles WHERE title_name LIKE ? ESCAPE "/"');
		$get_searched_titles->bind_param('s', $query);
		$get_searched_titles->execute();
		$searched_titles_result = $get_searched_titles->get_result();

		if ($searched_titles_result->num_rows == 0) {
			echo '<div class="search-content no-content"><p>No communities found for<br>"'. htmlspecialchars($_GET['query'], ENT_QUOTES) .'." Please try again.</p></div>';
		} else {
			echo '
			<div class="search-content">
			  <p class="note">Communities found for "'. htmlspecialchars($_GET['query'], ENT_QUOTES) .'."</p>
			  <ul class="list community-list community-title-list">';

			while ($titles = $searched_titles_result->fetch_assoc()){
				printTitleInfo($titles);
			}
		}
	}
}