<?php

/* ---------------- ONLY ADMIN ---------------- */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: " . BASE_URL . "login.php");
  exit;
}

/* ---------------- FETCH BATCHES ---------------- */
$batches_result = $conn->query("SELECT * FROM batches ORDER BY batch_year ASC");
$batches = $batches_result->fetch_all(MYSQLI_ASSOC);

/* ---------------- EDIT MODE ---------------- */
$user_id = $_POST['user_id'] ?? $_GET['id'] ?? null;
$editing = false;
$current_batch = null;
$user = ['name' => '', 'email' => '', 'role' => 'student', 'profile_image' => ''];

if ($user_id) {
  $editing = true;

  $stmt = $conn->prepare("SELECT * FROM users WHERE user_id=?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $userRow = $stmt->get_result()->fetch_assoc();
  if ($userRow) $user = $userRow;

  $stmt = $conn->prepare("SELECT batch_id FROM students WHERE user_id=?");
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $batchRow = $stmt->get_result()->fetch_assoc();
  $current_batch = $batchRow['batch_id'] ?? null;
}

/* ---------------- FORM SUBMIT ---------------- */
if (isset($_POST['submit'])) {

  $name     = trim($_POST['name']);
  $email    = trim($_POST['email']);
  $role     = $_POST['role'];
  $batch_id = $_POST['batch'] ?? null;

  /* ---------- IMAGE / AVATAR ---------- */
  $profileImage = $user['profile_image'] ?? null;
  $uploadDir = __DIR__ . "/../../uploads/img/profiles/";

  if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
  }

  if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];

    if (in_array($ext, $allowed)) {
      $profileImage = "profile_" . time() . "_" . rand(100, 999) . "." . $ext;
      move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $profileImage);
    }
  } elseif (!$editing) {
    // Auto-generate avatar for new user
    $parts = explode(" ", $name);
    $initials = strtoupper($parts[0][0] . ($parts[1][0] ?? ''));

    $size = 300;
    $img = imagecreatetruecolor($size, $size);
    $bg = imagecolorallocate($img, 0, 102, 255);
    $txt = imagecolorallocate($img, 255, 255, 255);
    imagefilledrectangle($img, 0, 0, $size, $size, $bg);

    $font = __DIR__ . "/../../assets/fonts/nunito-v9-latin-800.ttf";
    $fontSize = 120;
    $box = imagettfbbox($fontSize, 0, $font, $initials);

    $x = ($size - ($box[2] - $box[0])) / 2;
    $y = ($size - ($box[1] - $box[7])) / 2 + ($box[1] - $box[7]);

    imagettftext($img, $fontSize, 0, $x, $y, $txt, $font, $initials);

    $profileImage = "profile_" . time() . "_" . rand(100, 999) . ".png";
    imagepng($img, $uploadDir . $profileImage);
    imagedestroy($img);
  }

  /* ---------- DATABASE ---------- */
  if ($editing) {
    // UPDATE USER
    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role=?, profile_image=? WHERE user_id=?");
    $stmt->bind_param("ssssi", $name, $email, $role, $profileImage, $user_id);
    $stmt->execute();

    // UPDATE STUDENT BATCH
    if ($role === 'student' && $batch_id) {
      $stmt = $conn->prepare("
                INSERT INTO students (user_id, batch_id) VALUES (?, ?)
                ON DUPLICATE KEY UPDATE batch_id=?
            ");
      $stmt->bind_param("iii", $user_id, $batch_id, $batch_id);
      $stmt->execute();
    } else {
      $stmt = $conn->prepare("DELETE FROM students WHERE user_id=?");
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
    }

    $_SESSION['toast'] = [
      'message' => 'User Updated successfully',
      'mode' => 'success'
    ];
  } else {
    // CREATE USER
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name,email,role,password,profile_image) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $name, $email, $role, $password, $profileImage);
    $stmt->execute();

    $new_id = $stmt->insert_id;

    if ($role === 'student' && $batch_id) {
      $stmt = $conn->prepare("INSERT INTO students (user_id, batch_id) VALUES (?, ?)");
      $stmt->bind_param("ii", $new_id, $batch_id);
      $stmt->execute();
    }

    $_SESSION['toast'] = [
      'message' => 'User Added successfully',
      'mode' => 'success'
    ];
  }

  // ---------------- HEADER REDIRECT ----------------
  header("Location: dashboard.php?page=users/users_list.php");
  exit;
}
ob_end_flush()
?>


<div class="row">

  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between">
        <h4><?= $editing ? 'Edit User' : 'Add New User' ?></h4>
        <a href="dashboard.php?page=users/users_list.php" class="btn btn-primary">Go Back</a>
      </div>
      <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">
          <?php if ($editing): ?>
            <input type="hidden" name="user_id" value="<?= $user_id ?>">
          <?php endif; ?>

          <div class="row">
            <div class="mb-2 col-6">
              <label for="name">Full Name <span class="text-danger">*</span></label>
              <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>">
            </div>
            <div class="mb-2 col-6">
              <label for="email">Email <span class="text-danger">*</span></label>
              <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>">
            </div>
            <?php if (!$editing): ?>
              <div class="mb-2 col-6 position-relative">
                <label for="password">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" id="passwordField"
                  class="form-control" value="">

                <!-- Toggle Icon -->
                <i id="togglePasswordField"
                  class="fa fa-eye position-absolute"
                  style="right: 10px; top: 38px; margin-top: 8px; margin-right: 13px; cursor: pointer; color: #6c757d;">
                </i>
              </div>
            <?php endif; ?>

            <div class="mb-2 col-6">
              <label for="role">Select Role <span class="text-danger">*</span></label>
              <select name="role" id="roleSelect" class="form-control ">
                <option value="student" <?= $user['role'] == 'student' ? 'selected' : '' ?>>Student</option>
                <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="teacher" <?= $user['role'] == 'teacher' ? 'selected' : '' ?>>Teacher</option>
              </select>
            </div>
            <div class="mb-2 col-6" id="batchDiv">
              <label for="batch">Select Your Batch <span class="text-danger">*</span></label>
              <select name="batch" id="batch" class="form-control ">
                <?php foreach ($batches as $b): ?>
                  <option value="<?= $b['batch_id'] ?>" <?= $b['batch_id'] == $current_batch ? 'selected' : '' ?>>
                    <?= $b['batch_year'] ?>
                  </option>

                <?php endforeach; ?>
              </select>
            </div>
            <div class="mb-2 col-6">
              <label for="image">Profile Image </label>
              <input type="file" name="image" id="image" class="form-control" value="">
              <?php if ($editing && $user['profile_image']): ?>
                <img src="<?= BASE_URL ?>uploads/img/profiles/<?= $user['profile_image'] ?>" width="80" style="margin-bottom:10px;"><br>
              <?php endif; ?>
            </div>
            <div class="col-12">
              <button type="submit" name="submit" class="btn btn-success"> <?= $editing ? 'Update User' : 'Add New User' ?></button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  const passwordField = document.getElementById('passwordField');
  const togglePasswordField = document.getElementById('togglePasswordField');

  togglePasswordField.addEventListener('click', () => {
    if (passwordField.type === 'password') {
      passwordField.type = 'text';
      togglePasswordField.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
      passwordField.type = 'password';
      togglePasswordField.classList.replace('fa-eye-slash', 'fa-eye');
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