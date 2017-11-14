<?php
require_once('lib/connect.php');
$_SESSION['signed_in'] = false;
$_SESSION['user_id'] = null;
$_SESSION['username'] = null;
echo '<META HTTP-EQUIV="refresh" content="0;URL=/">';
