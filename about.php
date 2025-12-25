<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Page - CNMS</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="assets/extra/css/all.min.css">
    <script src="js/tailwindcss.js"></script>
    <link rel='shortcut icon' type='image/x-icon' href='assets/img/favicon.ico' />
</head>

<body class="bg-[#F5F5F5] min-h-screen">

    <!-- Navigation with mobile sidebar -->
    <nav class=" top-0 bg-[#FFFFFF] shadow-md z-50">
        <div class="container mx-auto px-4 sm:px-6 py-4 sm:py-5 md:py-6 lg:py-6">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <a href="index.html">
                        <img alt="logo" src="assets/img/logo.png" class="h-10 sm:h-11 md:h-12" />
                    </a>
                    <span class="logo-name text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 my-auto">CNMS</span>
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
        <div id="mobile-sidebar" class="fixed inset-y-0 right-0 z-50 w-72 sm:w-80 bg-[#FFFFFF] shadow-2xl transform translate-x-full transition-transform duration-300 ease-in-out md:hidden">
            <div class="flex justify-between items-center p-6 border-b border-gray-300">
                <span class="text-xl font-bold text-gray-800">Menu</span>
                <button id="close-sidebar" class="text-gray-700 hover:text-gray-900">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="flex flex-col p-6 space-y-5">
                <a href="index.html" class="nav-link text-gray-800 hover:text-blue-600 text-lg font-medium transition px-4 py-3 hover:bg-white/60 rounded-lg">Home</a>
                <a href="about.php" class="nav-link text-gray-800 hover:text-blue-600 text-lg font-medium transition px-4 py-3 hover:bg-white/60 rounded-lg">About</a>
                <a href="login.php" class="nav-link bg-blue-400 hover:bg-blue-600 text-white text-lg font-medium transition px-4 py-3 rounded-lg hover:shadow-md">Login</a>
            </div>
        </div>

        <!-- Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40 hidden transition-opacity duration-300 md:hidden"></div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 container mx-auto px-4 sm:px-6 py-10 md:py-12 min-h-screen">
        <div class="text-center mb-10 md:mb-12">
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-gray-800">About CNMS</h1>
            <p class="mt-3 text-gray-600 text-lg sm:text-xl">College Notice Management System</p>
        </div>

        <div class="max-w-4xl mx-auto space-y-10">
            <!-- Project Overview -->
            <div>
                <h2 class="text-2xl sm:text-3xl font-semibold text-gray-800 mb-4">Project Overview</h2>
                <p class="text-gray-700 leading-relaxed text-base sm:text-lg">
                    CNMS (College Notice Management System) is a web-based application designed to help colleges manage users, batches, and notices efficiently.
                    It allows teachers and students to access information easily, and helps administrators keep track of everything in one system.
                </p>
            </div>

            <!-- Team -->
            <div>
                <h2 class="text-2xl sm:text-3xl font-semibold text-gray-800 mb-4">Team</h2>
                <p class="text-gray-700 leading-relaxed text-base sm:text-lg">
                    This project was developed by Mohan Chaudhary and Raj Bastola.
                </p>
            </div>

            <!-- Technologies -->
            <div>
                <h2 class="text-2xl sm:text-3xl font-semibold text-gray-800 mb-4">Technologies Used</h2>
                <p class="text-gray-700 leading-relaxed text-base sm:text-lg">
                    PHP, MySQL, Tailwind CSS, Bootstrap, JavaScript, HTML, CSS
                </p>
            </div>

            <!-- Version -->
            <div class="text-center text-gray-600 text-base sm:text-lg">
                <p>Version: 1.0 | Last Updated: January 2026</p>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-[#FFFFFF] border-t border-gray-200 py-6 mt-auto">
        <div class="container mx-auto px-4 text-center">
            <p class="text-black uppercase text-center text-sm sm:text-base font-bold">
                © 2026 All Rights Reserved. Designed By Mohan Chaudhary & Raj Bastola
            </p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="js/main.js"></script>

</body>

</html>