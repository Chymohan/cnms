<?php

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['student', 'teacher'])) {
        header("Location: " . BASE_URL . "login.php");
        exit;
    }

$notice_id = (int)($_GET['id'] ?? 0);

$sql = "SELECT n.notice_id, n.title, n.description, n.category, n.attachment, n.created_at
        FROM notices n
        JOIN users u ON n.created_by = u.user_id
        WHERE n.notice_id = ? AND n.status='Published' AND u.role='admin'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $notice_id);
$stmt->execute();
$notice = $stmt->get_result()->fetch_assoc();

if (!$notice) die("Notice not found");

$attachment_filename = '';

if (!empty($notice['attachment'])) {
    $file_path = "../uploads/" . $notice['attachment'];
    $file_exists = file_exists($file_path);
    $file_ext = pathinfo($notice['attachment'], PATHINFO_EXTENSION);
    $attachment_filename = $notice['attachment'];
    $attachment_download_link = BASE_URL . "uploads/" . $notice['attachment'];

    if ($file_exists && in_array(strtolower($file_ext), ['txt', 'csv', 'log'])) {
        $attachment_content = htmlspecialchars(file_get_contents($file_path));
    }
}
?>
                <!-- starts table -->
                <div class="card col-md-7 offset-md-3 px-5">
                    <div class="card-header">
                        <h3><?= htmlspecialchars($notice['title']) ?></h3>
                    </div>

                    <div class="card-body">
                        <div class="py-4">
                            <p class="clearfix">
                                <span class="float-left">
                                    <strong> Category :</strong>
                                </span>
                                <span class="float-center  ml-4">
                                    <?= $notice['category'] ?>
                                </span>
                            </p>
                            <p class="clearfix">
                                <span class="float-left">
                                    <strong>Date:</strong>
                                </span>
                                <span class="float-center  ml-4">
                                    <?= date("d M Y", strtotime($notice['created_at'])) ?>
                                </span>
                            </p>
                            <hr>
                            <div class="form-group text-black"><pre><?= $notice['description'] ?></pre></div>
                            <hr>

                            <?php if (!empty($notice['attachment']) && file_exists("../uploads/".$notice['attachment'])): ?>
                            <hr>
                            <?php if (!empty($attachment_content)) : ?>
                                <!-- Display content for text-based attachments -->
                                <div class="form-group text-black">
                                    <pre><strong> --Attachment Content--<br> </strong><?= $attachment_content ?></pre>
                                </div>
                            <?php else : ?>
                                <p><i>Preview not available for this file type. Please download to view .</i></p>
                            <?php endif; ?>

                            <hr>
                            <?php endif; ?>

                            <p class="clearfix">
                                <span class="float-left">
                                    <?php if (!empty($notice['attachment'])): ?>

                                        <a href="<?= BASE_URL ?>uploads/<?= htmlspecialchars($notice['attachment']) ?>" download><button class="btn btn-primary"><i class="fa fa-download"></i> Download Attachment</button></a>

                                    <?php endif; ?>

                                </span>
                                <span class="float-right text-muted">
                                    <a href="download_notice_pdf.php?id=<?= $notice['notice_id'] ?>" class="btn btn-info"><i class="fas fa-file-pdf"></i> Download Notice as PDF</a>
                                </span>


                            </p>

                        </div>
                    </div>
                </div>
