<?php
require_once('lib/htm.php');
require_once('lib/htmUsers.php');

if(empty($_SESSION['signed_in'])){
	$tabTitle = 'Cedar';
	printHeader('');
	echo '<div id="main-body"><div class="warning-content warning-content-forward"><div><strong>Welcome to Cedar!</strong><p>You must sign in to view this page.</p>
    <a class="button" href="/">Cedar</a></div></div>';
} else {

	function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}

	if($_SERVER['REQUEST_METHOD'] != 'POST'){
		$get_user = $dbc->prepare('SELECT * FROM users INNER JOIN profiles ON profiles.user_id = users.user_id WHERE users.user_id = ? LIMIT 1');
		$get_user->bind_param('i', $_SESSION['user_id']);
		$get_user->execute();
		$user_result = $get_user->get_result();
		$user = $user_result->fetch_assoc();

		$tabTitle = 'Cedar - Profile Settings';

		printHeader('');

		$post_count = $dbc->prepare('SELECT COUNT(id) FROM posts WHERE post_by_id = ?');
		$post_count->bind_param('i', $user['user_id']);
		$post_count->execute();
		$result_count = $post_count->get_result();
		$post_amount = $result_count->fetch_assoc();

		$yeah_count = $dbc->prepare('SELECT COUNT(yeah_by) FROM yeahs WHERE yeah_by = ?');
		$yeah_count->bind_param('i', $user['user_id']);
		$yeah_count->execute();
		$result_count = $yeah_count->get_result();
		$yeah_amount = $result_count->fetch_assoc();

		echo '<div id="main-body"><div id="sidebar" class="user-sidebar">';

		userContent($user, "settings");

		echo '<div class="sidebar-setting sidebar-container">
		  <div class="sidebar-post-menu">
		    <a href="/users/'. $user['user_name'] .'/posts" class="sidebar-menu-post with-count symbol">
		      <span>All Posts</span>
		      <span class="post-count">
		        <span class="test-post-count">'. $post_amount['COUNT(id)'] .'</span>
		      </span>
		    </a>

		    <a href="/users/'. $user['user_name'] .'/yeahs" class="sidebar-menu-empathies with-count symbol">
		      <span>Yeahs</span>
		      <span class="post-count">
		        <span class="test-empathy-count">'. $yeah_amount['COUNT(yeah_by)'] .'</span>
		      </span>
		    </a>
		  </div>
		</div>';

		userInfo($user);

		echo '</div><div class="main-column"><div class="post-list-outline"><h2 class="label">Profile Settings</h2>';

		$get_prof = $dbc->prepare('SELECT * FROM profiles INNER JOIN posts ON id = fav_post AND deleted = 0 WHERE user_id = ?');
		$get_prof->bind_param('i', $user['user_id']);
		$get_prof->execute();
		$prof_result = $get_prof->get_result();
		$profile = $prof_result->fetch_assoc();

		echo '<form class="setting-form" action="" method="post" enctype="multipart/form-data">
		  <ul class="settings-list">
		    <li class="setting-profile-comment">
		      <p class="settings-label">Profile Comment</p>
		      <textarea id="profile-text" class="textarea" name="profile_comment" maxlength="400" placeholder="Write about yourself here.">'. $user['bio'] .'</textarea>
		    </li>

		    <li>
		      <p class="settings-label">Nickname</p>
		      <input class="textarea" placeholder="Change your nickname here." type="text" maxlength="16" name="name" style="cursor: auto; height: auto;" value="'. htmlspecialchars($user['nickname'], ENT_QUOTES) .'" />
		    </li>

		    <li class="setting-profile-post">
		      <p class="settings-label">Favorite Post</p>
		      <p class="note">You can set one of your posts as your favorite via the settings button of that post.</p>
		      '. (isset($profile['post_image']) ? '<div class="select-content"><button id="profile-post" type="button" class="submit"><span class="better-fav-button" style="background-image:url('. $profile['post_image'] .')"></span><span class="symbol">Remove Favorite Post</span></button></div>':'') .'
		    </li>

		    <li>
		      <div style="text-align: center;">
		        <p style="display: inline;">Custom Image:</p>
		        <input name="face-type" type="radio" value="1" checked style="margin-left: 5px; display: inline; margin-right: 50px; margin-top: 20px;">
		        <p style="display: inline;">Mii:</p>
		        <input name="face-type" type="radio" value="2" style="margin-left: 5px; display: inline;">
		      </div>
		      <div class="custom-face">
		        <p class="settings-label">Profile picture upload</p>
		        <input type="file" name="face" accept="image/*">
		      </div>
		      <div class="nnid-face none">
		        <p class="settings-label">NNID</p>
		        <input class="textarea" placeholder="Enter the NNID for the mii you want to use." type="text" maxlength="16" name="face" style="cursor: auto; height: auto;" />
		      </div>
		    </li>

		    <li>
		      <p class="settings-label"><label for="select_country">Country</label></p>
		      <div class="select-content">
		        <div class="select-button">
		          <select name="country" id="select_country">
		            <option value="1" ';

        if($profile['country']==1){
        	echo ' selected';
        }

        echo '>United States</option>
        <option value="2" ';

        if($profile['country']==2){
        	echo ' selected';
        }

        echo '>United Kingdom</option>
        <option value="3" ';

        if($profile['country']==3){
        	echo ' selected';
        }

        echo '>Japan</option>
        <option value="4" ';

        if($profile['country']==4){
        	echo ' selected';
        }

        echo '>France</option>
        <option value="5" ';

        if($profile['country']==5){
        	echo ' selected';
        }

        echo '>Canada</option>
        <option value="6" ';

        if($profile['country']==6){
        	echo ' selected';
        }

        echo '>Australia</option>
        <option value="7" ';

        if($profile['country']==7){
        	echo ' selected';
        }

        echo '>Germany</option>
        </select>
      </div>
    </div>
  </li>
  <li>
    <p class="settings-label"><label for="select_birthday">When is your Birthday?</label></p>
    <div class="select-content">
      <div class="select-button">
        <input type="date" name="birthday" min="2017-01-01" max="2017-12-31" value="'. (isset($user['birthday']) ? date('Y-m-d', strtotime($user['birthday'])) : '') .'" style="width: auto; max-width: 100%; min-width: 50%; font-size: 16px;">
      </div>
    </div>
    <p class="note">Only the day and month are stored.</p>
  </li>
</ul>
<div class="form-buttons">
<input type="submit" name="submit" class="black-button apply-button" value="Save Settings" /></div></form></div></div></div></div><div class="dialog active-dialog modal-window-open none mask"><div class="dialog-inner"><div class="window"><h1 class="window-title"></h1><div class="window-body"><p class="window-body-content">Settings saved.</p><div class="form-buttons"><button class="ok-button black-button" type="button" data-event-type="ok">OK</button></div></div></div></div></div></div>';
    } else {

    	if(!empty($_POST['name'])){

    		if(strlen($_POST['name']) > 16){
    			$errors[] = 'Name cannot be longer than 16 characters';
    		}

    		if(empty($errors)){

    			$name = $_POST['name'];
    			$user_change = $dbc->prepare('UPDATE users SET nickname = ? WHERE users.user_id = ?');
    			$user_change->bind_param('ss', $name, $_SESSION['user_id']);
    			$user_change->execute();
    		}
    	}

    	if(isset($_POST['birthday']) && validateDate($_POST['birthday'], 'Y-m-d')){
    		$birthday = date('Y-m-d', strtotime($_POST['birthday']));
    		$user_change = $dbc->prepare('UPDATE profiles SET birthday = ? WHERE user_id = ?');
    		$user_change->bind_param('si', $birthday, $_SESSION['user_id']);
    		$user_change->execute();
    	}

    	if($_POST['country'] == 1 || 2 || 3 || 4 || 5 || 6 || 7){
    		$user_change = $dbc->prepare('UPDATE profiles SET country = ? WHERE user_id = ?');
    		$user_change->bind_param('ii', $_POST['country'], $_SESSION['user_id']);
    		$user_change->execute();
    	}

    	if(strlen($_POST['profile_comment']) > 400){
    		$errors[] = 'Profile Comment cannot be longer than 400 characters';
    	}

    	if(empty($errors)){

    		if(!empty($_POST['profile_comment'])){

    			$bio = htmlspecialchars($_POST['profile_comment'], ENT_QUOTES);
    			$user_change = $dbc->prepare('UPDATE profiles SET bio = ? WHERE user_id = ?');
    			$user_change->bind_param('si', $bio, $_SESSION['user_id']);
    		} else {
    			$user_change = $dbc->prepare('UPDATE profiles SET bio = NULL WHERE user_id = ?');
    			$user_change->bind_param('i', $_SESSION['user_id']);
    		}
    		$user_change->execute();
    	}

    	if (isset($_POST['face'])){
    		if ($_POST['face-type'] == 2){

    			$ch = curl_init();
    			curl_setopt_array($ch, array(
    				CURLOPT_URL => 'https://ariankordi.net/seth/'. $_POST['face'],
    				CURLOPT_HEADER => true,
    				CURLOPT_RETURNTRANSFER => true));
    			$response = curl_exec($ch);

                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if($httpCode == 404) {
                    $errors[] = 'Invalid NNID.';
                } else {
                    $body = substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));
                    $dom = new DOMDocument;
                    $dom->loadHTML($body);
    			}

    			if(empty($errors)){
    				$user_change = $dbc->prepare('UPDATE users SET user_face = ? WHERE users.user_id = ?');
    				$user_change->bind_param('si', $body, $_SESSION['user_id']);
    				$user_change->execute();
    			} else {
                    exit($errors[0]);
                }
    		} else {

    			$img=$_FILES['face'];
    			if(!empty($img['name'])){
    				$filename = $img['tmp_name'];
    				
                    //imageUpload() returns 1 if it fails and the image URL if successful
                    $face = uploadImage($filename);
                    if ($face == 1) {
                    	$errors[] = 'Image upload failed';
                    }

    				if(!empty($errors)){

    				} else {
    					$user_change = $dbc->prepare('UPDATE users SET user_face = ? WHERE users.user_id = ?');
    					$user_change->bind_param('si', $face, $_SESSION['user_id']);
    					$user_change->execute();
    				}
    			}
    		}
    	}
    	echo 'success';
    }
}