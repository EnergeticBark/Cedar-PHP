<?php 
require_once('lib/htm.php');

if((isset($_GET['offset']) && is_numeric($_GET['offset'])) && isset($_GET['date'])){
  $offset = ($_GET['offset'] * 20);
  $date = htmlspecialchars($_GET['date']);

  $get_posts = $dbc->prepare('SELECT posts.*, users.*, COUNT(yeah_id) AS yeah_count FROM posts INNER JOIN users ON user_id = post_by_id LEFT JOIN yeahs ON yeah_post = posts.id WHERE post_title = ? AND posts.deleted = 0 AND posts.date_time >= ? - INTERVAL 2 DAY AND posts.date_time <= ? GROUP BY posts.id ORDER BY yeah_count DESC LIMIT 20 OFFSET ?');
  $get_posts->bind_param('issi', $title_id, $date, $date, $offset);

} else {

	if (isset($_GET['date'])){
		$date = htmlspecialchars($_GET['date']);
	} else {
		$date = date("Y-m-d");
	}

	printHeader(3);

	$get_title = $dbc->prepare('SELECT * FROM titles WHERE title_id = ?');
	$get_title->bind_param('i', $title_id);
	$get_title->execute();
	$title_result = $get_title->get_result();

	if($title_result->num_rows == 0){
		exit("<br/>Could not find community");
	} else {
		$title = $title_result->fetch_array();
		echo '<script>var loadOnScroll=true;</script><title>Cedar - '. $title['title_name'] .'</title>
		<meta property="og:image" content="'. $title['title_icon'] .'">
		<meta property="og:url" content="https://suckmyass.000webhostapp.com/titles/'. $title['title_id'] .'">
		<meta property="og:description" content="Cedar is some gay site by eric and seth lmao">
		<meta property="og:title" content="'. $title['title_name'] .' - Cedar">
		<div id="main-body">
		  <div id="sidebar">
		    <section class="sidebar-container" id="sidebar-community">
		      <span id="sidebar-cover">
		        <a href="/titles/'. $title['title_id'] .'">
		          <img src="'. $title['title_banner'] .'">
		        </a>
		      </span>
		      <header id="sidebar-community-body">
		        <span id="sidebar-community-img">
		          <span class="icon-container">
		            <a href="/titles/'. $title['title_id'] .'">
		              <img src="'. $title['title_icon'] .'" id="icon">
		            </a>
		          </span>
		          <span class="platform-tag">';

        switch ($title['type']) {
        	case 1:
	          echo '<img src="/img/platform-tag-wiiu.png">';
	          break;
	        case 2:
	          echo '<img src="/img/platform-tag-3ds.png">';
	          break;
	        case 3:
	          echo '<img src="/img/platform-tag-wiiu-3ds.png">';
	          break;
	        case 4:
	          echo '<img src="/img/platform-tag-switch.png">';
	          break;
        }

        echo '</span>
        </span>
        <h1 class="community-name"><a href="/titles/'. $title['title_id'] .'">'. $title['title_name'] .'</a></h1>
        </header>
        <div class="community-description js-community-description">
          <p class="text js-truncated-text">'. $title['title_desc'] .'</p>
        </div>';

        if(!empty($_SESSION['signed_in'])) {
        	echo '<button type="button" class="symbol button favorite-button';

        	$check_favorite = $dbc->prepare('SELECT * FROM favorite_titles WHERE user_id = ? AND title_id = ? LIMIT 1');
        	$check_favorite->bind_param('ii', $_SESSION['user_id'], $title['title_id']);
        	$check_favorite->execute();
        	$favorite_result = $check_favorite->get_result();

        	if (!$favorite_result->num_rows == 0){
        		echo ' checked';
        	}

        	echo '" data-title-id="'. $title['title_id'] .'"><span class="favorite-button-text">Favorite</span></button>';
        }

        echo '<div class="sidebar-setting">
          <div class="sidebar-post-menu">
          </div>
        </div>
        </section></div><div class="main-column"><div class="post-list-outline"><div id="posts-filter-tab-container" class="tab-container ">
        <div class="tab2"><a id="posts-filter-anchor" href="/titles/'. $title['title_id'] .'"><span class="new-posts">All Posts</span></a>
        <a class="selected" href="/titles/'. $title['title_id'] .'/hot">Popular posts</a></div></div><div class="pager-button">';

        if (date("Y-m-d", strtotime($date)) < date("Y-m-d")){
        	echo '<a href="/titles/'. $title['title_id'] .'/hot?date='. date("Y-m-d", strtotime($date . '+1 day')) .'" class="button back-button symbol"><span class="symbol-label">←</span></a>';
        }
        
        echo '<a href="/titles/'. $title['title_id'] .'/hot?date='. $date .'" class="button selected">'. date("m/d/Y", strtotime($date)) .'</a><a href="/titles/'. $title['title_id'] .'/hot?date='. date("Y-m-d", strtotime($date . '-1 day')) .'" class="button next-button symbol"><span class="symbol-label">→</span></a></div>';

        $get_posts = $dbc->prepare('SELECT posts.*, users.*, COUNT(yeah_id) AS yeah_count FROM posts INNER JOIN users ON user_id = post_by_id LEFT JOIN yeahs ON yeah_post = posts.id WHERE post_title = ? AND posts.deleted = 0 AND posts.date_time >= ? - INTERVAL 2 DAY AND posts.date_time <= ? GROUP BY posts.id ORDER BY yeah_count DESC LIMIT 20');
        $get_posts->bind_param('iss', $title_id, $date, $date);
        echo '<div class="list post-list" data-next-page-url="/titles/'. $title['title_id'] .'/hot?offset=1&date='. $date .'">';
    }
}

$get_posts->execute();
$posts_result = $get_posts->get_result();

if(!$posts_result->num_rows == 0){
	
	while($row = $posts_result->fetch_array()){
		
		echo '<div class="post trigger" data-href="/posts/' . $row['id'] . '">';

		printPost($row, 1);		
	}
	echo '</div></div>';
} else {
	if(!(isset($_GET['offset']) && is_numeric($_GET['offset']) && isset($_GET['date']))){
		echo '<div class="no-content">
		  <div>
		    <p>There are no popular posts.</p>
		  </div>
		</div>';
	}
}