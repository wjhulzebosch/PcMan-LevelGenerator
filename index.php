<?php
require_once 'levelData.php';



// Initialize levelData object
$levelData = new LevelData();

// Check if there's a request to create, update, delete, or swap level order
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'create' || $_POST['action'] == 'update') {
        $xmlBody = $_POST['xml-output'];
        $levelData->saveLevel($levelId, $xmlBody);
    } elseif ($_POST['action'] == 'delete') {
        $levelId = intval($_POST['levelId']);
        $levelData->deleteLevel($levelId);
    }
    header('Location: index.php');
} elseif (isset($_GET['up']) || isset($_GET['down'])) {
    $levelId = intval($_GET['up'] ?? $_GET['down']);
    $direction = isset($_GET['up']) ? 'up' : 'down';
    $levelData->swapLevelOrder($levelId, $direction);
    header('Location: index.php');
}

// Check if there's a request to edit a specific level or create a new level
if (isset($_GET['edit']) || isset($_GET['new'])) {
    if (isset($_GET['edit'])) {
        $levelId = intval($_GET['edit']);
        $level = $levelData->getLevel($levelId);
        $action = 'update';
    } else {
        $levelId = -1;
        $action = 'create';
    }
}

$levels = $levelData->getAllLevels();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>PcMan LevelGenerator</h1>
    <?php if (!isset($action)): ?>
        <table>
            <thead>
                <tr>
                    <th>Level ID</th>
                    <th>Level Order</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($levels as $level) : ?>
                    <tr>
                        <td><?php echo $level['levelId']; ?></td>
                        <td><?php echo $level['levelOrder']; ?></td>
                        <td>
                            <a href="?edit=<?php echo $level['levelId']; ?>">Edit</a> |
                            <form action="" method="post" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="levelId" value="<?php echo $level['levelId']; ?>">
                                <button type="submit">Delete</button>
                            </form> |
                            <a href="?up=<?php echo $level['levelId']; ?>">Up</a> |
                            <a href="?down=<?php echo $level['levelId']; ?>">Down</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="?edit=-1">New Level</a>
    <?php endif; ?>

    <?php if (isset($action)) 
    {
	    // read the contents of Templates/levelTemplate.html
        $levelTemplate = file_get_contents('Templates/levelTemplate.html');
		
		// if the current level exists, and the xmlBody isset
		if (isset($level) && isset($level['xmlBody'])) 
		{
			// replace the placeholder in the levelTemplate with the xmlBody
			$levelTemplate = str_replace('{{xmlBody}}', $level['xmlBody'], $levelTemplate);
		} 
		else 
		{
			// otherwise, replace the placeholder with an empty string
			$levelTemplate = str_replace('{{xmlBody}}', '', $levelTemplate);
		}
		
		// display the levelTemplate
		echo $levelTemplate;
    }
	?>

</body>
</html>
