<?php
// call conn.php to connect to the database
require('conn.php');

global $conn;

// A level has the following properties:
// levelId: The ID of the level
// levelOrder: The order of the level in the list of levels
// xml: The xml of the level
// levelName: The name of the level
// levelDescription: The description of the level
// levelAuthor: The author of the level

// If new levelInformation has been posted, and a levelId has been provided, call updateLevel (levelId -1 means new level)
if (isset($_POST['levelInformation']) && isset($_POST['levelId'])) 
{
	$levelInformation = $_POST['levelInformation'];
	$levelId = $_POST['levelId'];
	updateLevel($levelInformation, $levelId);	
}

// If we are looking at a specific level, show that level
if(isset($_GET['levelId']))
{
	showLevel($_GET['levelId']);
}

// If we are not looking at a specific level, show the list of levels
else
{
	listLevels();
}

// If the user wants to delete the level, and a levelId has been provided, call deleteLevel
if(isset($_GET['delete']) && isset($_GET['levelId']))
{
	deleteLevel($_GET['levelId']);
}

// If the user wants to reorder the level, and a levelId and direction has been provided, call reorderLevel
if(isset($_GET['reorder']) && isset($_GET['levelId']) && isset($_GET['direction']))
{
	reorderLevel($_GET['levelId'], $_GET['direction']);
}

// Add new level or update an existing level
function updateLevel($levelInformation, $levelId) {
	global $conn;

	if ($levelId == -1) {
		$sql = "INSERT INTO levels (levelOrder, xml, levelName, levelDescription, levelAuthor) VALUES (?, ?, ?, ?, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("issss", $levelInformation['levelOrder'], $levelInformation['xml'], $levelInformation['levelName'], $levelInformation['levelDescription'], $levelInformation['levelAuthor']);
	} else {
		$sql = "UPDATE levels SET levelOrder = ?, xml = ?, levelName = ?, levelDescription = ?, levelAuthor = ? WHERE levelId = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("issssi", $levelInformation['levelOrder'], $levelInformation['xml'], $levelInformation['levelName'], $levelInformation['levelDescription'], $levelInformation['levelAuthor'], $levelId);
	}

	$stmt->execute();
	$stmt->close();
}

// Show a specific level by levelId
function showLevel($levelId) {
	global $conn;
	
	// if levelId is -1, show a new level
	if($levelId == -1)
	{
		$level = array(
			'levelId' => -1,
			'levelOrder' => 0,
			'xml' => '',
			'levelName' => '',
			'levelDescription' => '',
			'levelAuthor' => ''
		);
	}
	else {
		$sql = "SELECT * FROM levels WHERE levelId = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i", $levelId);

		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows > 0) {
			$level = $result->fetch_assoc();
		}
			
		$stmt->close();
	}
	// Display the level details as per your requirements
	showLevelHtml($level);

}

// List all levels
function listLevels() {
	global $conn;

	$sql = "SELECT * FROM levels ORDER BY levelOrder ASC";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($level = $result->fetch_assoc()) {
			// Display the level details as per your requirements
		}
	} else {
		// No levels found
	}
}

// Delete a level by levelId
function deleteLevel($levelId) {
	global $conn;

	$sql = "DELETE FROM levels WHERE levelId = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $levelId);

	$stmt->execute();
	$stmt->close();
}

// Reorder a level
function reorderLevel($levelId, $direction) {
	global $conn;

	// Get the level information
	$sql = "SELECT * FROM levels WHERE levelId = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $levelId);

	$stmt->execute();
	$result = $stmt->get_result();

	if ($result->num_rows > 0) {
		$level = $result->fetch_assoc();
		$levelOrder = $level['levelOrder'];
		$newOrder = $levelOrder + ($direction == "up" ? -1 : 1);

		// Swap the order of the current level and the adjacent level
		$sql = "UPDATE levels SET levelOrder = ? WHERE levelOrder = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("ii", $levelOrder, $newOrder);
		$stmt->execute();

		$sql = "UPDATE levels SET levelOrder = ? WHERE levelId = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("ii", $newOrder, $levelId);
		$stmt->execute();
		$stmt->close();
	} else {
		// No level found with the given levelId
	}		
}
function showLevelHtml($level) 
{
	die("hier?");
	// read the template html
	$html = file_get_contents('template.html');
	
	// replace the placeholders with the level information
	$html = str_replace('{{levelId}}', $level['levelId'], $html);
	$html = str_replace('{{xml}}', $level['xml'], $html);
	$html = str_replace('{{levelName}}', $level['levelName'], $html);
	$html = str_replace('{{levelDescription}}', $level['levelDescription'], $html);
	$html = str_replace('{{levelAuthor}}', $level['levelAuthor'], $html);
	
	// display the html
	echo $html;
}