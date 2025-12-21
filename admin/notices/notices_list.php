<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

// Search/filter inputs
$title   = $_GET['title'] ?? '';
$category = $_GET['category'] ?? '';
$status  = $_GET['status'] ?? '';
$date    = $_GET['date'] ?? '';
$search   = $_GET['search'] ?? '';

// Build dynamic SQL
$sql = "SELECT n.notice_id, n.title, n.category, n.status, n.created_at, u.name as creator
        FROM notices n
        JOIN users u ON n.created_by = u.user_id
        WHERE 1"; // 1 allows us to append AND conditions easily

$params = [];
$types = '';

if ($title) {
  $sql .= " AND n.title LIKE ?";
  $params[] = "%$title%";
  $types .= 's';
}
if ($category) {
  $sql .= " AND n.category=?";
  $params[] = $category;
  $types .= 's';
}
if ($status) {
  $sql .= " AND n.status=?";
  $params[] = $status;
  $types .= 's';
}
if ($date) {
  $sql .= " AND DATE(n.created_at)=?";
  $params[] = $date;
  $types .= 's';
}

$sql .= " ORDER BY n.created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) {
  $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$notices = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="card-body">
  <div class="card-header d-flex justify-content-between">
    <h4>Notice List</h4>
    <a href="dashboard.php?page=notices/notice_form.php" class="btn btn-primary">Add New Notice</a>
  </div>

  <!-- FILTER FORM START -->
  <form method="GET" class="mb-2 d-flex gap-2 align-items-end table-responsive">
    <table class="table table-striped">
      <th>
        <div>
          <label class="text-muted">Category</label>
          <select name="category" class="form-control">
            <option value="">All Categories</option>
            <?php
            $categories = ['Academic', 'Exams', 'Events', 'Administration', 'General'];
            foreach ($categories as $c) {
              $sel = ($category ?? '') == $c ? 'selected' : '';
              echo "<option value='$c' $sel>$c</option>";
            }
            ?>
          </select>
        </div>
      </th>

      <th>
        <div class="text-muted">
          <label>Status</label>
          <select name="status" class="form-control">
            <option value="">All Status</option>
            <option value="Draft" <?= ($status ?? '') == 'Draft' ? 'selected' : '' ?>>Draft</option>
            <option value="Published" <?= ($status ?? '') == 'Published' ? 'selected' : '' ?>>Published</option>
          </select>
        </div>
      </th>

      <th>
        <div class="text-muted">
          <label class="text-muted">Date</label>
          <input type="date" name="date" class="form-control" value="<?= !empty($date) ? date('Y-m-d', strtotime($date)) : '' ?>">

        </div>
      </th>


      <th>
        <div class="mt-4">
          <button type="submit" class="btn btn-info px-4 my-3 ">Filter</button>
        </div>
      </th>
    </table>

  </form>
  <!-- FILTER FORM END -->

  <div class="table-responsive">
    <table class="table table-striped" id="table-1">
      <thead>
        <tr>
          <th class="text-center">ID</th>
          <th>Title</th>
          <th>Category</th>
          <th>Status</th>
          <th>Created By</th>
          <th>Created At</th>
          <th>Actions</th>
        </tr>
      </thead>

      <tbody>
        <?php foreach ($notices as $row) { ?>
          <tr>
            <td class="text-center"><?= $row['notice_id'] ?></td>
            <td><?= $row['title'] ?></td>
            <td><?= $row['category'] ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= $row['creator'] ?></td>
            <td><?php echo date("d.m.Y H:i", strtotime($row['created_at'])); ?></td>
            <td class="">
              <a href="dashboard.php?page=notices/notice_form.php&id=<?= $row['notice_id'] ?>" class="btn btn-primary mr-2">
                <i class="fas fa-edit"></i> Edit
              </a>
              <!-- delete button -->
              <a href="dashboard.php?page=notices/notice_delete.php&id=<?= $row['notice_id'] ?>" class="btn btn-danger mr-2" onclick="return confirm('Are you sure?')">
                <i class="fas fa-trash"></i> Delete
              </a>
              <?php if ($row['status'] == 'Draft') { ?>
                <a href="notices/notice_publish.php?id=<?= $row['notice_id'] ?>" class="btn btn-success px-3">Publish</a>
              <?php } else { ?>
                <a href="notices/notice_publish.php?id=<?= $row['notice_id'] ?>" class="btn btn-warning">Unpublish</a>
              <?php } ?>
            </td>
          </tr>
        <?php } ?>

      </tbody>

    </table>
  </div>
</div>
</div>
</div>
</div>