<?php
// Only admin can access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

// Fetch all batches
$result = $conn->query("SELECT * FROM batches ORDER BY batch_year ASC");
$batches = $result->fetch_all(MYSQLI_ASSOC);
?>

         <div class="row">
          <div class=" mx-auto col-12 col-md-8 col-lg-8">
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <h4>Batch List</h4>
                <a href="dashboard.php?page=batches/batch_form.php" class="btn btn-primary">Add new</a>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-striped" id="table-1">
                    <thead>
                      <tr>
                        <th class="text-center">
                          ID
                        </th>
                        <th>Batch Year</th>
                        <th>Created at</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                       <?php foreach($batches as $row) { ?>                   
                        <tr>
                          <td class="text-center"><?= $row['batch_id'] ?></td>
                          <td><?= $row['batch_year'] ?></td>
                          <td><?php echo date("d.m.Y H:i", strtotime($row['created_at'])); ?></td>
                          <td class="">
                            <a href="dashboard.php?page=batches/batch_form.php&id=<?= $row['batch_id'] ?>" class="btn btn-primary mr-2">
                              <i class="fas fa-edit"></i> Edit
                            </a>
                            <!-- delete button -->
                              <a href="dashboard.php?page=batches/batch_delete.php&id=<?= $row['batch_id'] ?>" class="btn btn-danger mr-2" onclick="return confirm('Are you sure?')">
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