
<?php


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

// Fetch all users into array
$sql = "SELECT u.user_id, u.name, u.email, u.role, u.profile_image, u.created_at , b.batch_year 
        FROM users u
        LEFT JOIN students s ON u.user_id = s.user_id
        LEFT JOIN batches b ON s.batch_id = b.batch_id
        ORDER BY u.user_id ASC";
$result = $conn->query($sql);

$users = $result->fetch_all(MYSQLI_ASSOC); // fetch all as associative array
?>


  <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <h4>User Data</h4>
                <a href="dashboard.php?page=users/user_form.php" class="btn btn-primary">Add new</a>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped" id="table-1">
                    <thead>
                      <tr>
                        <th class="text-center">
                          ID
                        </th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Batch</th>
                        <th>Profile Image</th>
                        <th>Created at</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                       <?php foreach($users as $row) { ?>                   
                        <tr>
                          <td class="text-center"><?= $row['user_id'] ?></td>
                          <td><?= $row['name'] ?></td>
                          <td><?= $row['email'] ?></td>
                          <td><?= $row['role'] ?></td>
                          <td><?= $row['batch_year'] ?? '-' ?></td>
                          <td class="d-flex justify-content-center align-items-center text-center">
                            <img src="<?= BASE_URL ?>uploads/img/profiles/<?= htmlspecialchars($row['profile_image']) ?>"
                              alt="profile_image" style="width:50px;height:50px;border-radius:50%;">
                          </td>
                          <td><?php echo date("d.m.Y H:i", strtotime($row['created_at'])); ?></td>
                          <td class="">
                            <a href="dashboard.php?page=users/user_form.php&id=<?= $row['user_id'] ?>" class="btn btn-primary mr-2">
                              <i class="fas fa-edit"></i> Edit
                            </a>
                            <!-- delete button -->
                              <a href="dashboard.php?page=users/user_delete.php&id=<?= $row['user_id'] ?>" class="btn btn-danger mr-2" onclick="return confirm('Are you sure?')">
                              <i class="fas fa-trash"></i> Delete
                            </a>
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
