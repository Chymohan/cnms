<?php
session_start();
session_destroy();
session_start();
$_SESSION['toast'] = [
    'message' => 'Logout Successful',
    'mode' => 'success'
];
header("Location: login.php");
exit;
?>