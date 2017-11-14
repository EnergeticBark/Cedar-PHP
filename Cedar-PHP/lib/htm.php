<?php
require_once('connect.php');
//This is mainly for storing functions. Using functions is faster than using include/require. I created printHeader() to get rid of header.php, yeah functions to get rid of postLib.php, etc.
function printFace($face, $feeling) {
	if(strpos($face, "imgur") || strpos($face, "cloudinary")){
		return $face;
	} else {
		switch ($feeling) {
		case 0:
          $type = "_normal_face.png";
          break;
        case 1:
          $type = "_happy_face.png";
          break;
        case 2:
          $type = "_like_face.png";
          break;
        case 3:
          $type = "_surprised_face.png";
          break;
        case 4:
          $type = "_frustrated_face.png";
          break;
        case 5:
          $type = "_puzzled_face.png";
          break;
        }
        return 'https://mii-secure.cdn.nintendo.net/'. $face . $type;
	}
}

function printHeader($on_page) { 
	global $dbc;
	global $tabTitle;

	echo '<!DOCTYPE html>
	<head>
	'.(isset($tabTitle) ? '<title>'.$tabTitle.'</title>' : '').'
	<link rel="stylesheet" type="text/css" href="/assets/css/style.css">';

	if(isset($_COOKIE['cedar_color_theme'])){
		$HSL = explode(',', $_COOKIE['cedar_color_theme']);
		echo '<style>
		#global-menu li.selected a:before {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		#global-menu li.selected a {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		#global-menu li a:hover, #global-menu li button:hover {box-shadow: inset 0 -4px 0 -1px hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		#identified-user-banner .title {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		.tab2 a.selected, .tab3 a.selected {background: -webkit-gradient(linear, left top, left bottom, from(hsl('.($HSL[0]+3).','.($HSL[1]-12).'%,'.($HSL[2]+4).'%)), to(hsl('.($HSL[0]+3).','.$HSL[1].'%,'.($HSL[2]-7).'%)));}
		.feeling-selector .feeling-button.checked {color: hsl('.$HSL[0].','.$HSL[1].'%,'.($HSL[2]+14).'%);}
		.user-data h4 span {background-color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		.sidebar-setting .sidebar-menu-post:before {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		.sidebar-setting .sidebar-menu-empathies:before {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		h2.label {border-bottom: 3px solid hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		.sidebar-setting .sidebar-menu-setting:before {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		.sidebar-setting .sidebar-menu-info:before {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		.sidebar-setting .sidebar-menu-replies:before {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		h2.reply-label {background: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);border-top: 1px solid hsl('.$HSL[0].','.$HSL[1].'%,'.($HSL[2]-5).'%);}
		#global-menu #global-my-menu .symbol:before {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		.dialog .window-title {
			border-top: 1px solid hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);border-bottom: 1px solid hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);background: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		#post-meta .yeah-added + .empathy, .reply-meta .yeah-added + .empathy {color: hsl('.($HSL[0]).','.$HSL[1].'%,'.($HSL[2]).'%);}
		#post-meta .yeah-added + .empathy:before, .reply-meta .yeah-added + .empathy:before {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		.news-list a.link {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		.user-sidebar .follow-button:before {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		.user-sidebar .friend-button:before {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		div#activity-feed-tutorial {border: 3px solid hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		div#activity-feed-tutorial h3 {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		.list .toggle-button .follow-button:before {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		#image-header-content .image-header-title .title {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		.list .toggle-button .follow-done-button:before {color: hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);}
		#reply-content .list .my {background-color: hsl('.($HSL[0]+3).','.($HSL[1]-29).'%,'.($HSL[2]+47).'%);}
		#reply-content .list .my:hover {background-color: hsl('.($HSL[0]+3).','.($HSL[1]-29).'%,'.($HSL[2]+46).'%);}
		#reply-content .list .my:active {background-color: hsl('.($HSL[0]+3).','.($HSL[1]-29).'%,'.($HSL[2]+43).'%);}
		@media screen and (max-width: 980px){
		#global-menu li.selected a {
    		border-bottom: 2px solid hsl('.$HSL[0].','.$HSL[1].'%,'.$HSL[2].'%);
		}}
		</style>';
	}

	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<script src="/assets/js/jquery-3.2.1.min.js"></script>
	<script src="/assets/js/pace.min.js"></script>
	<script src="/assets/js/favico.js"></script>
	<script async src="/assets/js/yeah.js"></script>
	<link rel="icon" type="image/png" sizes="96x96" href="/assets/img/favicon-96x96.png">
	<meta property="og:site_name" content="Cedar">
	<meta property="og:type" content="article">
	<script>
	  (function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,\'script\',\'https://www.google-analytics.com/analytics.js\',\'ga\');

	  ga(\'create\', \'UA-104422284-1\', \'auto\');
	  ga(\'send\', \'pageview\');

	</script>
	</head>

	<body>
	  <div id="wrapper">
	    <div id="sub-body">
          <menu id="global-menu">
            <li id="global-menu-logo"><h1><a href="/"><img src="/assets/img/cedar-logo.png" alt="Miiverse" width="120" height="30"></a></h1></li>';


	if(!empty($_SESSION['signed_in'])){
		$get_user = $dbc->prepare('SELECT * FROM users LEFT JOIN titles ON titles.type = 5 WHERE user_id = ? LIMIT 1');
		$get_user->bind_param('i', $_SESSION['user_id']);
		$get_user->execute();
		$user_result = $get_user->get_result();
		$user = $user_result->fetch_assoc();
        
        echo '<li id="global-menu-list">
            <ul>
              <li id="global-menu-mymenu"'.($on_page == 1 ? ' class="selected"' : '').'><a href="/users/'.$user['user_name'].'/posts"><span class="icon-container"><img src="'. printFace($user['user_face'], 0) .'" alt="User Page"></span><span>User Page</span></a></li>
              <li id="global-menu-feed"'.($on_page == 2 ? ' class="selected"' : '').'><a href="/activity" class="symbol"><span>Activity Feed</span></a></li>
              <li id="global-menu-community"'.($on_page == 3 ? ' class="selected"' : '').'><a href="/" class="symbol"><span>Communities</span></a></li>
              <li id="global-menu-news"'.($on_page == 4 ? ' class="selected"' : '').'><a href="/news/my_news" class="symbol"><span class="badge" style="display: none;">0</span></a></li>

              <li id="global-menu-my-menu"><button class="symbol js-open-global-my-menu open-global-my-menu"></button>
                <menu id="global-my-menu" class="invisible none">
                  <li><a href="/settings/profile" class="symbol my-menu-profile-setting"><span>Profile Settings</span></a></li>
                  <li><a href="/settings/account" class="symbol my-menu-miiverse-setting"><span>Cedar Settings</span></a></li>
                  <li><a href="/titles/'.$user['title_id'].'" class="symbol my-menu-info"><span>Cedar Announcements</span></a></li>
                  '.($user['user_level'] > 0 ? '<li><a href="/admin_panel" class="symbol my-menu-miiverse-setting"><span>Admin Panel</span></a></li>' : '').'
                  <li>
                    <form action="/logout" method="post" id="my-menu-logout" class="symbol">
                      <input type="submit" value="Sign out">
                    </form>
                  </li>
                </menu>
              </li></ul></li>';
	} else {
		echo '<li id="global-menu-login"><a href="/login" style="box-shadow: none;"><img alt="Sign in" src="/assets/img/signin_base.png"></a></li>';
	}

	echo '</menu></div>';
}

function notify($to, $type, $post){
	//types 0: post yeah, 1: reply yeah, 2: comment on your post, 3: posters comment, 4: follow.
	global $dbc;
	
	$check_mergedusernews = $dbc->query('SELECT * FROM notifs WHERE notif_by = "'.$_SESSION['user_id'].'" AND notif_to = "'.$to.'" AND notif_type = '.$type.' '.($type != 4 ? 'AND notif_post = '.$post : '').' AND merged IS NOT NULL AND notif_date > NOW() - 7200 ORDER BY notif_date DESC');
	if($check_mergedusernews->num_rows != 0) {
		$result_update_mergedusernewsagain = $dbc->query('UPDATE notifs SET notif_read = "0", notif_date = CURRENT_TIMESTAMP WHERE notif_id = "'.$check_mergedusernews->fetch_assoc()['merged'].'"');
	} else {
		$result_update_newsmergesearch = $dbc->query('SELECT * FROM notifs WHERE notif_to = '.$to.' '.($type != 4 ? 'AND notif_post = '.$post : '').' AND notif_date > NOW() - 7200 AND notif_type = '.$type.' ORDER BY notif_date DESC');	
		if($result_update_newsmergesearch->num_rows != 0) {
			$row_update_newsmergesearch = $result_update_newsmergesearch->fetch_assoc();
			$result_newscreatemerge = $dbc->query('INSERT INTO notifs(notif_by, notif_to, '.($type != 4 ? 'notif_post, ' : '').'merged, notif_type, notif_read) VALUES ("'.$_SESSION['user_id'].'", "'.$to.'", '.($type != 4 ? '"'.$post.'", ' : '').'"'.$row_update_newsmergesearch['notif_id'].'", '.$type.', "0")');
			$result_update_newsformerge = $dbc->query('UPDATE notifs SET notif_read = "0", notif_date = NOW() WHERE notif_id = "'.$row_update_newsmergesearch['notif_id'].'"');
		} else {
			$result_newscreate = $dbc->query('INSERT INTO notifs(notif_by, notif_to, '.($type != 4 ? 'notif_post,' : '').'notif_type, notif_read) VALUES ("'.$_SESSION['user_id'].'", "'.$to.'", '.($type != 4 ? '"'.$post.'",' : '').' '.$type.', "0")'); 	
		}
	}
}

function printPost($post, $reply_pre){
	global $dbc;

	echo '<a href="/users/'. $post['user_name'] .'/posts" class="icon-container'.($post['user_level'] > 1 ? ' verified' : '').'"><img src="'. printFace($post['user_face'], $post['feeling_id']) .'" id="icon"></a>
		<p class="user-name"><a href="/users/'. $post['user_name'] .'/posts">'. htmlspecialchars($post['nickname'], ENT_QUOTES) .'</a></p>
		<p class="timestamp-container"><a class="timestamp" href="/posts/'.$post['id'].'">'.humanTiming(strtotime($post['date_time'])).'</a></p><div id="body">';

	if ($post['deleted'] == 1) {
		echo '<p class="deleted-message">
            Deleted by administrator.<br>
            Post ID: '.$post['id'].'
          </p>';
	}
		
	if (!empty($post['post_image'])) {
		echo '<div class="screenshot-container"><img src="'.$post['post_image'].'"></div>';
	}
		
	echo '<div id="post-body">'.(mb_strlen($post['text']) > 199 ? nl2br(htmlspecialchars(mb_substr($post['text'],0,200), ENT_QUOTES)).'...' : nl2br(htmlspecialchars($post['text'], ENT_QUOTES))).'</div><div id="post-meta">';

	$yeah_count = $dbc->prepare('SELECT COUNT(yeah_by) FROM yeahs WHERE type = "post" AND yeahs.yeah_post = ?');
	$yeah_count->bind_param('i', $post['id']);
	$yeah_count->execute();
	$result_count = $yeah_count->get_result();
	$yeah_amount = $result_count->fetch_assoc();
					
	echo '<button class="yeah symbol '. (!empty($_SESSION['signed_in']) ? (checkYeahAdded($post['id'], 'post', $_SESSION['user_id']) ? 'yeah-added' : '') : '') .'" '. (!empty($_SESSION['signed_in']) && !checkPostCreator($post['id'], $_SESSION['user_id']) ? '' : 'disabled') .' id="'. $post['id'] .'" data-track-label="post"><span class="yeah-button-text">'. (!empty($_SESSION['signed_in']) ? (checkYeahAdded($post['id'], 'post', $_SESSION['user_id']) ? 'Unyeah' : 'Yeah!') : 'Yeah!') .'</span></button>
		<div class="empathy symbol"><span class="yeah-count">' . $yeah_amount['COUNT(yeah_by)'] . '</span></div>';
		
	$reply_count = $dbc->prepare('SELECT COUNT(reply_id) FROM replies WHERE reply_post = ? AND deleted = 0');
	$reply_count->bind_param('i', $post['id']);
	$reply_count->execute();
	$result_count = $reply_count->get_result();
	$reply_amount = $result_count->fetch_assoc();
		
	echo '<div class="reply symbol"><span id="reply-count">'.$reply_amount['COUNT(reply_id)'].'</span></div></div>';

	if ($post['deleted'] == 0) {

		if ($reply_pre == 1){
	        $search_replies = $dbc->prepare('SELECT users.*, replies.* FROM replies LEFT JOIN users ON user_id = reply_by_id WHERE reply_post = ? AND deleted = 0 ORDER BY date_time DESC LIMIT 1');
	        $search_replies->bind_param('i', $post['id']);
	        $search_replies->execute();
	        $replies_result = $search_replies->get_result();
	        $replies = $replies_result->fetch_assoc();

	        if (!$reply_amount['COUNT(reply_id)'] == 0){
	        	echo '<div class="recent-reply-content">
	        	'.($reply_amount['COUNT(reply_id)']>1?'<div class="recent-reply-read-more-container" data-href="/posts/'.$post['id'].'" tabindex="0">View More Comments ('.($reply_amount['COUNT(reply_id)']-1).')</div>':'').'
	        	<div class="recent-reply trigger"><a href="/users/'.$replies['user_name'].'/posts" class="icon-container'.($replies['user_level']==2?' verified':'').'"><img src="'.printFace($replies['user_face'], $replies['feeling_id']).'" id="icon"></a>
	        	<p class="user-name"><a href="/users/'.$replies['user_name'].'/posts">'. htmlspecialchars($replies['nickname'], ENT_QUOTES) .'</a></p>
	        	<p class="timestamp-container"><a class="timestamp" href="/posts/'.$post['id'].'">'.humanTiming(strtotime($replies['date_time'])).'</a></p>
	        	<div id="body"><div class="post-content"><p class="recent-reply-content-text">'.$replies['text'].'</p></div></div></div></div>';
	        }
	    }
	}

	if ($reply_pre == 1) {
		echo '</div></div>';
	}
}



function checkPostCreator($post, $user_id){
	global $dbc;
	
	$check_posted = $dbc->prepare('SELECT * FROM posts WHERE posts.id = ? AND posts.post_by_id = ? LIMIT 1');
	$check_posted->bind_param('ss', $post, $user_id);
	$check_posted->execute();
    $posted_result = $check_posted->get_result();
	
	if (!$posted_result->num_rows == 0){
		return true;
	} else {
		return false;
	}
}

function checkReplyCreator($reply, $user_id){
	global $dbc;
	
	$check_posted = $dbc->prepare('SELECT * FROM replies WHERE replies.reply_id = ? AND replies.reply_by_id = ? LIMIT 1');
	$check_posted->bind_param('ss', $reply, $user_id);
	$check_posted->execute();
    $posted_result = $check_posted->get_result();
	
	if (!$posted_result->num_rows == 0){
		return true;
	} else {
		return false;
	}
}


function checkYeahAdded($post, $type, $user_id){
	global $dbc;
	
	$check_yeahed = $dbc->prepare('SELECT * FROM yeahs WHERE yeahs.yeah_post = ? AND yeahs.type = ? AND yeahs.yeah_by = ?');
	$check_yeahed->bind_param('sss', $post, $type, $user_id);
	$check_yeahed->execute();
    $yeahed_result = $check_yeahed->get_result();
	
	if (!$yeahed_result->num_rows == 0){
		return true;
	} else {
		return false;
	}
}

function checkPostExists($post){
	global $dbc;
	
	$check_post = $dbc->prepare('SELECT * FROM posts WHERE id = ? LIMIT 1');
	$check_post->bind_param('s', $post);
	$check_post->execute();
    $post_result = $check_post->get_result();
	
	if (!$post_result->num_rows == 0){
		return true;
	} else {
		return false;
	}
}

function checkReplyExists($reply){
	global $dbc;
	
	$check_post = $dbc->prepare('SELECT * FROM replies WHERE reply_id = ? LIMIT 1');
	$check_post->bind_param('s', $reply);
	$check_post->execute();
    $post_result = $check_post->get_result();
	
	if (!$post_result->num_rows == 0){
		return true;
	} else {
		return false;
	}
}

function printTitleInfo($title){
	echo '<li class="trigger test-community-list-item " data-href="/titles/'.$title['title_id'].'" tabindex="0">
	  <img src="'.$title['title_banner'].'" class="community-list-cover">
	  <div class="community-list-body">
		<span class="icon-container"><img src="'.$title['title_icon'].'" id="icon"></span>
		<div class="body">
		  <a class="title" href="/titles/'.$title['title_id'].'" tabindex="-1">'.$title['title_name'].'</a>';

    switch ($title['type']) {
    	case 1:
		  echo '<span class="platform-tag"><img src="/assets/img/platform-tag-wiiu.png"></span>';
		  break;
		case 2:
		  echo '<span class="platform-tag"><img src="/assets/img/platform-tag-3ds.png"></span>';
		  break;
		case 3:
		  echo '<span class="platform-tag"><img src="/assets/img/platform-tag-wiiu-3ds.png"></span>';
		  break;
		case 4:
		  echo '<span class="platform-tag"><img src="/assets/img/platform-tag-switch.png"></span>';
		  break;
    }

	echo '<span class="text">';

	switch ($title['type']) {
		case 0:
		  echo 'General Community';
		  break;
		case 1:
    	  echo 'Wii U Games';
    	  break;
		case 2:
    	  echo '3DS Games';
    	  break;
		case 3:
    	  echo 'Wii U Games・3DS Games';
    	  break;
		case 4:
    	  echo 'Switch Games';
		  break;
		default:
		  echo 'Special Community';
	}

	echo '</span>
	</div>
	</div>
	</li>';
}

function printReply($reply){
	global $dbc;
	$yeah_count = $dbc->prepare('SELECT COUNT(yeah_by) FROM yeahs WHERE type = "reply" AND yeah_post = ?');
	$yeah_count->bind_param('i', $reply['reply_id']);
	$yeah_count->execute();
    $result_count = $yeah_count->get_result();
	$yeah_amount = $result_count->fetch_assoc();

	echo '<a href="/users/'.$reply['user_name'].'/posts" class="icon-container'.($reply['user_level']>1?' verified':'').'">
	<img src="'.printFace($reply['user_face'], $reply['feeling_id']).'" id="icon"></a><div class="body"><div class="header">
	<p class="user-name"><a href="/users/'.$reply['user_name'].'/posts">'. htmlspecialchars($reply['nickname'], ENT_QUOTES) .'</a></p>
	<p class="timestamp-container"><a class="timestamp" href="/replies/'.$reply['reply_id'].'">'.humanTiming(strtotime($reply['date_time'])).'</a></p>
	</div>';

	if ($reply['deleted'] == 1) {
		echo '<p class="deleted-message">
            Deleted by administrator.<br>
            Reply ID: '.$reply['reply_id'].'
          </p>';
    }
    if ($reply['deleted'] == 0 || $reply['reply_by_id'] == $_SESSION['user_id']) {
    	echo '<p class="reply-content-text">'.$reply['text'].'</p>'.(!empty($reply['reply_image'])?'<div class="screenshot-container"><img src="'.$reply['reply_image'].'"></div>':'').'<div class="reply-meta"><button class="yeah symbol '.(checkYeahAdded($reply['reply_id'], 'reply', $_SESSION['user_id'])?'yeah-added':'').'" '.(!empty($_SESSION['signed_in']) && !checkReplyCreator($reply['reply_id'], $_SESSION['user_id'])?'':'disabled').' id="'.$reply['reply_id'].'" data-track-label="reply"><span class="yeah-button-text">'.(checkYeahAdded($reply['reply_id'], 'reply', $_SESSION['user_id'])?'Unyeah':'Yeah!').'</span></button><div class="empathy symbol"><span class="yeah-count">'.$yeah_amount['COUNT(yeah_by)'].'</span></div></div>';
    }
    echo '</div></li>';
}

function get_percentage($total, $number){if($total>0){return round($number/($total/100),2);}else{return 0;}}

function humanTiming($time){
	if(time() - $time >= 345600){
		return date("m/d/Y g:i A", $time);
	}
	$time = time() - $time;
	if (strval($time) < 1) {
		$time = 1;
	}
	if ($time <= 59){
		return 'Less than a minute ago';
	}
	$tokens = array(86400 => 'day', 3600 => 'hour', 60 => 'minute');
	foreach ($tokens as $unit => $text){
		if($time < $unit) continue;
		$numberOfUnits = floor($time / $unit);
		return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':''). ' ago';
	}
}