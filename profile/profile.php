<?php


if (!isset($_SESSION['user_id'])) {
  header("Location: " . BASE_URL . "/cnms/");
  exit;
}

$user_id = $_SESSION['user_id'];
$role    = $_SESSION['role'];

// Base user info
$sql = "SELECT u.user_id, u.name, u.email, u.role, u.profile_image, u.created_at,
               b.batch_year
        FROM users u
        LEFT JOIN students s ON u.user_id = s.user_id
        LEFT JOIN batches b ON s.batch_id = b.batch_id
        WHERE u.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<div class="card col-md-6 offset-md-3 px-5">
  <div class="card-header">
    <h4>Personal Details</h4>
  </div>
  <div>
    <div class="card-body text-center">

      <?php if (!empty($user['profile_image'])): ?>
        <img src="<?= BASE_URL ?>uploads/img/profiles/<?= $user['profile_image'] ?>" alt="user" class="rounded-circle" width="120">
      <?php endif; ?>
      <h5 class="mt-3 text-muted">Profile Image</h5>
    </div>
    <div class="card-body">
      <div class="py-4">
        <p class="clearfix">
          <span class="float-left">
            Full Name
          </span>
          <span class="float-right text-muted">
            <?= $user['name'] ?>
          </span>
        </p>
        <p class="clearfix">
          <span class="float-left">
            Email Id
          </span>
          <span class="float-right text-muted">
            <?= $user['email'] ?>
          </span>
        </p>
        <?php if ($user['role'] === 'student') { ?>
          <p class="clearfix">
            <span class="float-left">
              Batch Year
            </span>
            <span class="float-right text-muted">
              <?= $user['batch_year'] ?>
            </span>
          </p>
        <?php } ?>

        <p class="clearfix">
          <span class="float-left">
            <a href="dashboard.php?page=../profile/edit_profile.php">Edit Profile</a>
          </span>
          <span class="float-right text-muted">
            <a href="dashboard.php?page=fnotices_list.php">Back to Notices</a>
          </span>
        </p>

      </div>
    </div>
  </div>
</div>