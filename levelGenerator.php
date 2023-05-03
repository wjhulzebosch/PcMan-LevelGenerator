<?php

// call conn.php to connect to the database
require('conn.php');

// A level has the following properties:
// levelId: The ID of the level
// levelOrder: The order of the level in the list of levels
// xml: The xml of the level
// levelName: The name of the level
// levelDescription: The description of the level
// levelAuthor: The author of the level

// If an action has been provided (action can be: update, delete, reorder)
if(isset($_POST['action']))
{
	$message = "";
	// If action is update and levelInformation and a levelId have been provided, call updateLevel (levelId -1 means new level)
	if($_POST['action'] == 'update' && isset($_POST['levelInformation']) && isset($_POST['levelId']))
	{
		if(updateLevel($_POST['levelInformation'], $_POST['levelId'])) {
			$message = "Level updated";
		} else {
			$message = "ERROR: Level not updated";
		}		
	}
	// If action is delete and a levelId has been provided, call deleteLevel
	else if($_POST['action'] == 'delete' && isset($_POST['levelId']))
	{
		if(deleteLevel($_POST['levelId'])) {
			$message = "Level deleted";
		} else {
			$message = "ERROR: Level not deleted";
		}
	}
	// If action is reorder and a levelOrder has been provided, call reorderLevels
	else if($_POST['action'] == 'reorder' && isset($_POST['levelOrder']))
	{
		if(reorderLevels($_POST['levelOrder'])){
			$message = "Levels reordered";
		} else {
			$message = "ERROR: Levels not reordered";
		}
	}
	else
	{
		// If no action has been provided, return an error
		$message = "Error: No action provided";
	}
	
	// If the action was successful, return to index.php?message=$message
	header("index.php?message=" . $message);	
	
}

// If we are looking at a specific level, show that level
if(isset($_GET['levelId']))
{
	showLevel($_GET['levelId']);
	die;
}
// If we are not looking at a specific level, show the list of levels
else
{
	listLevels();
	die;
}