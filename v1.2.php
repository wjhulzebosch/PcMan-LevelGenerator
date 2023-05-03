<?php
$action = $_POST['action'];
echo $action; die;
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
    <h1>CRUD System</h1>
    <?php if (!isset($action)): ?>
        <table>
            <thead>
                <tr>
                    <th>Level ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($levels as $level) : ?>
                    <tr>
                        <td><?php echo $level['levelId']; ?></td>
                        <td>
                            <a href="?edit=<?php echo $level['levelId']; ?>">Edit</a> |
                            <form action="" method="post" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="levelId" value="<?php echo $level['levelId']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="?new">New Level</a>
    <?php endif; ?>
    
    <?php if (isset($action)): ?>
        <form action="" method="post">
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <input type="hidden" name="levelId" value="<?php echo $levelId; ?>">
            <label for="xmlBody">XML Body:</label>
            <textarea name="xmlBody" id="xmlBody" rows="10"><?php echo isset($level) ? $level['xmlBody'] : ''; ?></textarea>
            <button type="submit"><?php echo $action == 'update' ? 'Save' : 'Create'; ?></button>
        </form>
    <?php endif; ?>
</body>
</html>
