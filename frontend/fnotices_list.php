 <?php


    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['student', 'teacher'])) {
        header("Location: " . BASE_URL . "login.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $name    = $_SESSION['name'];

    // Handle filter
    $category = $_GET['category'] ?? '';
    $date     = $_GET['date'] ?? '';

    $sql = "SELECT n.notice_id, n.title, n.category, n.created_at, n.attachment
        FROM notices n
        JOIN users u ON n.created_by = u.user_id
        WHERE n.status='Published' AND u.role='admin'";

    $params = [];
    $types = '';


    if ($category) {
        $sql .= " AND n.category=?";
        $params[] = $category;
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
<!-- contents here -->
 <h2 class="ml-5">Welcome,<?php if ($_SESSION['role'] === 'student') echo " Student";
                            else echo " Teacher"; ?> <?= htmlspecialchars($name) ?></h2>

 <div class="card-body px-">

     <!-- FILTER FORM START -->
     <form method="GET" class="mb-2 d-flex gap-2 align-items-end table-responsive px-4 col-md-12">
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

     <div class="table-responsive px-4 col-md-12">
         <table class="table table-striped" id="table-1">
             <thead>
                 <tr>
                     <th>Title</th>
                     <th>Category</th>
                     <th>Date</th>
                     <th>Action</th>
                 </tr>
             </thead>

             <tbody>
                 <?php foreach ($notices as $row) { ?>
                     <tr>
                         <td><?= $row['title'] ?></td>
                         <td><?= $row['category'] ?></td>
                         <td><?= date("d M Y", strtotime($row['created_at'])) ?></td>


                         <td class="">
                             <a href="download_notice_pdf.php?id=<?= $row['notice_id'] ?>" class="btn btn-primary mr-2 ml-3">
                                 <i class="fas fa-file-pdf"></i> Download PDF
                             </a>
                             <!-- delete button -->
                             <a href="dashboard.php?page=view_notice.php&id=<?= $row['notice_id'] ?>" class="btn btn-success mr-2">
                                 <i class="far fa-eye"></i> View Notice
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