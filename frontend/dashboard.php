<?php
ob_start();
session_start();
require "../db.php";
define('BASE_URL', '/cnms/');

/* ADMIN ONLY */
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['student', 'teacher'])) {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

/* Get user info */
$user_id = $_SESSION['user_id'];
$sql = "SELECT profile_image FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* Determine content page */
$page = $_GET['page'] ?? 'fnotices_list.php';

/* Security: whitelist allowed pages */
$allowed_pages = [
    'fnotices_list.php',
    '404_error.php',
    'view_notice.php',
    '../profile/profile.php',
    '../profile/edit_profile.php'
];
if (!in_array($page, $allowed_pages, true)) {
    $page = '404_error.php';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User </title>
    <link rel="stylesheet" href="../assets/css/app.min.css">
    <link rel="stylesheet" href="../assets/bundles/bootstrap-social/bootstrap-social.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/components.css">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel='shortcut icon' type='image/x-icon' href='../assets/img/favicon.ico' />

    <!-- Font Awesome 6 (latest & free) -->
    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet" href="../assets/bundles/datatables/datatables.min.css">
    <link rel="stylesheet" href="../assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">




</head>

<body>

    <div class="loader"></div>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar sticky">
                <div class="form-inline mr-auto">
                    <ul class="navbar-nav mr-3">
                        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg
									collapse-btn"> <i data-feather="align-justify"></i></a></li>
                        <li><a href="#" class="nav-link nav-link-lg fullscreen-btn">
                                <i data-feather="maximize"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <ul class="navbar-nav navbar-right">
                    <li class="dropdown"><a href="#" data-toggle="dropdown"
                            class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <?php if (!empty($user['profile_image'])): ?>
                                <img alt="image" src="../uploads/img/profiles/<?= $user['profile_image'] ?>"
                                    <?php endif; ?>
                                    class="user-img-radious-style"> <span class="d-sm-none d-lg-inline-block"></span></a>

                    </li>
                </ul>
            </nav>
            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper" class="">
                    <div class="sidebar-brand">
                        <a href="dashboard.php"> <img alt="image" src="../assets/img/logo.png" class="header-logo" /> <span
                                class="logo-name">CNMS</span>
                        </a>
                    </div>
                    <div class="">
                        <ul class="sidebar-menu">
                            <li class="menu-header">Main</li>


                            <li class="dropdown">
                                <a href="dashboard.php?page=fnotices_list.php"
                                    class="nav-link <?= ($page == 'fnotices_list.php') ? 'active' : '' ?>">
                                    <i data-feather="file-text"></i><span>Notices</span>
                                </a>
                            </li>

                            <li class="dropdown">
                                <a href="#" class="menu-toggle nav-link has-dropdown <?=
                                                                                        ($page == '../profile/profile.php' || $page == '../profile/edit_profile.php') ? 'active' : '' ?>">
                                    <i data-feather="user"></i><span>Profile</span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="nav-link <?= ($page == '../profile/profile.php') ? 'active' : '' ?>"
                                            href="dashboard.php?page=../profile/profile.php">View Profile</a></li>
                                    <li><a class="nav-link <?= ($page == '../profile/edit_profile.php') ? 'active' : '' ?>"
                                            href="dashboard.php?page=../profile/edit_profile.php">Edit Profile</a></li>
                                </ul>
                            </li>

                            <li class="dropdown">
                                <a href="../logout.php" class="nav-link"><i data-feather="log-out"></i><span>Logout</span></a>
                            </li>


                        </ul>
                    </div>
                </aside>
            </div>
            <!-- Main Content -->
            <div class="main-content">


                <!-- starts table -->
                <?php
                if (in_array($page, $allowed_pages)) {
                    include $page;
                } else {
                    header("Location: dashboard.php?page=404_error.php");
                    exit;
                } ?>

                <div class="settingSidebar">
                    <a href="javascript:void(0)" class="settingPanelToggle"> <i class="fa fa-spin fa-cog"></i>
                    </a>
                    <div class="settingSidebar-body ps-container ps-theme-default">
                        <div class=" fade show active">
                            <div class="setting-panel-header">Setting Panel
                            </div>
                            <div class="p-15 border-bottom">
                                <h6 class="font-medium m-b-10">Select Layout</h6>
                                <div class="selectgroup layout-color w-50">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="value" value="1" class="selectgroup-input-radio select-layout" checked>
                                        <span class="selectgroup-button">Light</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="value" value="2" class="selectgroup-input-radio select-layout">
                                        <span class="selectgroup-button">Dark</span>
                                    </label>
                                </div>
                            </div>
                            <div class="p-15 border-bottom">
                                <h6 class="font-medium m-b-10">Sidebar Color</h6>
                                <div class="selectgroup selectgroup-pills sidebar-color">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="icon-input" value="1" class="selectgroup-input select-sidebar">
                                        <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip"
                                            data-original-title="Light Sidebar"><i class="fas fa-sun"></i></span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="icon-input" value="2" class="selectgroup-input select-sidebar" checked>
                                        <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip"
                                            data-original-title="Dark Sidebar"><i class="fas fa-moon"></i></span>
                                    </label>
                                </div>
                            </div>
                            <div class="p-15 border-bottom">
                                <h6 class="font-medium m-b-10">Color Theme</h6>
                                <div class="theme-setting-options">
                                    <ul class="choose-theme list-unstyled mb-0">
                                        <li title="white" class="active">
                                            <div class="white"></div>
                                        </li>
                                        <li title="cyan">
                                            <div class="cyan"></div>
                                        </li>
                                        <li title="black">
                                            <div class="black"></div>
                                        </li>
                                        <li title="purple">
                                            <div class="purple"></div>
                                        </li>
                                        <li title="orange">
                                            <div class="orange"></div>
                                        </li>
                                        <li title="green">
                                            <div class="green"></div>
                                        </li>
                                        <li title="red">
                                            <div class="red"></div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="p-15 border-bottom">
                                <div class="theme-setting-options">
                                    <label class="m-b-0">
                                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input"
                                            id="mini_sidebar_setting">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="control-label p-l-10">Mini Sidebar</span>
                                    </label>
                                </div>
                            </div>
                            <div class="p-15 border-bottom">
                                <div class="theme-setting-options">
                                    <label class="m-b-0">
                                        <input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input"
                                            id="sticky_header_setting">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="control-label p-l-10">Sticky Header</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mt-4 mb-4 p-3 align-center rt-sidebar-last-ele">
                                <a href="#" class="btn btn-icon icon-left btn-primary btn-restore-theme">
                                    <i class="fas fa-undo"></i> Restore Default
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="main-footer ">
                <div class="footer-center d-flex justify-content-center align-items-center">
                    <p class="w-full font-16 text-center " style=" color: black; font-family: Arial, sans-serif; ">© 2026 All Right Reserved. Designed By Mohan Chaudhary & Raj Bastola</p>
                </div>

            </footer>
        </div>
    </div>

    <!-- Scripts -->
    <!-- Your existing scripts -->
    <script src="../assets/js/app.min.js"></script>
    <script src="../assets/js/scripts.js"></script>
    <script src="../assets/js/custom.js"></script>

    <!-- JS Libraies -->
    <script src="../assets/bundles/apexcharts/apexcharts.min.js"></script>
    <!-- Page Specific JS File -->
    <script src="../assets/js/page/index.js"></script>

    <script src="../assets/bundles/datatables/datatables.min.js"></script>
    <script src="../assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="../assets/js/page/datatables.js"></script>

    <!-- for toast  -->
    <script src="../js/sweetalert2.all.min.js"></script>
    <script src="../js/toast.js"></script>

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