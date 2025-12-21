<?php
session_start();
require("../db.php");
require("../lib/tcpdf/tcpdf.php"); // TCPDF library

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['student', 'teacher'])) {
    exit("Unauthorized access");
}

$notice_id = (int)($_GET['id'] ?? 0);

$sql = "SELECT n.notice_id, n.title, n.description, n.category, n.created_at, n.attachment
        FROM notices n
        JOIN users u ON n.created_by = u.user_id
        WHERE n.notice_id = ? AND n.status='Published' AND u.role='admin'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $notice_id);
$stmt->execute();
$notice = $stmt->get_result()->fetch_assoc();

if (!$notice) exit("Notice not found");

/* ================= PDF SETTINGS ================= */

// Create PDF with header/footer disabled
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

// Disable auto-header and auto-footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins: left=1.25 inches (31.75mm), others=1 inch (25mm)
$left_margin = 31.75;  // 1.25 inches
$other_margin = 25;    // 1 inch
$pdf->SetMargins($left_margin, $other_margin, $other_margin);
$pdf->SetAutoPageBreak(true, $other_margin);
$pdf->AddPage();

// Line spacing
$pdf->SetCellHeightRatio(1.5);

// Draw page border manually
$page_width = $pdf->getPageWidth();
$page_height = $pdf->getPageHeight();

$pdf->SetLineWidth(0.5); // 0.5 mm thickness
$pdf->Rect(
    $left_margin,      // X start (inside left margin)
    $other_margin,     // Y start (inside top margin)
    $page_width - $left_margin - $other_margin, // Width inside margins
    $page_height - 2 * $other_margin, // Height inside margins
    'D' // Draw border
);

// Set content padding: 0.5mm from ALL sides of the border
$content_padding = 0.5; // 0.5mm padding from border

// Calculate content area boundaries
$content_start_x = $left_margin + $content_padding;
$content_start_y = $other_margin + $content_padding;
$content_width = ($page_width - $left_margin - $other_margin) - (2 * $content_padding);

// Start content with 0.5mm padding from border
$pdf->SetXY($content_start_x, $content_start_y);

// Also limit the maximum width for content
$pdf->SetRightMargin($other_margin + $content_padding);

/* ================= CONTENT ================= */

// Main heading (title) - Times New Roman, font size 14
$pdf->SetFont('times', 'B', 14);
// Use MultiCell for centered text with width limit
$pdf->MultiCell($content_width, 10, $notice['title'], 0, 'C', false, 1, $content_start_x);
$pdf->Ln(5);

// Reset X position for content
$pdf->SetX($content_start_x);

// Subheading / paragraph - Times New Roman, font size 12
$pdf->SetFont('times', '', 12);
$pdf->Cell($content_width, 8, "Category: " . $notice['category'], 0, 1);
$pdf->SetX($content_start_x);
$pdf->Cell($content_width, 8, "Date: " . date("d M Y", strtotime($notice['created_at'])), 0, 1);
$pdf->Ln(5);

// Summernote content (paragraphs) - Times New Roman, font size 12
$pdf->SetX($content_start_x);
// Use writeHTMLCell to respect content width
$pdf->writeHTMLCell($content_width, 0, $content_start_x, '', $notice['description'], 0, 1, false, true, '', true);
$pdf->Ln(8);

/* ================= ATTACHMENT ================= */

if (!empty($notice['attachment'])) {
    $file_path = "../uploads/" . $notice['attachment'];

    if (file_exists($file_path)) {
        $pdf->SetFont('times', 12);
        $pdf->SetX($content_start_x);
        $pdf->Cell($content_width, 8, "--- Attachment: " . $notice['attachment'] . " ---", 0, 1);
        $pdf->Ln(2);

        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

        // Merge plain text attachments
        if (in_array($ext, ['txt', 'csv', 'log'])) {
            $content = file_get_contents($file_path);
            $pdf->SetX($content_start_x);
            $pdf->writeHTMLCell($content_width, 0, $content_start_x, '', nl2br(htmlspecialchars($content)), 0, 1, false, true, '', true);
        }
        // PDF or other files cannot merge
        else {
            $pdf->SetX($content_start_x);
            $pdf->writeHTMLCell(
                $content_width,
                0,
                $content_start_x,
                '',
                "<i>[Attachment exists but cannot be merged into PDF. Download separately.]</i>",
                0,
                1,
                false,
                true,
                '',
                true
            );
        }
    }
}

$pdf->Output("notice_{$notice_id}.pdf", "D");
exit;
