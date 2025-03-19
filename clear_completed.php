<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';

try {
    // Delete all completed tasks for the current user
    $stmt = $conn->prepare("DELETE FROM tasks WHERE user_id = :user_id AND is_completed = 1");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    
    // Redirect back to the main page with success message
    header('Location: index.php?success=Completed tasks cleared successfully');
    exit;
} catch (PDOException $e) {
    // Redirect back with error message if something goes wrong
    header('Location: index.php?error=Failed to clear completed tasks');
    exit;
}
?>