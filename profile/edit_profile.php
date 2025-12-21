<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "/cnms/");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ================= FETCH USER ================= */
$stmt = $conn->prepare("
    SELECT u.user_id, u.name, u.email, u.role, u.profile_image,
           s.batch_id, b.batch_year
    FROM users u
    LEFT JOIN students s ON u.user_id = s.user_id
    LEFT JOIN batches b ON s.batch_id = b.batch_id
    WHERE u.user_id=?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

/* ================= FETCH ALL BATCHES ================= */
$batches_result = $conn->query("SELECT batch_id, batch_year FROM batches ORDER BY batch_year ASC");
$batches = $batches_result->fetch_all(MYSQLI_ASSOC);

/* ================= UPDATE PROFILE ================= */
if (isset($_POST['update'])) {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email)) {
        $_SESSION['toast'] = [
            'message' => 'Name and Email cannot be empty',
            'mode' => 'error'
        ];
    } else {

        // Check email uniqueness
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email=? AND user_id!=?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            $_SESSION['toast'] = [
                'message' => 'Email already in use',
                'mode' => 'error'
            ];
        } else {

            /* ===== PROFILE IMAGE ===== */
            if (!empty($_FILES['profile_image']['name'])) {

                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed)) {
                    $_SESSION['toast'] = [
                        'message' => 'Invalid file type (JPG, PNG, GIF allowed)',
                        'mode' => 'error'
                    ];
                } else {

                    $newname = 'user_' . $user_id . '_' . time() . '.' . $ext;
                    $target = "../uploads/img/profiles/" . $newname;

                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
                        $stmt = $conn->prepare("UPDATE users SET profile_image=? WHERE user_id=?");
                        $stmt->bind_param("si", $newname, $user_id);
                        $stmt->execute();
                        $user['profile_image'] = $newname;
                    }
                }
            }

            /* ===== UPDATE USER DATA ===== */
            if (empty($password)) {
                $stmt = $conn->prepare("UPDATE users SET name=?, email=? WHERE user_id=?");
                $stmt->bind_param("ssi", $name, $email, $user_id);
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=? WHERE user_id=?");
                $stmt->bind_param("sssi", $name, $email, $hashed, $user_id);
            }
            $stmt->execute();

            /* ===== UPDATE STUDENT BATCH (ONLY IF STUDENT) ===== */
            if ($user['role'] === 'student' && isset($_POST['batch'])) {
                $batch_id = (int)$_POST['batch'];
                $stmt = $conn->prepare("UPDATE students SET batch_id=? WHERE user_id=?");
                $stmt->bind_param("ii", $batch_id, $user_id);
                $stmt->execute();
                $user['batch_id'] = $batch_id;
            }

            $_SESSION['name'] = $name;
            $user['name'] = $name;
            $user['email'] = $email;

            $_SESSION['toast'] = [
                'message' => 'Profile Updated Successfully',
                'mode' => 'success'
            ];
        }
        header("Location: dashboard.php?page=../profile/profile.php");
        exit;
    }
}
ob_end_flush();
?>

<div class="row">
    <div class="mx-auto col-12 col-md-6 col-lg-6">
        <div class="card">

            <div class="card-header d-flex justify-content-between">
                <h4>Edit Profile</h4>
                <a href="dashboard.php?page=../profile/profile.php" class="btn btn-primary">Go Back</a>
            </div>

            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-2 ml-5 mx-auto col-10">
                        <label>Full Name *</label>
                        <input type="text" name="name" class="form-control"
                            value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>

                    <div class="mb-2 ml-5 mx-auto col-10">
                        <label>Email *</label>
                        <input type="email" name="email" class="form-control"
                            value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>

                    <?php if ($user['role'] === 'student'): ?>
                        <div class="mb-2 ml-5 mx-auto col-10">
                            <label>Batch *</label>
                            <select name="batch" id="batch" class="form-control">
                                <?php foreach ($batches as $b): ?>
                                    <option value="<?= $b['batch_id'] ?>"
                                        <?= ($b['batch_id'] == $user['batch_id']) ? 'selected' : '' ?>>
                                        <?= $b['batch_year'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>


                        </div>
                    <?php endif; ?>

                    <div class="mb-2 ml-5 mx-auto col-10 position-relative">
                        <label>New Password</label>

                        <input type="password" name="password" id="newPassword"
                            class="form-control"
                            placeholder="Leave blank to keep current">

                        <!-- Toggle Icon -->
                        <i id="toggleNewPassword"
                            class="fa fa-eye position-absolute"
                            style="right: 15px; top: 38px; margin-top: 8px; margin-right: 10px; cursor: pointer; color: #6c757d;">
                        </i>
                    </div>

                    <div class="mb-2 ml-5 mx-auto col-10">
                        <label>Profile Picture</label>
                        <input type="file" name="profile_image" class="form-control">

                        <?php if (!empty($user['profile_image'])): ?>
                            <img src="../uploads/img/profiles/<?= $user['profile_image'] ?>"
                                width="80" height="80"
                                style="border-radius:50%; margin-top:10px;">
                        <?php endif; ?>
                    </div>

                    <div class="col-6 mx-auto">
                        <!-- FIXED name -->
                        <button type="submit" name="update" class="btn px-5 btn-success">
                            Update Profile
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
    const newPassword = document.getElementById('newPassword');
    const toggleNewPassword = document.getElementById('toggleNewPassword');

    toggleNewPassword.addEventListener('click', () => {
        if (newPassword.type === 'password') {
            newPassword.type = 'text';
            toggleNewPassword.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            newPassword.type = 'password';
            toggleNewPassword.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });

    const roleSelect = document.getElementById('roleSelect');
    const batchDiv = document.getElementById('batchDiv');

    function toggleBatch() {
        batchDiv.style.display = roleSelect.value === 'student' ? 'block' : 'none';
    }
    roleSelect.addEventListener('change', toggleBatch);
    toggleBatch();
</script>