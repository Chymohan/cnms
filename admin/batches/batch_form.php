<?php
ob_start();
/* ---------------- ONLY ADMIN ---------------- */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "login.php");
    exit;
}

/* ---------------- FETCH BATCHES ---------------- */
$batch_id = $_GET['id'] ?? null;
$editing = false;
$batch_year = '';

if($batch_id){
    $editing = true;
    $row = $conn->query("SELECT * FROM batches WHERE batch_id=$batch_id")->fetch_assoc();
    $batch_year = $row['batch_year'];
}

if(isset($_POST['submit'])){
    $batch_year_input = trim($_POST['batch_year']);

    if($editing){
        $stmt = $conn->prepare("UPDATE batches SET batch_year=? WHERE batch_id=?");
        $stmt->bind_param("si", $batch_year_input, $batch_id);
        if($stmt->execute()){
            $_SESSION['toast'] = [
                'message' => 'Batch Updated Successfully',
                'mode' => 'success'
            ];
        } else {
            $_SESSION['toast'] = [
                'message' => 'Failed to Update Batch',
                'mode' => 'error'
            ];
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO batches (batch_year) VALUES (?)");
        $stmt->bind_param("s", $batch_year_input);
        $stmt->execute();
        $_SESSION['toast'] = [
            'message' => 'Batch Added Successfully',
            'mode' => 'success'
        ];
    }
    header("Location: dashboard.php?page=batches/batch_list.php");
    exit;
}
ob_end_flush();
?>

 <div class="row">

          <div class=" mx-auto col-12 col-md-6 col-lg-6">
            <div class="card">
              <div class="card-header d-flex justify-content-between">
                <h4><?= $editing ? 'Edit Batch' : 'Add New Batch' ?></h4>
                <a href="dashboard.php?page=batches/batch_list.php" class="btn btn-primary">Go Back</a>
              </div>
              <div class="card-body">
                <form action="" method="POST" >
                  <div class="row">
                    <div class="mb-2  ml-5 mx-auto col-10    ">
                      <label for="batch_year">Batch Year <span class="text-danger">*</span></label>
                      <input type="text" name="batch_year" id="batch_year" class="form-control" placeholder="Batch Year (e.g., 2023)" value="<?= htmlspecialchars($batch_year) ?>" required>
                    </div>
                    <div class="col-6  mx-auto">
                      <button type="submit" name="submit" class="btn px-5 btn-success"> <?= $editing ? 'Update Batch' : 'Add Batch' ?></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

<script>
const roleSelect = document.getElementById('roleSelect');
const batchDiv = document.getElementById('batchDiv');

function toggleBatch() {
    batchDiv.style.display = roleSelect.value === 'student' ? 'block' : 'none';
}
roleSelect.addEventListener('change', toggleBatch);
toggleBatch();
</script>

