<?php
session_start();
include('../config/dbconn.php');

if (!isset($_SESSION['userid'])) {
  header('Location: ../index.php');
  exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $requestId = $_POST['request_id'];
  $status = $_POST['status'];

  // Check if the status is rejected and get the comment if provided
  if ($status === 'rejected' && isset($_POST['comment'])) {
    $comment = $_POST['comment'];
  } else {
    $comment = ''; // Default empty comment
  }

  try {
    $dbh->beginTransaction();

    // Update the status of the request
    $stmt = $dbh->prepare("UPDATE tblrequest SET status = :status WHERE id = :request_id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':request_id', $requestId);
    $stmt->execute();

    // If the status is rejected, update the admin comment
    if ($status === 'rejected') {
      $stmt = $dbh->prepare("UPDATE tblrequest SET admin_comment = :comment WHERE id = :request_id");
      $stmt->bindParam(':comment', $comment);
      $stmt->bindParam(':request_id', $requestId);
      $stmt->execute();
    }

    $dbh->commit();

    // Redirect back with success message
    sleep(1); // Delay for 1 second to show the success message
    $msg = urlencode("Reports have been successfully updated");
    header("Location: ../views/admin/dashboard.php?status=success&msg=" . $msg);
    exit();
  } catch (PDOException $e) {
    // Rollback transaction on error
    $dbh->rollBack();

    // Redirect back with error message
    sleep(1); // Delay for 1 second to show the error message
    $error = urlencode("An error occurred while updating reports: " . $e->getMessage());
    header("Location: ../views/admin/dashboard.php?status=error&error=" . $error);
    exit();
  }
} else {
  // Redirect back if accessed without POST method
  header('Location: ../index.php');
  exit();
}
?>
