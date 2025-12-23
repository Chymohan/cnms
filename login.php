<?php
session_start();
include "db.php"; // Database connection

if (isset($_POST['login'])) {
    $role = $_POST['role'];
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $batch_input = isset($_POST['batch']) ? trim($_POST['batch']) : null;

    if ($role === 'student') {
        // Student login using email
        $sql = "SELECT user_id, name, password FROM users WHERE email = ? AND role = 'student'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $user_id = $user['user_id'];

            // Check batch assignment
            $sql2 = "SELECT * FROM students s
                     JOIN batches b ON s.batch_id = b.batch_id
                     WHERE s.user_id = ? AND b.batch_year = ?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("is", $user_id, $batch_input);
            $stmt2->execute();
            $result2 = $stmt2->get_result();

            if ($result2 && $result2->num_rows === 1) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['role'] = 'student';
                    $_SESSION['name'] = $user['name'];

                    $_SESSION['toast'] = [
                        'message' => 'Login Successful',
                        'mode' => 'success'
                    ];

                    header("Location: frontend/dashboard.php");
                    exit;
                } else {
                    $error = "❌ Incorrect Password";
                }
            } else {
                $error = "❌ Unauthorized Access: Invalid Batch";
            }
        } else {
            $error = "❌ Invalid Email or Not a Student";
        }
    } elseif ($role === 'teacher') {
        // Teacher login (redirect to same frontend)
        $sql = "SELECT * FROM users WHERE (user_id = ? OR email = ?) AND role = 'teacher'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = 'teacher';
                $_SESSION['name'] = $user['name'];

                $_SESSION['toast'] = [
                    'message' => 'Login Successful',
                    'mode' => 'success'
                ];

                header("Location: frontend/dashboard.php");
                exit;
            } else {
                $error = "❌ Incorrect Password";
            }
        } else {
            $error = "❌ Unauthorized Access";
        }
    } elseif ($role === 'admin') {
        // Admin login
        $sql = "SELECT * FROM users WHERE (user_id = ? OR email = ?) AND role = 'admin'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = 'admin';
                $_SESSION['name'] = $user['name'];

                $_SESSION['toast'] = [
                    'message' => 'Welcome back Admin',
                    'mode' => 'info'
                ];

                header("Location: admin/dashboard.php");
                exit;
            } else {
                $error = "❌ Incorrect Password";
            }
        } else {
            $error = "❌ Unauthorized Access";
        }
    } else {
        $error = "❌ Invalid Role Selected";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page - CNMS</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="assets/extra/css/all.min.css">
    <script src="js/tailwindcss.js"></script>
    <link rel='shortcut icon' type='image/x-icon' href='assets/img/favicon.ico' />

</head>

<body class="bg-slate-80 min-h-screen">

    <!-- Navigation with mobile sidebar -->
    <nav class="sticky top-0 bg-[#EBF4DD] shadow-md z-50">
        <div class="container mx-auto px-4 sm:px-6 py-4 sm:py-5 md:py-6 lg:py-7">
            <div class="flex items-center justify-between">

                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <a href="index.html">
                        <img alt="logo" src="assets/img/logo.png" class="h-10 sm:h-11 md:h-12" />
                    </a>
                    <span class="logo-name text-xl sm:text-2xl md:text-2xl font-bold text-gray-800 my-auto">CNMS</span>
                </div>

                <!-- Desktop menu -->
                <ul class="hidden md:flex items-center space-x-6 md:space-x-8 lg:space-x-10">
                    <li>
                        <a href="index.html" class="nav-link text-gray-700 hover:text-blue-600 font-medium text-base lg:text-lg transition">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="about.php" class="nav-link text-gray-700 hover:text-blue-600 font-medium text-base lg:text-lg transition">
                            About
                        </a>
                    </li>
                    <li>
                        <a href="login.php" class="nav-link bg-blue-400 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium text-base lg:text-lg hover:shadow-md transition">
                            Login
                        </a>
                    </li>
                </ul>

                <!-- Mobile Hamburger -->
                <button id="mobile-menu-btn" class="md:hidden text-gray-800 focus:outline-none p-2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" id="menu-icon-path" />
                    </svg>
                </button>

            </div>
        </div>

        <!-- Mobile Sidebar -->
        <div id="mobile-sidebar" class="
    fixed inset-y-0 right-0 z-50 w-72 sm:w-80 bg-[#EBF4DD] shadow-2xl
    transform translate-x-full transition-transform duration-300 ease-in-out
    md:hidden
  ">
            <div class="flex justify-between items-center p-6 border-b border-gray-300">
                <span class="text-xl font-bold text-gray-800">Menu</span>
                <button id="close-sidebar" class="text-gray-700 hover:text-gray-900">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex flex-col p-6 space-y-5">
                <a href="index.html" class="nav-link text-gray-800 hover:text-blue-600 text-lg font-medium transition px-4 py-3 hover:bg-white/60 rounded-lg">
                    Home
                </a>
                <a href="about.php" class="nav-link text-gray-800 hover:text-blue-600 text-lg font-medium transition px-4 py-3 hover:bg-white/60 rounded-lg">
                    About
                </a>
                <a href="login.php" class="nav-link bg-blue-400 hover:bg-blue-600 text-white text-lg font-medium transition px-4 py-3 rounded-lg hover:shadow-md">
                    Login
                </a>
            </div>
        </div>

        <!-- Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden transition-opacity duration-300 md:hidden"></div>
    </nav>

    <!-- Login Form Container -->
    <div class="flex-1 flex items-center justify-center px-4 py-10 sm:px-6 sm:py-12 md:px-8 lg:py-16 bg-gradient-to-br from-blue-50/30 to-indigo-50/30 min-h-screen">
        <form
            method="POST"
            class="bg-slate-90 rounded-2xl shadow-xl w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl 
           p-6 sm:p-8 md:p-10 lg:p-12 space-y-6 sm:space-y-7 md:space-y-8">
            <!-- Heading -->
            <h2 class="text-3xl sm:text-4xl md:text-4xl lg:text-5xl font-bold text-center text-gray-800 tracking-tight">
                Login
            </h2>

            <!-- Error Message -->
            <?php if (isset($error)): ?>
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-center text-base sm:text-lg">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Role Selection -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2 text-base sm:text-lg">Select Role</label>
                <select name="role" id="roleSelect"
                    class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base sm:text-lg transition-all duration-200">
                    <option value="student" selected>Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <!-- Email / User ID -->
            <div>
                <label class="block text-gray-700 font-semibold mb-2 text-base sm:text-lg">Email / User ID</label>
                <input type="text" name="username" placeholder="Enter your email or user ID" required
                    class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base sm:text-lg transition-all duration-200" />
            </div>

            <!-- Batch Field -->
            <div id="batchDiv">
                <label class="block text-gray-700 font-semibold mb-2 text-base sm:text-lg">Batch Year</label>
                <input type="text" name="batch" placeholder="e.g., 2023"
                    class="w-full px-4 py-3.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base sm:text-lg transition-all duration-200" />
            </div>

            <!-- Password with Eye Icon -->
            <div class="relative">
                <label class="block text-gray-700 font-semibold mb-2 text-base sm:text-lg">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required
                    class="w-full px-4 py-3.5 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base sm:text-lg transition-all duration-200" />
                <!-- Eye Icon (positioned perfectly) -->
                <i id="togglePassword"
                    class="fa-solid fa-eye absolute right-4 top-1/2  mt-4 mr-1 -translate-y-1/2 text-gray-500 hover:text-gray-700 cursor-pointer text-xs sm:text-xl transition-colors duration-200">
                </i>
            </div>

            <!-- Submit Button -->
            <button type="submit" name="login"
                class="w-full bg-blue-500 hover:bg-blue-600 active:bg-blue-700 text-white font-semibold py-3.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 text-base sm:text-lg md:text-xl">
                Login
            </button>
        </form>
    </div>

    <!-- footer -->
    <footer class="bg-[#EBF4DD] border-t border-gray-200 py-6 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <p class="text-black uppercase text-sm sm:text-base font-bold">
                © 2026 All Rights Reserved. Designed By Mohan Chaudhary & Raj Bastola
            </p>
        </div>
    </footer>

    <script>
        // Password toggle visibility
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePassword');

        if (toggleIcon && passwordInput) {
            toggleIcon.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle eye icon (open ↔ closed)
                toggleIcon.classList.toggle('fa-eye');
                toggleIcon.classList.toggle('fa-eye-slash');
            });
        }

        // Your existing batch field toggle script
        const roleSelect = document.getElementById('roleSelect');
        const batchDiv = document.getElementById('batchDiv');
        roleSelect.addEventListener('change', function() {
            batchDiv.style.display = (this.value === 'student') ? 'block' : 'none';
        });
        if (roleSelect.value !== 'student') batchDiv.style.display = 'none';
    </script>
    <script src="js/main.js"></script>
    <script src="js/sweetalert2.all.min.js"></script>
    <script src="js/toast.js"></script>

    <?php if (isset($_SESSION['toast'])): ?>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                showToast(
                    "<?= htmlspecialchars($_SESSION['toast']['message']) ?>",
                    "<?= $_SESSION['toast']['mode'] ?>"
                );
            });
        </script>
    <?php unset($_SESSION['toast']);
    endif; ?>
</body>

</html>