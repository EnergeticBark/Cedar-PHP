<?php

function userSidebarSetting($user, $page)
{
    global $dbc;

    $post_count = $dbc->prepare('SELECT COUNT(id) FROM posts WHERE post_by_id = ?');
    $post_count->bind_param('i', $user['user_id']);
    $post_count->execute();
    $result_count = $post_count->get_result();
    $post_amount = $result_count->fetch_assoc();

    $reply_count = $dbc->prepare('SELECT COUNT(reply_by_id) FROM replies WHERE reply_by_id = ?');
    $reply_count->bind_param('i', $user['user_id']);
    $reply_count->execute();
    $result_count = $reply_count->get_result();
    $reply_amount = $result_count->fetch_assoc();

    $yeah_count = $dbc->prepare('SELECT COUNT(yeah_by) FROM yeahs WHERE yeah_by = ?');
    $yeah_count->bind_param('i', $user['user_id']);
    $yeah_count->execute();
    $result_count = $yeah_count->get_result();
    $yeah_amount = $result_count->fetch_assoc();

    $nah_count = $dbc->prepare('SELECT COUNT(nah_by) FROM nahs WHERE nah_by = ?');
    $nah_count->bind_param('i', $user['user_id']);
    $nah_count->execute();
    $result_count = $nah_count->get_result();
    $nah_amount = $result_count->fetch_assoc();

    echo '<div class="sidebar-setting sidebar-container">
    <div class="sidebar-post-menu">
      <a href="/users/'. $user['user_name'] .'/posts" class="sidebar-menu-post with-count symbol'. ($page == 1 ? ' selected' : '') .'">
        <span>All Posts</span>
        <span class="post-count">
          <span class="test-post-count">'. $post_amount['COUNT(id)'] .'</span>
        </span>
      </a>
      <a href="/users/'. $user['user_name'] .'/replies" class="sidebar-menu-replies with-count symbol'. ($page == 2 ? ' selected' : '') .'">
        <span>Replies</span>
        <span class="post-count">
          <span class="test-reply-count">'. $reply_amount['COUNT(reply_by_id)'] .'</span>
        </span>
      </a>
      <a href="/users/'. $user['user_name'] .'/yeahs" class="sidebar-menu-empathies with-count symbol'. ($page == 3 ? ' selected' : '') .'">
        <span>Yeahs</span>
        <span class="post-count">
          <span class="test-empathy-count">'. $yeah_amount['COUNT(yeah_by)'] .'</span>
        </span>
      </a>';

    if ($user['user_id'] == $_SESSION['user_id']) {
        echo '<a href="/users/'. $user['user_name'] .'/nahs" class="sidebar-menu-nahs with-count symbol'. ($page == 4 ? ' selected' : '') .'">
    	<span>Nahs</span>
    	<span class="post-count">
    	<span class="test-empathy-count">'. $nah_amount['COUNT(nah_by)'] .'</span>
        </span>
        </a>';
    }
      
    echo '</div></div>';
}

function userContent($user, $selected)
{
    global $dbc;

    $following_count = $dbc->prepare('SELECT COUNT(follow_by) FROM follows WHERE follow_by = ?');
    $following_count->bind_param('i', $user['user_id']);
    $following_count->execute();
    $result_count = $following_count->get_result();
    $following_amount = $result_count->fetch_assoc();

    $followers_count = $dbc->prepare('SELECT COUNT(follow_to) FROM follows WHERE follow_to = ?');
    $followers_count->bind_param('i', $user['user_id']);
    $followers_count->execute();
    $result_count = $followers_count->get_result();
    $followers_amount = $result_count->fetch_assoc();

    $get_fav_post = $dbc->prepare('SELECT * FROM profiles INNER JOIN posts ON id = fav_post AND deleted = 0 WHERE user_id = ?');
    $get_fav_post->bind_param('i', $user['user_id']);
    $get_fav_post->execute();
    $result_fav_post = $get_fav_post->get_result();
    $fav_post = $result_fav_post->fetch_assoc();

    echo '<div class="sidebar-container">
    '. (isset($fav_post['post_image']) ? '<a href="/posts/'.$fav_post['id'].'" id="sidebar-cover" style="background-image:url('.$fav_post['post_image'].')">
        <img src="'.$fav_post['post_image'].'" class="sidebar-cover-image">
      </a>':'').'
      <div id="sidebar-profile-body" class="'.(isset($fav_post['post_image'])?'with-profile-post-image':'').'">
        <div class="icon-container'.($user['user_level'] > 1 ? ' verified' : '').'">
          <a href="/users/'.$user['user_name'] .'/posts">
            <img src="'.printFace($user['user_face'], 0).'" alt="'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'" id="icon">
          </a>
        </div>
        '.(isset($user['organization'])?'<span class="user-organization">'.$user['organization'].'</span>':'').'
        <a href="/users/'. $user['user_name'] .'/posts" '.(isset($user['name_color']) ? 'style="color: '. $user['name_color'] .'"' : '').' class="nick-name">'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'</a>
        <p class="id-name">'. $user['user_name'] .'</p>
      </div>';

    if (!empty($_SESSION['signed_in']) && ($_SESSION['user_id'] !== $user['user_id'])) {
        echo '<div class="user-action-content"><div class="toggle-button" style="text-align: center;">
    	<button type="button" data-user-id="'. $user['user_id'] .'" class="';

        $check_followed = $dbc->prepare('SELECT * FROM follows WHERE follow_by = ? AND follow_to = ? LIMIT 1');
        $check_followed->bind_param('ii', $_SESSION['user_id'], $user['user_id']);
        $check_followed->execute();
        $followed_result = $check_followed->get_result();

        if (!$followed_result->num_rows == 0) {
            echo 'unfollow';
        } else {
            echo 'follow';
        }
        echo '-button button symbol">Follow</button>
		</div></div>';
    } elseif (!empty($_SESSION['signed_in']) && ($_SESSION['user_id'] == $user['user_id']) && !empty($selected)) {
        echo '<div id="edit-profile-settings"><a class="button symbol" href="/settings/profile">Profile Settings</a></div>';
    }

    echo '<ul id="sidebar-profile-status">
        <li><a href="/users/'. $user['user_name'] .'/following"'. ($selected == "following" ? 'class="selected"' : '') .'><span class="number">'. $following_amount['COUNT(follow_by)'] .'</span>Following</a></li>
        <li><a href="/users/'. $user['user_name'] .'/followers"'. ($selected == "followers" ? 'class="selected"' : '') .'><span class="number">'. $followers_amount['COUNT(follow_to)'] .'</span>Followers</a></li>
      </ul>
    </div>';
}

function sidebarSetting()
{
    global $dbc;

    $get_announce = $dbc->prepare('SELECT * FROM titles WHERE type = 5 LIMIT 1');
    $get_announce->execute();
    $announce_result = $get_announce->get_result();
    $announce = $announce_result->fetch_assoc();
    echo '<div class="sidebar-setting sidebar-container">
		  <ul>

			<li><a href="/settings/account" class="sidebar-menu-setting symbol"><span>Cedar Settings</span></a></li>
			<li><a href="/titles/'.$announce['title_id'].'" class="sidebar-menu-info symbol"><span>Cedar Announcements</span></a></li>
	        
		  </ul>
		</div>';
}


function noUser()
{
    echo '<title>Cedar - Error</title><div class="no-content track-error" data-track-error="404"><div><p>The user could not be found.</p></div></div>';
}

function userInfo($user)
{

    global $dbc;

    $get_prof = $dbc->prepare('SELECT * FROM profiles WHERE user_id = ?');
    $get_prof->bind_param('i', $user['user_id']);
    $get_prof->execute();
    $prof_result = $get_prof->get_result();
    $profile = $prof_result->fetch_assoc();

    $get_user_level = $dbc->prepare('SELECT user_level FROM users WHERE user_id = ?');
    $get_user_level->bind_param('i', $_SESSION['user_id']);
    $get_user_level->execute();
    $user_level_result = $get_user_level->get_result();
    $user_level = $user_level_result->fetch_assoc();

    $get_yeahs = $dbc->prepare('SELECT COUNT(yeah_id) FROM yeahs WHERE yeah_post IN (SELECT id FROM posts WHERE post_by_id = ?) OR yeah_post IN (SELECT reply_id FROM replies WHERE reply_by_id = ?)');
    $get_yeahs->bind_param('ii', $user['user_id'], $user['user_id']);
    $get_yeahs->execute();
    $yeahs_result = $get_yeahs->get_result();
    $yeahs = $yeahs_result->fetch_assoc();

    echo '<div class="sidebar-container sidebar-profile">';
    if (!is_null($profile['bio'])) {
        echo '<div class="profile-comment"><p class="js-truncated-text">';
        if (mb_strlen($profile['bio']) <= 99) {
            echo nl2br($profile['bio']) .'</p></div>';
        } else {
            echo nl2br(mb_substr($profile['bio'], 0, 97)) .'...</p>
			<p class="js-full-text none">'.nl2br($profile['bio']).'</p>
			<button type="button" class="description-more-button js-open-truncated-text-button">Show More</button></div>';
        }
    }

    echo '<div class="user-data">
      <div class="user-main-profile data-content">
        <h4><span>Country</span></h4>
        <div class="note">';

    switch ($profile['country']) {
        case 1:
            echo "United States";
            break;
        case 2:
            echo "United Kingdom";
            break;
        case 3:
            echo "Japan";
            break;
        case 4:
            echo "France";
            break;
        case 5:
            echo "Canada";
            break;
        case 6:
            echo "Australia";
            break;
        case 7:
            echo "Germany";
            break;
        default:
            echo "Not set.";
    }

    echo '</div>
    <h4><span>Birthday</span></h4>
    <div class="note birthday">'. (isset($profile['birthday']) ? date('m/d', strtotime($profile['birthday'])) : 'Not set.') .'</div>
    </div>
    <div class="game-skill data-content">
	  <h4><span>Status</span></h4>
	  <div class="note"><span class="test-game-skill symbol'.(strtotime($profile['last_online'])>time()-35?'">On':' offline">Off').'line</span></div>
    </div>

    <div class="yeahs-received'. ($user_level['user_level'] > 0 ? ' data-content' : '') .'"><h4><span>Yeahs Received</span></h4><div class="note">'. number_format($yeahs['COUNT(yeah_id)']) .'</div></div>';


    if ($user_level['user_level'] > 0) {
        echo '<div class="user-id data-content"><h4><span>User ID</span></h4><div class="note">'. $user['user_id'] .'</div></div>
    	<div class="ip"><h4><span>IP Address</span></h4><div class="note">'. $user['ip'] .'</div></div>';
    }


    echo '</div></div>

    <div class="sidebar-container sidebar-favorite-community">
      <h4><a href="/'.(!empty($_SESSION['signed_in']) && ($_SESSION['user_id'] == $user['user_id']) ? 'communities' : 'users/'.$user['user_name']).'/favorites'.'" class="symbol favorite-community-button"><span>Favorite Communities</span></a></h4>


      <ul class="test-favorite-communities">';


    $get_fav_titles = $dbc->prepare('SELECT titles.* FROM titles, favorite_titles WHERE titles.title_id = favorite_titles.title_id AND favorite_titles.user_id = ? ORDER BY favorite_titles.fav_id DESC LIMIT 10');
    $get_fav_titles->bind_param('i', $user['user_id']);
    $get_fav_titles->execute();
    $fav_titles_result = $get_fav_titles->get_result();
    $empty_space = 0;
    while ($fav_titles = $fav_titles_result->fetch_assoc()) {
        echo '<li class="favorite-community"><a href="/titles/'.$fav_titles['title_id'].'"><span class="icon-container"><img id="icon" src="'.$fav_titles['title_icon'].'"></span></a>              
          <span class="platform-tag">';
        switch ($fav_titles['type']) {
            case 1:
                echo '<img src="/assets/img/platform-tag-wiiu.png">';
                break;
            case 2:
                echo '<img src="/assets/img/platform-tag-3ds.png">';
                break;
            case 3:
                echo '<img src="/assets/img/platform-tag-wiiu-3ds.png">';
                break;
            case 4:
                echo '<img src="/assets/img/platform-tag-switch.png">';
                break;
        }
        echo '</span></li>';
        $empty_space++;
    }
    for ($i = 10; $i > $empty_space; $i--) {
        echo '<li class="favorite-community empty"><span class="icon-container empty-icon"><img src="/assets/img/'. (isset($_COOKIE['dark-mode']) ? 'dark-' : '') .'empty.png" id="icon"></span></li>';
    }

    echo '</ul>


    </div>';
}
