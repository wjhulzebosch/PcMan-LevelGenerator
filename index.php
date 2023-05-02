<?php

string $html;

// If new levelInformation has been posted, update the database
if (isset($_POST['levelInformation'])) {
	// FIXME: Write code here

}

// If we are looking at a specific level, show that level
if (isset($_GET['level'])) {
	// call conn.php to connect to the database
	require_once('conn.php');
	
	// Read the xml field from the levels table in the pcmanlevels database, where the levelID is the one specified in the URL
	$query = "SELECT xml FROM levels WHERE levelID = " . $_GET['level'];
	$result = mysql_query($query);
	$row = mysql_fetch_array($result);
	
	// If the level exists, replace the {xml} in $html with the xml from the database
	if ($row) {
		$html = str_replace('{xml}', $row['xml'], $html);
	}
		// If the level doesn't exist, show an error message
	else {
		$html = str_replace('{xml}', 'Error: Level not found', $html);
	}
	
	// Close the database connection
	mysql_close($conn);

	// Show the page
	echo $html;	
}
// Else, show the name, description, and link to each level
else {
	// FIXME: Write code here
}

function getHtml() {
	// Read template.html, and store it in $html
	$html = file_get_contents('template.html');
}