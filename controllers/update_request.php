<?php
session_start();
include('../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}
// Initialize variables
$msg = '';
$error = '';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // Get the request ID, admin comment, and admin ID from the form
  $request_id = $_POST['request_id'];
  $admin_comment = $_POST['admin_comment'];
  $admin_id = $_SESSION['userid']; // Assuming the admin ID is stored in a session variable

  // Check which button was clicked (approve or reject)
  if (isset($_POST['approve_request'])) {
    // Update the request status to 'approved' and set the approved timestamp
    $approved_at = date('Y-m-d H:i:s');
    $stmt = $dbh->prepare("UPDATE tblrequest SET status = 'approved', approved_at = :approved_at, approved_by = :admin_id, admin_comment = :admin_comment WHERE id = :request_id");
    $stmt->bindParam(':approved_at', $approved_at);
  } elseif (isset($_POST['reject_request'])) {
    // Update the request status to 'rejected' and set the rejected timestamp
    $rejected_at = date('Y-m-d H:i:s');
    $stmt = $dbh->prepare("UPDATE tblrequest SET status = 'rejected', rejected_at = :rejected_at, rejected_by = :admin_id, admin_comment = :admin_comment WHERE id = :request_id");
    $stmt->bindParam(':rejected_at', $rejected_at);
  }

  // Bind parameters and execute the statement for updating tblrequest
  $stmt->bindParam(':admin_id', $admin_id);
  $stmt->bindParam(':admin_comment', $admin_comment);
  $stmt->bindParam(':request_id', $request_id);

  // Execute the statement
  if ($stmt->execute()) {
    // Set success message
    $msg = "Request status updated successfully!";
  } else {
    // Set error message
    $error = "An error occurred while updating the request status.";
  }
}

// Redirect back to the previous page
header("Location: {$_SERVER['HTTP_REFERER']}");
exit();
