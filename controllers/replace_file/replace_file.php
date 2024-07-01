<?php
// Check if the form is submitted with a file
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["new_file"])) {
  // Include database connection and configuration file
  include_once "../../config/dbconn.php";

  // Get attachment ID and request ID from the form
  $attachment_id = $_POST['attachment_id'];
  $request_id = $_POST['request_id'];

  // Define the upload directory and allowed file types
  $uploadDir = "../../uploads/tblreports/file_report1/";
  $allowedTypes = array('pdf', 'doc', 'docx');

  // Ensure upload directory exists and is writable
  if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
    $error = urlencode("Upload directory does not exist or is not writable.");
    header("Location: {$_SERVER['HTTP_REFERER']}?status=error&msg=" . $error);
    exit();
  }

  // Get file details
  $fileName = $_FILES['new_file']['name'];
  $fileTmpName = $_FILES['new_file']['tmp_name'];
  $fileType = $_FILES['new_file']['type'];
  $fileSize = $_FILES['new_file']['size'];

  // Get the file extension
  $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

  // Check if the file type is allowed
  if (in_array($fileExtension, $allowedTypes)) {
    // Generate a unique file name based on the original file name
    $newFileName = $fileName; // Use the original file name as the unique name
    $uploadPath = $uploadDir . $newFileName;

    // Start transaction
    $dbh->beginTransaction();

    try {
      // Get the old file path from the database
      $stmt_old_file = $dbh->prepare("SELECT file_path FROM tblrequest_attachments WHERE id = :attachment_id");
      $stmt_old_file->bindParam(':attachment_id', $attachment_id, PDO::PARAM_INT);
      $stmt_old_file->execute();
      $old_file_path = $stmt_old_file->fetchColumn();

      // Delete the old file from the server
      if ($old_file_path && file_exists($old_file_path)) {
        unlink($old_file_path);
      }

      // Update the file path in the database
      $sql = "UPDATE tblrequest_attachments SET file_path = :file_path WHERE id = :attachment_id";
      $stmt = $dbh->prepare($sql);
      $stmt->bindParam(':file_path', $uploadPath, PDO::PARAM_STR);
      $stmt->bindParam(':attachment_id', $attachment_id, PDO::PARAM_INT);
      $stmt->execute();

      // Move the uploaded file to the server
      if (!move_uploaded_file($fileTmpName, $uploadPath)) {
        throw new Exception("File upload failed.");
      }

      // Commit transaction
      $dbh->commit();

      // Redirect to the previous page with a success message
      $msg = urlencode("File updated successfully.");
      header("Location: {$_SERVER['HTTP_REFERER']}?status=success&msg=" . $msg);
      exit();
    } catch (Exception $e) {
      // Rollback transaction on failure
      $dbh->rollBack();

      // Log the error message
      error_log("Error: " . $e->getMessage());

      // Redirect with an error message
      $error = urlencode($e->getMessage());
      header("Location: {$_SERVER['HTTP_REFERER']}?status=error&msg=" . $error);
      exit();
    }
  } else {
    // Redirect with an error message if file type is not allowed
    $error = urlencode("File type not allowed.");
    header("Location: {$_SERVER['HTTP_REFERER']}?status=error&msg=" . $error);
    exit();
  }
} else {
  // Redirect with an error message if form is not submitted correctly
  $error = urlencode("Form submission error.");
  header("Location: {$_SERVER['HTTP_REFERER']}?status=error&msg=" . $error);
  exit();
}
?>
