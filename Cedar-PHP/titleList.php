<?php
require_once('lib/htm.php');
require_once('lib/connect.php');

$tabTitle = 'Cedar - Community List';

printHeader(3);
?>

<div id="main-body">
	<div class="community-top-sidebar">
		<form method="GET" action="/titles/search" class="search">
			<input type="text" name="query" placeholder="Search Communities" minlength="2" maxlength="20">
			<input type="submit" value="q" title="Search">
		</form>

		<div id="identified-user-banner" style="margin-bottom: 10px;">
			<a href="/identified_user_posts" data-pjax="#body" class="list-button us">
				<span class="title">Get the latest news here!</span>
				<span class="text">Posts from Verified Users</span>
			</a>
		</div>

		<div class="post-list-outline" style="text-align: center">
			<h2 class="label">What is Cedar?</h2>
			<p style="width: 90%; display: inine-block; padding: 10px;">Cedar is a Miiverse clone written by Seth and Eric. Cedar is open source: https://github.com/EnergeticBark/Cedar-PHP</p>
		</div>

		<button type="button" onclick="window.location.href='/titles/new'" class="symbol button create-button" style="padding: 14px 60px;">
			<span class="favorite-button-text">Create Community</span>
		</button>
		<br>
		<button type="button" onclick="window.location.href='/communities/categories/all'" class="symbol button create-button" style="padding: 14px 60px;">
			<span class="favorite-button-text">All Communities</span>
		</button>
	</div>
	<div class="community-main">

<?php

if (!empty($_SESSION['signed_in'])) {

	echo '<h3 class="community-title symbol community-favorite-title">Favorite Communities</h3>';

	$get_fav_titles = $dbc->prepare('SELECT titles.title_id, titles.title_icon FROM titles, favorite_titles WHERE titles.title_id = favorite_titles.title_id AND favorite_titles.user_id = ? ORDER BY favorite_titles.fav_id DESC');
	$get_fav_titles->bind_param('i', $_SESSION['user_id']);
	$get_fav_titles->execute();
	$fav_titles_result = $get_fav_titles->get_result();
	if ($fav_titles_result->num_rows == 0) {
		echo '
	  <div class="no-content no-content-favorites">
		<div>
		  <p>Tap the ☆ button on a community\'s page to have it show up as a favorite community here.</p>
		  <a href="/communities/favorites" class="favorite-community-link symbol"><span class="symbol-label">Show More</span></a>
        </div>
      </div>';
    } else {
    	echo '<div class="card" id="community-favorite"><ul>';

    	$empty_space = 0;

    	while ($fav_titles = $fav_titles_result->fetch_assoc()){
    		echo '<li class="test-favorite-community">
    		<a href="/titles/'. $fav_titles['title_id'] .'" class="icon-container"><img src="'. $fav_titles['title_icon'] .'" id="icon"></a></li>';
    		$empty_space++;
    	}

    	for ($i = 8; $i > $empty_space; $i--) {
    		echo '<li class="test-favorite-empty-placeholder"><span class="empty-icon"><img src="/assets/img/empty.png" alt="empty"></span></li>';
    	}
    	echo '
    	<li class="read-more">
          <a href="/communities/favorites" class="favorite-community-link symbol"><span class="symbol-label">Show More</span></a>
        </li>
      </ul>
    </div>';
	}
}

//Popular communities (these aren't dynamic so you have to change them right here)
echo '
<h3 class="community-title symbol">Popular Communities</h3>
<div>
  <ul class="list community-list community-card-list test-hot-communities">';

$get_pop_titles = $dbc->prepare('SELECT * FROM titles INNER JOIN (SELECT COUNT(id) AS FUCK_SQL, post_title FROM posts GROUP BY post_title) AS ok ON post_title = title_id WHERE title_id IN (SELECT post_title FROM posts GROUP BY post_title) ORDER BY FUCK_SQL DESC LIMIT 4');
$get_pop_titles->execute();
$pop_titles_result = $get_pop_titles->get_result();
while ($pop_titles = $pop_titles_result->fetch_assoc()){
	printTitleInfo($pop_titles);
}

echo '
  </ul>
</div>

<h3 class="community-title"><span>Official Communities</span></h3>
<div>
  <ul class="list community-list community-card-list device-new-community-list">';

$get_titles = $dbc->prepare('SELECT * FROM titles WHERE user_made = 0 LIMIT 6');
$get_titles->execute();
$titles_result = $get_titles->get_result();

while ($titles = $titles_result->fetch_assoc()){
	printTitleInfo($titles);
}

echo '
</ul>';

echo '
<h3 class="community-title"><span>User-Created Communities</span></h3>
<div>
  <ul class="list community-list community-card-list device-new-community-list">';

$get_titles = $dbc->prepare('SELECT * FROM titles WHERE user_made = 1 LIMIT 6');
$get_titles->execute();
$titles_result = $get_titles->get_result();

while ($titles = $titles_result->fetch_assoc()){
	printTitleInfo($titles);
}

echo '
</ul>
</div>';

?>
</div>
