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
<html>

<head>
    <title> Login Page</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/tailwindcss.js"></script>
    <link rel="stylesheet" href="assets/extra/css/all.min.css">

</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav class="sticky top-0 bg-slate-200 shadow-md z-10">
        <div class="container mx-auto px-4 sm:px-6 py-4 sm:py-5 md:py-6 lg:py-7">
            <div class="flex items-center justify-between">
                <div class=" flex justify-center my-auto text-black-500 text-xl sm:text-2xl md:text-2xl font-bold   ">
                    <a href=""> <img alt="image" src="assets/img/logo.png" class="h-12 " />
                    </a>
                    <span
                        class="logo-name my-auto">CNMS</span>

                </div>
                <ul class="flex space-x-3 sm:space-x-4 md:space-x-6">
                    <li>
                        <a href="index.html" class="nav-link relative hover:text-blue-600 font-medium text-sm sm:text-base md:text-base transition-colors">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="about.php" class="nav-link relative hover:text-blue-600 font-medium text-sm sm:text-base md:text-base transition-colors">
                            About
                        </a>
                    </li>
                    <li>
                        <a href="login.php" class="nav-link relative bg-blue-400 hover:bg-blue-700 text-white px-3 py-1.5 sm:px-4 sm:py-2 md:px-4 md:py-2 rounded-lg font-medium text-sm sm:text-base md:text-base hover:shadow-md transition-all">
                            Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <!-- Login Form Container -->
    <div class="flex-1 flex justify-center items-center px-4 sm:px-6 py-8 sm:py-12">
        <form method="POST" class="bg-slate-100 rounded-xl shadow-xl w-full max-w-md sm:max-w-lg md:max-w-lg lg:max-w-xl p-4 sm:p-5 md:p-6 lg:p-8">
            <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold text-center text-gray-800 mb-4 sm:mb-6">
                Login
            </h2>

            <?php if (isset($error)) {
                echo '<div class="mb-3 sm:mb-4 p-3 bg-red-50 text-red-600 text-sm sm:text-base rounded-lg border border-red-200">' . $error . '</div>';
            } ?>

            <!-- Role Selection -->
            <div class="mb-3 sm:mb-4">
                <label class="block text-gray-700 font-medium mb-1 sm:mb-2 text-sm sm:text-base md:text-lg">
                    Select Role
                </label>
                <select name="role" id="roleSelect" class="w-full px-3 py-2.5 sm:py-3 md:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm sm:text-base md:text-lg">
                    <option value="student" selected>Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <!-- Email/User ID -->
            <div class="mb-3 sm:mb-4">
                <label class="block text-gray-700 font-medium mb-1 sm:mb-2 text-sm sm:text-base md:text-lg">
                    Email / User ID
                </label>
                <input type="text" name="username" placeholder="Enter your email or user ID"
                    class="w-full px-3 py-2.5 sm:py-3 md:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm sm:text-base md:text-lg" required>
            </div>

            <!-- Batch Field -->
            <div class="mb-3 sm:mb-4" id="batchDiv">
                <label class="block text-gray-700 font-medium mb-1 sm:mb-2 text-sm sm:text-base md:text-lg">
                    Batch
                </label>
                <input type="text" name="batch" placeholder="e.g., 2023"
                    class="w-full px-3 py-2.5 sm:py-3 md:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm sm:text-base md:text-lg">
            </div>

            <!-- Password -->
            <div class="mb-4 sm:mb-6 relative">
                <label class="block text-gray-700 font-medium mb-1 sm:mb-2 text-sm sm:text-base md:text-lg">
                    Password
                </label>

                <input type="password" name="password" id="password" placeholder="Enter your password"
                    class="w-full px-3 py-2.5 sm:py-3 md:py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all text-sm sm:text-base md:text-lg pr-10"
                    required>

                <!-- Toggle Icon -->
                <i id="togglePassword"
                    class="fa-solid fa-eye absolute right-3 top-1/2 mt-4 mr-4 -translate-y-1/2 cursor-pointer text-gray-500">
                </i>
            </div>


            <!-- Submit Button -->
            <button type="submit" name="login"
                class="w-full bg-blue-400 hover:bg-blue-700 text-white font-medium py-2.5 sm:py-3 md:py-3.5 rounded-lg hover:shadow-lg transition-all duration-300 text-sm sm:text-base md:text-lg">
                Login
            </button>
        </form>
    </div>

    <!-- Footer -->
    <footer class="bg-slate-200 border-t border-gray-200 py-4 sm:py-5 md:py-6 lg:py-7">
        <div class="container mx-auto px-4">
            <p class="text-center text-black uppercase  text-xs sm:text-sm md:text-base font-family: Arial, sans-serif; font-[16px]; font-bold">
                © 2026 All Right Reserved. Designed By Mohan Chaudhary & Raj Bastola
            </p>
        </div>
    </footer>
    <script>
        const password = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');

        togglePassword.addEventListener('click', () => {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Toggle icon
            togglePassword.classList.toggle('fa-eye');
            togglePassword.classList.toggle('fa-eye-slash');
        });
        // Get current page
        const currentPage = window.location.pathname.split("/").pop();

        document.querySelectorAll(".nav-link").forEach(link => {
            if (link.getAttribute("href") === currentPage) {
                link.classList.remove("text-gray-700");
                link.classList.add("text-blue-600", "underline", "font-semibold");
            }
        });
    </script>

    <script>
        const roleSelect = document.getElementById('roleSelect');
        const batchDiv = document.getElementById('batchDiv');

        roleSelect.addEventListener('change', function() {
            batchDiv.style.display = (this.value === 'student') ? 'block' : 'none';
        });

        if (roleSelect.value !== 'student') batchDiv.style.display = 'none';
    </script>

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