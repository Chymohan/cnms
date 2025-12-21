<!DOCTYPE html>
<html>

<head>
    <title> About Page</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/tailwindcss.js"></script>
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
                        <a href="index.html" class="nav-link relative text-gray-700 hover:text-blue-600 font-medium text-sm sm:text-base md:text-base transition-colors">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="about.php" class="nav-link relative text-gray-700 hover:text-blue-600 font-medium text-sm sm:text-base md:text-base transition-colors">
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



    <div class="container mx-auto px-4 py-10">
        <!-- Hero Section -->
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold  text-gray-800">About CNMS</h1>
            <p class="mt-3 text-gray-600 text-lg">
                College/Notice Management System
            </p>
        </div>

        <!-- Project Overview -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Project Overview</h2>
            <p class="text-gray-700 leading-relaxed">
                CNMS (College/Notice Management System) is a web-based application designed to help colleges manage users, batches, and notices efficiently.
                It allows teachers and students to access information easily, and helps administrators keep track of everything in one system.
            </p>
        </div>

        <!-- Team Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Team</h2>
            <p class="text-gray-700 leading-relaxed">
                This project was developed by Mohan Chaudhary and Raj Bastola.
            </p>
        </div>

        <!-- Technologies -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Technologies Used</h2>
            <p class="text-gray-700 leading-relaxed">
                PHP, MySQL, Tailwind CSS, Bootstrap, JavaScript, HTML, CSS
            </p>
        </div>

        <!-- Version / Updates -->
        <div class="text-center text-gray-600">
            <p>Version: 1.0 | Last Updated: January 2026</p>
        </div>
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
        const currentPage = window.location.pathname.split("/").pop();

        document.querySelectorAll(".nav-link").forEach(link => {
            if (link.getAttribute("href") === currentPage) {
                link.classList.remove("text-gray-700");
                link.classList.add("text-blue-600", "underline", "font-semibold");
            }
        });
    </script>
</body>

</html>