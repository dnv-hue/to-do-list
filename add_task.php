<?php
session_start();


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    if (empty($_POST['task_name'])) {
        // Redirect pag may error
        header('Location: index.php?error=Task name cannot be empty');
        exit;
    }
    
    require_once 'db.php';
    
    try {
        
        $taskName = trim($_POST['task_name']);
        $currentTime = date('Y-m-d H:i:s');
        $userId = $_SESSION['user_id'];
        
        $sql = "INSERT INTO tasks (task_name, is_completed, created_at, user_id) VALUES (:task_name, 0, :created_at, :user_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':task_name', $taskName);
        $stmt->bindParam(':created_at', $currentTime);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        
        header('Location: index.php?success=Task added successfully');
        exit;
    } catch (PDOException $e) {
        
        header('Location: index.php?error=' . urlencode("Database error: " . $e->getMessage()));
        exit;
    }
} else {
    
    header('Location: index.php');
    exit;
}
?>