<?php

// Only admin can delete users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$user_id = $_GET['id'] ?? null;

if($user_id){
    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    if($stmt->execute()){
    $_SESSION['toast'] = [
        'message' => 'User deleted successfully',
        'mode' => 'success'
    ];
} else {
    $_SESSION['toast'] = [
        'message' => 'Error deleting user',
        'mode' => 'error'
    ]; 
}
}

header("Location: users_list.php");
exit;
