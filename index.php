<?php
session_start();


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';


$incompleteTasks = [];
$completedTasks = [];


try {
    
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = :user_id AND is_completed = 0 ORDER BY created_at DESC");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $incompleteTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = :user_id AND is_completed = 1 ORDER BY created_at DESC");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $completedTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="main-container">
        
        <div class="sidebar">
            <div class="sidebar-header">Todo List</div>
            <div class="sidebar-item">New Task</div>
            <div class="sidebar-item"><a href="logout.php" class="logout-link">Logout</a></div>
        </div>
        
        
        <div class="content">
            <!-- Welcome message sa taas -->
            <div class="welcome-section">
                <p>Hi, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>!!</p>
            </div>
            
            <!--    alert message ito, tanggalin ko muna
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
            <?php endif; ?> -->
            
            
            <div class="section">
                <h2>New Task</h2>
                <form action="add_task.php" method="POST" class="add-task-form">
                    <input type="text" name="task_name" placeholder="Task Name" required>
                    <button type="submit" class="add-task-btn">Add Task</button>
                </form>
            </div>
            
            
            <div class="section">
                <h2>Task Lists</h2>
                <div class="task-list">
                    <?php if (!empty($incompleteTasks)): ?>
                        <?php foreach ($incompleteTasks as $task): ?>
                            <div class="task-item">
                                <span class="task-name"><?php echo htmlspecialchars($task['task_name']); ?></span>
                                <div class="task-actions">
                                    <a href="complete_task.php?id=<?php echo $task['id']; ?>" class="btn-complete">Complete</a>
                                    <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="btn-delete">Delete</a>
                                    <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn-edit">Edit</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-tasks">No active tasks</div>
                    <?php endif; ?>
                </div>
            </div>
            
            
            <div class="section">
    <h2 style="display: flex; justify-content: space-between; align-items: center;">
        Completed Tasks
        <a href="clear_completed.php" class="btn-delete" style="margin-left: auto;" onclick="return confirm('Are you sure you want to clear all completed tasks?');">Clear</a>
    </h2>
    <div class="task-list completed-list">
        <?php if (!empty($completedTasks)): ?>
            <?php foreach ($completedTasks as $task): ?>
                <div class="task-item completed">
                    <span class="task-name"><?php echo htmlspecialchars($task['task_name']); ?></span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-tasks">No completed tasks</div>
        <?php endif; ?>
    </div>
</div>
    </div>
</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>