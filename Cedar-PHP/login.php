<?php
require_once('lib/htm.php');

if(empty($_SESSION['signed_in'])){
	if($_SERVER['REQUEST_METHOD'] != 'POST'){
		?>
		<!DOCTYPE html>
        <html lang="en">
        <head>
        	<title>Sign In to Cedar</title>
        	<meta name="viewport" content="width=device-width,minimum-scale=1, maximum-scale=1">
        	<link rel="stylesheet" type="text/css" href="/assets/css/login.css">
        </head>
        <body>
        	<div class="hb-contents-wrapper">
        		<div class="hb-container hb-l-inside">
        			<h2>Sign In</h2>
        			<p>Please sign in with a Cedar User ID to proceed.</p>
        			<p>Or <a href="/signup">create an account</a>.</p>
        		</div>
        		<form method="post">
        			<div class="hb-container hb-l-inside-half hb-mg-top-none">              
        				<div class="auth-input-double">               
        					<label><input type="text" name="username" maxlength="16" title="Cedar ID" placeholder="User ID" value=""></label>
        					<label><input type="password" name="password" maxlength="16" title="Password" placeholder="Password"></label>
        				</div>
        				<input type="submit" name="submit" class="hb-btn hb-is-decide" style="margin-top: 4px;" id="btn_text" value="Sign In">
        			</div>
        		</form>
        	</div>
        </body>
        <?php
	} else {

		$errors = array();

		if(!empty($_SESSION['signed_in'])) {
			$errors[] = 'Already signed in';
		}

		if(empty($_POST['username'])){
			$errors[] = 'User ID cannot be empty';
		}

		if(empty($_POST['password'])){
			$errors[] = 'Passord cannot be empty';
		}

		$search_user = $dbc->prepare('SELECT * FROM users WHERE user_name = ? LIMIT 1');
		$search_user->bind_param('s', $_POST['username']);
		$search_user->execute();
		$user_result = $search_user->get_result();

		if(!$user_result || $user_result->num_rows == 0) {
			$errors[] = 'User ID doesn\'t exsist';
			exit('<script type="text/javascript">alert("' . $errors[0] . '");</script><META HTTP-EQUIV="refresh" content="0;URL=/login">');
		}

		$user = $user_result->fetch_assoc();

		if(!password_verify($_POST['password'], $user['user_pass'])) {
			$errors[] = 'User ID and password don\'t match'; 
		} 

		if (empty($errors)) {
			echo '<div id="main-body">redirecting';
			$_SESSION['signed_in'] = true;
			$_SESSION['user_id'] = $user['user_id'];

			$update_ip = $dbc->prepare('UPDATE users SET ip = ? WHERE user_id = ?');
			$update_ip->bind_param('si', $_SERVER['HTTP_CF_CONNECTING_IP'], $_SESSION['user_id']);
			$update_ip->execute();
			echo '<META HTTP-EQUIV="refresh" content="0;URL=/">';
		} else {
			echo '<script type="text/javascript">alert("' . $errors[0] . '");</script><META HTTP-EQUIV="refresh" content="0;URL=/login">';
		}
	}
}