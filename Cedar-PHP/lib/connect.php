<?php
/*btw don't put your credentials in variables
instead just use the values directly in mysqli_connect
that way, you won't have variables which contain your username and password
floating around accessible to anything in your files*/

mb_internal_encoding("UTF-8");
error_reporting(E_ALL);

// die will output to console which is not what you want to do if you want a clean console
// exit will not
function mysql_machine_broke()
{
    http_response_code(503);
    exit('The database has disconnected itself, please try again later.');
}


$dbc = @mysqli_connect('localhost', 'root', '', 'cedar');
if (!$dbc) {
    mysql_machine_broke();
}

@$dbc->set_charset('utf8mb4') || mysql_machine_broke();
//sets timezones
@$dbc->query('SET time_zone = "-5:00";') || mysql_machine_broke();
date_default_timezone_set('America/New_York');
if (session_status() == PHP_SESSION_NONE) {
    session_name('graham');
    session_set_cookie_params(30 * 6000000, "/");
    session_start();
}

// Error handler. If this returns nothing, there's something wrong.
if (empty($dbc->query('SELECT 1;')->num_rows)) {
    mysql_machine_broke();
}
