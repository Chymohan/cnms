<?php

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$batch_id = $_GET['id'] ?? null;

if ($batch_id) {
    // Deleting batch will automatically remove students due to foreign key cascade
    $conn->query("DELETE FROM batches WHERE batch_id=$batch_id");
    $_SESSION['toast'] = [
        'message' => 'Batch deleted successfully',
        'mode' => 'success'
    ];
}

header("Location: batch_list.php");
exit;
