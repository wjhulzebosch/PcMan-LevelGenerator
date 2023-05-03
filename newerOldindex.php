<?php
// call conn.php to connect to the database
require('conn.php');
require('Data/levelData.php');

global $conn;

// A level has the following properties:
// levelId: The ID of the level
// levelOrder: The order of the level in the list of levels
// xml: The xml of the level
// levelName: The name of the level
// levelDescription: The description of the level
// levelAuthor: The author of the level

if(isset($_POST['action'])) 
{
	if($_POST['action'] == "update")
	{
		// if new level (levelId -1)
		var_dump($_POST);
		die;
	}
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

function showLevelHtml($level) 
{
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