<?php


// Only admin can access
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'){
    header("Location: " . BASE_URL . "login.php");
    exit;
}

// Check if editing
$notice_id = $_GET['id'] ?? null;
$editing = false;
$notice = ['title'=>'','description'=>'','category'=>'Academic','status'=>'Draft','attachment'=>''];

if($notice_id){
    $editing = true;
    $stmt = $conn->prepare("SELECT * FROM notices WHERE notice_id=?");
    $stmt->bind_param("i", $notice_id);
    $stmt->execute();
    $notice = $stmt->get_result()->fetch_assoc();
}

if(isset($_POST['submit'])){
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $status = $_POST['status'] ?? 'Draft';
    $created_by = $_SESSION['user_id'];

    // File upload
    $attachment = $notice['attachment'] ?? '';
    if(isset($_FILES['attachment']) && $_FILES['attachment']['name']){
        $file_name = time().'_'.$_FILES['attachment']['name'];
        move_uploaded_file($_FILES['attachment']['tmp_name'], 'uploads/'.$file_name);
        $attachment = $file_name;
    }

    if($editing){
        $stmt = $conn->prepare("UPDATE notices SET title=?, description=?, category=?, status=?, attachment=? WHERE notice_id=?");
        $stmt->bind_param("sssssi", $title, $description, $category, $status, $attachment, $notice_id);
        if($stmt->execute()){
            $_SESSION['toast'] = [ 
                'message' => 'Notice updated successfully',
                'mode' => 'success'
            ];
        } else {
            $_SESSION['toast'] = [
                'message' => 'Error updating notice',
                'mode' => 'error'
            ]; 
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO notices (title, description, category, status, attachment, created_by) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("sssssi", $title, $description, $category, $status, $attachment, $created_by);
        if($stmt->execute()){
            $_SESSION['toast'] = [
                'message' => 'Notice added successfully',
                'mode' => 'success'
            ];
        } else {
            $_SESSION['toast'] = [
                'message' => 'Error adding notice',
                'mode' => 'error'
            ];  
        }
    }

    header("Location: dashboard.php?page=notices/notices_list.php");
    exit;
}
ob_end_flush();
?>

 <div class="row">

          <div class="col-12">
            <div class="card">
              <div class="card-header d-flex justify-content-between ">
                <h4 class="text-muted"><?= $editing ? 'Edit Notice' : 'Add New Notice' ?></h4>
                <a href="dashboard.php?page=notices/notices_list.php" class="btn btn-primary">Go Back</a>
              </div>
              <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">

                  <div class="row">
                    <div class="mb-2 col-6">
                      <label for="title">Title <span class="text-danger">*</span></label>
                      <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($notice['title']) ?>">
                    </div>
                    <div class="mb-2 col-6">
                      <label for="category">Category <span class="text-danger">*</span></label>
                      <select name="category" id="category" class="form-control">
                        <?php 
                        $categories = ['Academic','Exams','Events','Administration','General'];
                        foreach($categories as $c){
                            $selected = $notice['category']==$c?'selected':'';
                            echo "<option value='$c' $selected>$c</option>";
                        }
                        ?>
                      </select>
                    </div>
                    <div class="col-12">
                      <label for="description">Description <span class="text-danger">*</span></label>
                        <textarea name="description" class="summernote-simple"><?= htmlspecialchars($notice['description']) ?></textarea>
                    </div>
                    
                    <?php if($_SESSION['role']=='admin'){ ?>
                    <div class="mb-2 col-6">    
                        <label for="status">Status <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control">
                            <option value="Draft" <?= $notice['status']=='Draft'?'selected':'' ?>>Draft</option>
                            <option value="Published" <?= $notice['status']=='Published'?'selected':'' ?>>Published</option>
                        </select>   
                    </div>
                    <?php } ?>     
                    <div class="mb-2 col-6">
                      <label for="attachment">Attachment </label>
                      <input type="file" name="attachment" id="attachment" class="form-control" value="">
                      <?php if($editing && $notice['attachment']): ?>     
        <a href="<?php echo BASE_URL; ?>uploads/<?= $notice['attachment'] ?>" target="_blank">View Current Attachment</a><br>
    <?php endif; ?>
                    </div>
                    <div class="col-12">
                      <button type="submit" name="submit" class="btn btn-success"> <?= $editing ? 'Update Notice' : 'Add Notice' ?> </button>
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