<?php
require_once('lib/htm.php');

if (!empty($_SESSION['signed_in'])) {
    if (isset($_GET['postId']) && isset($_GET['postType'])) {
        if ($_GET['postType'] == "post") {
            if (checkPostExists($_GET['postId']) && checkPostCreator($_GET['postId'], $_SESSION['user_id'])) {
                $delete = $dbc->prepare('UPDATE posts SET deleted = 2 WHERE id = ?');
                $delete->bind_param('i', $_GET['postId']);
                $delete->execute();
                echo 'success';
            }
        } else {
            if (checkReplyExists($_GET['postId']) && checkReplyCreator($_GET['postId'], $_SESSION['user_id'])) {
                $delete = $dbc->prepare('UPDATE replies SET deleted = 2 WHERE reply_id = ?');
                $delete->bind_param('i', $_GET['postId']);
                $delete->execute();
                echo 'success';
            }
        }
    }
}
