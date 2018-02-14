<?php
require_once('lib/htm.php');

if (!empty($_SESSION['signed_in'])) {

	$get_user = $dbc->prepare('SELECT * FROM users WHERE user_id = ?');
	$get_user->bind_param('i', $_SESSION['user_id']);
	$get_user->execute();
	$user_result = $get_user->get_result();
	$user = $user_result->fetch_assoc();

	if ($user['user_level'] > 0) {

		if (isset($action)){
			if ($action == 'delete_user') {
				$get_user = $dbc->prepare('SELECT * FROM users WHERE user_id = ? LIMIT 1');
				$get_user->bind_param('i', $_POST['user_id']);
				$get_user->execute();
				$user_result = $get_user->get_result();
				if ($user_result->num_rows == 0){
					exit('{"success":0, "problem":"User does not exist."}');
				} else {
					$user = $user_result->fetch_assoc();
					if ($user['user_level'] > 0) {
						exit('{"success":0,"problem":"You can\'t delete an admin."}');
					} else {
						$delete_user = $dbc->prepare('DELETE FROM users WHERE user_id = ? LIMIT 1');
						$delete_user->bind_param('i', $user['user_id']);
						$delete_user->execute();
						exit('{"success":1}');
					}
			    }

			} elseif ($action == 'delete_post') {
				$get_post = $dbc->prepare('SELECT * FROM posts WHERE id = ? LIMIT 1');
				$get_post->bind_param('i', $_POST['post_id']);
				$get_post->execute();
				$post_result = $get_post->get_result();
				if ($post_result->num_rows == 0) {
					exit('{"success":0,"problem":"Post does not exist."}');
				} elseif ($_POST['post_violation_type'] == '') {
					exit('{"success":0,"problem":"Please specify a violation type."}');
				} elseif ($_POST['post_violation_type'] == 0 && $_POST['post_reason'] == '') {
					exit('{"success":0,"problem":"Please specify a reason for deletion."}');
				} else {
					$post = $post_result->fetch_assoc();

					if ($_POST['post_violation_type'] == 0) {
						$admin_message = $dbc->prepare('INSERT INTO admin_messages (admin_type, admin_text, admin_to, admin_by, admin_post, is_reply) VALUES (?, ?, ?, ?, ?, 0)');
						$admin_message->bind_param('isiii', $_POST['post_violation_type'], $_POST['post_reason'], $post['post_by_id'], $_SESSION['user_id'], $post['id']);
					} else {
						$admin_message = $dbc->prepare('INSERT INTO admin_messages (admin_type, admin_to, admin_by, admin_post, is_reply) VALUES (?, ?, ?, ?, 0)');
						$admin_message->bind_param('iiii', $_POST['post_violation_type'], $post['post_by_id'], $_SESSION['user_id'], $post['id']);
				    }
					$admin_message->execute();

					$get_notif = $dbc->prepare('SELECT * FROM notifs WHERE notif_type = 5 AND notif_to = ? LIMIT 1');
					$get_notif->bind_param('i', $post['post_by_id']);
					$get_notif->execute();
					$notif_result = $get_notif->get_result();
					if ($notif_result->num_rows == 0) {
						$admin_notif = $dbc->prepare('INSERT INTO notifs (notif_type, notif_to) VALUES (5, ?)');
						$admin_notif->bind_param('i', $post['post_by_id']);
						$admin_notif->execute();
					} else {
						$notif = $notif_result->fetch_assoc();

						$admin_notif = $dbc->prepare('UPDATE notifs SET notif_read = 0, notif_date = NOW() WHERE notif_id = ?');
						$admin_notif->bind_param('i', $notif['notif_id']);
						$admin_notif->execute();
					}

					$delete_user = $dbc->prepare('UPDATE posts SET deleted = 1 WHERE id = ?');
					$delete_user->bind_param('i', $post['id']);
					$delete_user->execute();
					exit('{"success":1}');
				}
			
			} elseif ($action == 'delete_reply') {
				$get_reply = $dbc->prepare('SELECT * FROM replies WHERE reply_id = ? LIMIT 1');
				$get_reply->bind_param('i', $_POST['reply_id']);
				$get_reply->execute();
				$reply_result = $get_reply->get_result();
				if ($reply_result->num_rows == 0) {
					exit('{"success":0,"problem":"Reply does not exist."}');
				} elseif ($_POST['reply_violation_type'] == '') {
					exit('{"success":0,"problem":"Please specify a violation type."}');
				} elseif ($_POST['reply_violation_type'] == 0 && $_POST['reply_reason'] == '') {
					exit('{"success":0,"problem":"Please specify a reason for deletion."}');
				} else {
					$reply = $reply_result->fetch_assoc();

					if ($_POST['reply_violation_type'] == 0) {
						$admin_message = $dbc->prepare('INSERT INTO admin_messages (admin_type, admin_text, admin_to, admin_by, admin_post, is_reply) VALUES (?, ?, ?, ?, ?, 1)');
						$admin_message->bind_param('isiii', $_POST['reply_violation_type'], $_POST['reply_reason'], $reply['reply_by_id'], $_SESSION['user_id'], $reply['reply_id']);
					} else {
						$admin_message = $dbc->prepare('INSERT INTO admin_messages (admin_type, admin_to, admin_by, admin_post, is_reply) VALUES (?, ?, ?, ?, 1)');
						$admin_message->bind_param('iiii', $_POST['reply_violation_type'], $reply['reply_by_id'], $_SESSION['user_id'], $reply['reply_id']);
				    }
					$admin_message->execute();

					$get_notif = $dbc->prepare('SELECT * FROM notifs WHERE notif_type = 5 AND notif_to = ? LIMIT 1');
					$get_notif->bind_param('i', $reply['reply_by_id']);
					$get_notif->execute();
					$notif_result = $get_notif->get_result();
					if ($notif_result->num_rows == 0) {
						$admin_notif = $dbc->prepare('INSERT INTO notifs (notif_type, notif_to) VALUES (5, ?)');
						$admin_notif->bind_param('i', $reply['reply_by_id']);
						$admin_notif->execute();
					} else {
						$notif = $notif_result->fetch_assoc();

						$admin_notif = $dbc->prepare('UPDATE notifs SET notif_read = 0, notif_date = NOW() WHERE notif_id = ?');
						$admin_notif->bind_param('i', $notif['notif_id']);
						$admin_notif->execute();
					}

					$delete_user = $dbc->prepare('UPDATE replies SET deleted = 1 WHERE reply_id = ?');
					$delete_user->bind_param('i', $reply['reply_id']);
					$delete_user->execute();
					exit('{"success":1}');
				}
			
			} else {
				header('HTTP/1.0 404 Forbidden');
			}
		} else {
			?>

		<!DOCTYPE html>

		<html lang="en">
		  <head>
		    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		      <title>Admin Panel</title>
		      <link rel="icon" type="image/png" sizes="96x96" href="/assets/img/favicon-96x96.png">
		      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
		      <link rel="stylesheet" type="text/css" href="/admin/css/style.css">
	      </head>

	      <body class="m-4">
	        <h1>Admin Panel</h1>

	        <p>I threw this admin panel together in like 5 minutes.<br>It may look like shit, but it works for now.</p>

	        <div class="row">

	          <div class="col-sm-6 mb-3">
	            <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Delete User</h4>
                    <p class="card-text">Permanently delete a user and all of their posts.</p>
                    <p>A users unique User ID can be found on their profile.</p>
                    <div class="alert alert-danger" role="alert">
                      Note: This action cannot be undone.
                    </div>
                    <form id="delete_user" action="/admin_panel/delete_user">
                      <input class="form-control mb-2" type="text" name="user_id" placeholder="User ID">
                      <input type="submit" class="btn btn-danger float-right" value="Delete">
                    </form>
                  </div>
                </div>
              </div>

              <div class="col-sm-6 mb-3">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Delete Post</h4>
                    <form id="delete_post" action="/admin_panel/delete_post">
                      <input class="form-control mb-2" type="text" name="post_id" placeholder="Post ID">
                      <p>Violation Type</p>
                      <select name="post_violation_type" class="form-control">
                        <option value="">Please make a selection.</option>
                        <option value="1">Spam</option>
                        <option value="2">Sexually Explicit</option>
                        <option value="0">Other</option>
                      </select>
                      <input type="submit" class="btn btn-primary mt-2 float-right" value="Delete">
                    </form>
                  </div>
                </div>
              </div>

              <div class="col-sm-6 mb-3">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Delete Reply</h4>
                    <form id="delete_reply" action="/admin_panel/delete_reply">
                      <input class="form-control mb-2" type="text" name="reply_id" placeholder="Reply ID">
                      <p>Violation Type</p>
                      <select name="reply_violation_type" class="form-control">
                        <option value="">Please make a selection.</option>
                        <option value="1">Spam</option>
                        <option value="2">Sexually Explicit</option>
                        <option value="0">Other</option>
                      </select>
                      <input type="submit" class="btn btn-primary mt-2 float-right" value="Delete">
                    </form>
                  </div>
                </div>
              </div>

            </div>

	        <script src="/assets/js/jquery-3.3.1.min.js"></script>
	        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
	        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
	        <script src="/admin/js/admin.js"></script>
	      </body>
	    </html>

	    <?php
	}

	} else {
		header('HTTP/1.0 403 Forbidden');
	}

} else {
	header('HTTP/1.0 403 Forbidden');
}