<?php

session_start();
require "../../db.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

$notice_id = $_GET['id'] ?? null;
if ($notice_id) {
    $current_status = $conn->query("SELECT status FROM notices WHERE notice_id=$notice_id")->fetch_assoc()['status'];
    $new_status = $current_status == 'Draft' ? 'Published' : 'Draft';

    $stmt = $conn->prepare("UPDATE notices SET status=? WHERE notice_id=?");
    $stmt->bind_param("si", $new_status, $notice_id);

    if ($stmt->execute()) {
        $_SESSION['toast'] = [
            'message' => $new_status == 'Published' ? 'Notice published successfully' : 'Notice unpublished successfully',
            'mode' => 'success'
        ];
    } else {
        $_SESSION['toast'] = [
            'message' => 'Error updating notice status',
            'mode' => 'error'
        ];
    }
}

header("Location: ../dashboard.php?page=notices/notices_list.php");
exit;
