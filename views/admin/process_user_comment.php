<?php
session_start();
include('../../config/dbconn.php');

// Check if user is authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

// Function to sanitize input
function sanitizeInput($input)
{
  return htmlspecialchars(trim($input));
}

// Initialize $msg and $error variables
$msg = '';
$error = '';

// Check if form is submitted and process the comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_comment'])) {
  $report_id = $_POST['report_id'];
  $user_id = $_SESSION['userid'];
  $comment = sanitizeInput($_POST['user_comment']);

  // Insert comment into tblcomments
  $insertCommentQuery = "INSERT INTO tblcomments (report_id, user_id, comment, created_at)
                         VALUES (:report_id, :user_id, :comment, NOW())";
  $insertStmt = $dbh->prepare($insertCommentQuery);
  $insertStmt->bindParam(':report_id', $report_id, PDO::PARAM_INT);
  $insertStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
  $insertStmt->bindParam(':comment', $comment, PDO::PARAM_STR);

  if ($insertStmt->execute()) {
    $msg = "Comment added successfully"; // Success message
  } else {
    $error = "Failed to add comment"; // Error message
  }
} else {
  $error = "Unauthorized access"; // Unauthorized access error
}

// Redirect back to the report page after comment is posted
header("Location: view_reports.php?status=success&msg= . $msg");
exit();
