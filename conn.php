<?php
connect();
function connect() {
	global $conn;
	
	// Database configuration
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "csd_iv_levelgenerator_v1_1";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	// echo "Connected successfully";
}

?>