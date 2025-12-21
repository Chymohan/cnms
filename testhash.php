<?php
$password = "admin123";
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed Password: " . $hashedPassword;
echo "\n";
$password1 = "teacher123";
$hashedPassword1 = password_hash($password1, PASSWORD_DEFAULT);
echo "Hashed Password2: " . $hashedPassword1;
echo "\n";

$password2 = "student123";
$hashedPassword2 = password_hash($password2, PASSWORD_DEFAULT);
echo "Hashed Password3: " . $hashedPassword2;

?>