<?php
session_start();
include "../../db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$notice_id = $_GET['id'] ?? null;

if ($notice_id) {
    // Delete notice file
    $file = $conn->query("SELECT attachment FROM notices WHERE notice_id=$notice_id")->fetch_assoc()['attachment'];
    if ($file && file_exists("uploads/$file")) unlink("uploads/$file");

    // Delete notice
    $stmt = $conn->prepare("DELETE FROM notices WHERE notice_id=?");
    $stmt->bind_param("i", $notice_id);
    if ($stmt->execute()) {
        $_SESSION['toast'] = [
            'message' => 'Notice deleted successfully',
            'mode' => 'success'
        ];
    } else {
        $_SESSION['toast'] = [
            'message' => 'Error deleting notice',
            'mode' => 'error'
        ];
    }
}

header("Location: notices_list.php");
exit;
