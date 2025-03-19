<?php
session_start();


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';


function isTaskOwnedByCurrentUser($conn, $taskId, $userId) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE id = :id AND user_id = :user_id");
    $stmt->bindParam(':id', $taskId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id']) && isset($_POST['task_name'])) {
    try {
        $taskId = $_POST['task_id'];
        $taskName = trim($_POST['task_name']);
        
        
        if (empty($taskName)) {
            header('Location: index.php?error=Task name cannot be empty');
            exit;
        }
        
        
        if (!isTaskOwnedByCurrentUser($conn, $taskId, $_SESSION['user_id'])) {
            header('Location: index.php?error=You do not have permission to modify this task');
            exit;
        }
        
        
        $stmt = $conn->prepare("UPDATE tasks SET task_name = :task_name WHERE id = :id");
        $stmt->bindParam(':task_name', $taskName);
        $stmt->bindParam(':id', $taskId);
        $stmt->execute();
        
        
        header('Location: index.php?success=Task updated successfully');
        exit;
    } catch (PDOException $e) {
        header('Location: index.php?error=' . urlencode("Database error: " . $e->getMessage()));
        exit;
    }
}


if (isset($_GET['id'])) {
    $taskId = $_GET['id'];
    
    
    try {
        if (!isTaskOwnedByCurrentUser($conn, $taskId, $_SESSION['user_id'])) {
            header('Location: index.php?error=You do not have permission to modify this task');
            exit;
        }
        
        
        $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->bindParam(':id', $taskId);
        $stmt->execute();
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$task) {
            header('Location: index.php?error=Task not found');
            exit;
        }
    } catch (PDOException $e) {
        header('Location: index.php?error=' . urlencode("Database error: " . $e->getMessage()));
        exit;
    }
} else {
    header('Location: index.php?error=No task specified');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main-container">
        
        <div class="sidebar">
            <div class="sidebar-header">Todo List</div>
            <div class="sidebar-item"><a href="index.php">Task List</a></div>
            <div class="sidebar-item"><a href="logout.php" class="logout-link">Logout</a></div>
        </div>
        
        
        <div class="content">
            <div class="welcome-section">
                <p>Edit Task</p>
            </div>
            
            <div class="section">
                <h2>Edit Task</h2>
                <form action="edit_task.php" method="POST" class="edit-task-form">
                    <input type="hidden" name="task_id" value="<?php echo htmlspecialchars($task['id']); ?>">
                    <input type="text" name="task_name" value="<?php echo htmlspecialchars($task['task_name']); ?>" required>
                    <div class="form-buttons">
                        <button type="submit" class="add-task-btn">Update Task</button>
                        <a href="index.php" class="btn-cancel">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>