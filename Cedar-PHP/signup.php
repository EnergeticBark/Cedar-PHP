<?php
require_once('lib/htm.php');
if(empty($_SESSION['signed_in'])){
	if($_SERVER['REQUEST_METHOD'] != 'POST'){
		?>
        <script src="/assets/js/jquery-3.2.1.min.js"></script>
        <script async src="/assets/js/yeah.js"></script>
        <script src="/assets/js/pace.min.js"></script>
        <script src="/assets/js/favico.js"></script>
        <script src="https://unpkg.com/tippy.js@2.0.9/dist/tippy.all.min.js"></script>
        <meta name="viewport" content="width=device-width,minimum-scale=1, maximum-scale=1">
        <link rel="stylesheet" type="text/css" href="/assets/css/login.css">

        <title>Create an account</title>
        <div class="hb-contents-wrapper"><div class="hb-container hb-l-inside">
            <h2>Sign Up</h2>
            <p>Create a User ID for Cedar.</p>
        </div>

        <form method="post" enctype="multipart/form-data">
            <div class="hb-container hb-l-inside-half hb-mg-top-none">              

                <div class="auth-input-double">               
                    <label>
                        <input type="text" name="username" maxlength="16" title="Cedar ID" placeholder="User ID" value="">
                    </label>
                    <label>
                        <input type="password" name="password" maxlength="16" title="Password" placeholder="Password">
                    </label>
                    <label>
                        <input type="password" name="confirm_password" maxlength="16" title="Password" placeholder="Confirm Password">
                    </label>
                    <label>
                        <input type="text" name="name" maxlength="16" title="Name" placeholder="Name" value="">
                    </label>
                    <lable>
                        <div style="text-align: center;">
                            <p style="display: inline;">Custom Image:</p>
                            <input name="face-type" type="radio" value="1" checked="" style="margin-left: 5px;display: inline;margin-right: 50px;margin-top: 10px;margin-bottom: 10px;">
                            <p style="display: inline;">Mii:</p>
                            <input name="face-type" type="radio" value="2" style="margin-left: 5px; display: inline;">
                        </div>
                        <div class="custom-face">
                            <p style="display: inline">Profile picture upload: </p>
                            <input type="file" name="face" style="max-width: 230;" accept="image/*">
                        </div>
                        <div class="nnid-face none">
                            <p style="margin: 0;">Enter the NNID for the Mii you want to use.</p>
                            <input type="text" name="face" maxlength="16" placeholder="NNID">
                        </div>
                    </lable>
                </div>
                <input type="submit" name="submit" class="hb-btn hb-is-decide" style="margin-top: 4px;" id="btn_text" value="Sign Up">
            </form>
        </div>

    <?php
    } else {
    	if (isset($_POST['submit'])) {
    		$errors = array();

    		if (($_POST['face-type'] == 2) && isset($_POST['face'])){

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
                    $face = $body;
    			}

    			if(!empty($errors)){
                    exit($errors[0]);
                }
    		} else {
    			$img=$_FILES['face'];
    			if (empty($img['name'])) {  
    				$errors[] = 'upload an image';
    			} else {
    				$filename = $img['tmp_name'];

                    //imageUpload() returns 1 if it fails and the image URL if successful
                    $face = uploadImage($filename);
                    if ($face == 1) {
                        $errors[] = 'Image upload failed';
                    }
    			}
    		}

    		if (strlen($_POST['username']) > 16) {
    			$errors[] = 'User ID connot be longer than 16 characters.';
    		}
    		if (empty($_POST['username'])){
    			$errors[] = 'User ID cannot be empty.';
    		}
    		if(preg_match("/([%\$#\*\/\ ]+)/", $_POST['username'])){
    			$errors[] = 'User ID cannot contain special characters or spaces.';
    		}

    		$search_user = $dbc->prepare('SELECT * FROM users WHERE users.user_name = ? LIMIT 1');
    		$search_user->bind_param('s', $_POST['username']);
    		$search_user->execute();
    		$user_result = $search_user->get_result();

    		if ($user_result->num_rows > 0) {
    			$errors[] = 'User ID already exists';
    		}

    		if ($_POST['password'] != $_POST['confirm_password']) {
    			$errors[] = 'Passwords do not match.';
    		}
    		if (empty($_POST['password'])) {
    			$errors[] = 'Password cannot be empty.';
    		}

    		if (strlen($_POST['name']) > 16){
    			$errors[] = 'Name connot be longer than 16 characters.';
    		}
    		if (empty($_POST['name'])){
    			$errors[] = 'Name cannot be empty.';
    		}

    		if (!empty($errors)){
    			echo '<script type="text/javascript">alert("' . $errors[0] . '");</script><META HTTP-EQUIV="refresh" content="0;URL=/signup">';
    		} else {

    			$username = htmlspecialchars($_POST['username'], ENT_QUOTES);
    			$name = $_POST['name'];

    			$password_gen = password_hash($_POST['password'], PASSWORD_DEFAULT);

    			$new_user = $dbc->prepare('INSERT INTO users (user_name, user_pass, nickname, user_face, date_created, ip) VALUES (?,?,?,?,NOW(),?)');
    			$new_user->bind_param('sssss', $username, $password_gen, $name, $face, $_SERVER['REMOTE_ADDR']);
    			$new_user->execute();

    			$get_user = $dbc->prepare('SELECT user_id FROM users WHERE user_name = ? LIMIT 1');
    			$get_user->bind_param('s', $username);
    			$get_user->execute();
    			$user_result = $get_user->get_result();

    			if ($user_result->num_rows == 0){
    				printHeader();
    				exit('<br>There was an error creating your account please try again.');
    			} else {

    				$user = $user_result->fetch_assoc();

    				$new_profile = $dbc->prepare('INSERT INTO profiles (user_id) VALUES (?)');
    				$new_profile->bind_param('i', $user['user_id']);
    				$new_profile->execute();

    				$_SESSION['signed_in'] = true;
    				$_SESSION['user_id'] = $user['user_id'];
    				echo '<META HTTP-EQUIV="refresh" content="0;URL=/">';
    			}
    		}
    	}
    }
}