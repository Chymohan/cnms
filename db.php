<?php
$conn = new mysqli("localhost", "root", "", "cnms");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
