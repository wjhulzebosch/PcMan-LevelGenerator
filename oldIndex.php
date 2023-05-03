<?php
// call conn.php to connect to the database
require('conn.php');
connect();

global $conn;

// If new levelInformation has been posted, update the database
if (isset($_POST['levelInformation'])) 
{
    $levelInformation = $_POST['levelInformation'];
	// FIXME: Write code here
	echo 'Storing in DB';
}

// If we are looking at a specific level, show that level
if (isset($_GET['level'])) {
	$html = getHtml();	

	// Read the xml field from the levels table in the pcmanlevels database, where the levelID is the one specified in the URL
	$stmt = $conn->prepare("SELECT xml FROM levels WHERE levelID = ?");
	$stmt->bind_param("i", $_GET['level']);
	$stmt->execute();
	$stmt->bind_result($xml);
	$stmt->fetch();
	
	// If the level exists, replace the {xml} in $html with the xml from the database
	if ($xml) {
		$html = str_replace('{xml}', $xml, $html);
	}
	else {
		echo "Whoopsie!";
	}

	// Show the page
	echo $html;
}
else {
	// If we are not looking at a specific level, show the list of levels
	$stmt = $conn->prepare("SELECT levelId, levelOrder FROM levels ORDER BY levelOrder ASC");
	$stmt->execute();
	$stmt->bind_result($levelId, $levelOrder);

	// Show the list of levels, in divs. Each div has a link to the level itself,
	// a button to delete the level, and an up- and down button to reorder (delete calls delete.php?levelId=levelId,
	// up and down call reorder.php?levelId=levelId&direction=up/down)
	while ($stmt->fetch()) {
		echo '<div>';
		echo '<a href="index.php?level=' . $levelId . '">Level ' . $levelId . '</a>';
		echo '<a href="delete.php?levelId=' . $levelId . '"><button>Delete</button></a>';
		echo '<a href="reorder.php?levelId=' . $levelId . '&direction=up"><button>Up</button></a>';
		echo '<a href="reorder.php?levelId=' . $levelId . '&direction=down"><button>Down</button></a>';
		
		echo '<a href="index.php?level=">Level ' . $levelId . '</a>';
		echo '</div>';

	}
}

function getHtml() {
	// Read template.html, and store it in $html
	$html = file_get_contents('template.html');
	return $html;
}