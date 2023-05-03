<?php
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
	}

	// Show a button to create a new level
	echo '<a href="?levelId=-1">Add level</a>';
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

public function swapLevelOrder($levelId, $direction) {
    $currentLevel = $this->getLevel($levelId);
    if ($direction === 'up') {
        $query = "SELECT * FROM levels WHERE levelOrder < ? ORDER BY levelOrder DESC LIMIT 1";
    } else {
        $query = "SELECT * FROM levels WHERE levelOrder > ? ORDER BY levelOrder ASC LIMIT 1";
    }
    $stmt = $this->conn->prepare($query);
    $stmt->bind_param('i', $currentLevel['levelOrder']);
    $stmt->execute();
    $result = $stmt->get_result();
    $otherLevel = $result->fetch_assoc();

    if ($otherLevel) {
        $query = "UPDATE levels SET levelOrder = ? WHERE levelId = ?";
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param('ii', $otherLevel['levelOrder'],$currentLevel['levelId']);
		$stmt->execute();
		    $stmt->bind_param('ii', $currentLevel['levelOrder'], $otherLevel['levelId']);
    $stmt->execute();
}


?>