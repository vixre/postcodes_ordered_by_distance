<?php

require_once('distance.php');
date_default_timezone_set('Europe/London');

// connect to database
$db_server = 'localhost';
$db_user   = 'root';
$db_pass   = 'password';
$db_name   = 'example_db';

$conn = new mysqli($db_server, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
	trigger_error('Database connection failed: '  . $conn->connect_error, E_USER_ERROR);
}

// The origin post code 
$_REQUEST['post_code'] = 'YO31 8UB';

// Remove all spaces in post code
$post_code_search = str_replace(' ', '', $_REQUEST['post_code']);

$page_number = (!empty($_REQUEST['page_number'])) ? $_REQUEST['page_number'] : $page_number = 1;

$distance = new distance;

$distance->entities_per_page = 10;
$distance->offset = ($page_number - 1) * $distance->entities_per_page;

// Initialize map object and set location origin
$distance->origin = $post_code_search;

$distance->populate_coordinates();
$returned_array = $distance->build_result();

// Encoded in json
echo json_encode($returned_array);
?>
