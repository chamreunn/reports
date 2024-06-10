<?php
session_start();
include('../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}
include('../includes/translate.php');
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Retrieve form data
  $loginType = $_POST['login_type'];
  $reportId = $_POST['reportid'];
  $updatedHeadlines = $_POST['updatedHeadlines'];
  $updatedData = $_POST['updatedData'];

  // Ensure there is data to process
  if ($loginType === 'edit_report2' && !empty($reportId) && !empty($updatedHeadlines) && !empty($updatedData)) {
      try {
          // Combine headlines and data into a single string
          $headlineString = implode("\n", $updatedHeadlines);
          $dataString = implode("\n", $updatedData);

          // Prepare insert statement
          $stmt = $dbh->prepare("INSERT INTO tblreport_step2 (request_id, headline, data) VALUES (:request_id, :headline, :data)");

          // Bind parameters and execute
          $stmt->bindParam(':request_id', $reportId, PDO::PARAM_INT);
          $stmt->bindParam(':headline', $headlineString, PDO::PARAM_STR);
          $stmt->bindParam(':data', $dataString, PDO::PARAM_STR);

          // Execute the statement
          $stmt->execute();

          // Check if the insert was successful
          if ($stmt->rowCount() > 0) {
              sleep(1);
              $msg = urlencode(translate("Reports have been successfully updated"));
              header("Location: ../views/user/audits.php?status=success&msg=" . $msg);
              exit();
          } else {
              $error = urlencode(translate("Failed to update settings"));
          }
      } catch (Exception $e) {
          $error = urlencode(translate("Failed to update settings: ") . $e->getMessage());
      }
  } else {
      $error = urlencode(translate("Invalid data provided"));
  }

  // Redirect back with error message
  header("Location: ../supperadmin/settings.php?status=error&msg=" . $error);
  exit();
} else {
  // Invalid request method
  $error = urlencode(translate("Invalid request method"));
  header("Location: ../supperadmin/settings.php?status=error&msg=" . $error);
  exit();
}



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reportid'])) {
  $reportId = $_POST['reportid'];

  // Collect updated headlines and data from the form
  $updatedHeadlines = $_POST['updatedHeadlines'];
  $updatedData = $_POST['updatedData'];

  // Convert arrays to newline-separated strings
  $headlinesStr = implode("\n", $updatedHeadlines);
  $dataStr = implode("\n", $updatedData);

  // Prepare the SQL statement to update the report
  $stmt = $dbh->prepare("UPDATE tblreport_step1 SET headline = :headline, data = :data WHERE request_id = :request_id");
  $stmt->bindParam(':request_id', $reportId, PDO::PARAM_INT);
  $stmt->bindParam(':headline', $headlinesStr, PDO::PARAM_STR);
  $stmt->bindParam(':data', $dataStr, PDO::PARAM_STR);

  // Execute the statement and check for success
  if ($stmt->execute()) {
    $_SESSION['message'] = "Report updated successfully!";
  } else {
    $_SESSION['error'] = "Error: Could not update report!";
  }

  // Redirect to the dashboard
  header('Location: dashboard.php');
  exit();
} else {
  $_SESSION['error'] = "Error: Invalid request!";
  header('Location: your_page.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['report_id'])) {
  $reportId = $_POST['report_id'];
  $requestName = $_POST['request_name'];
  $description = $_POST['description'];
  $files = isset($_FILES['files']) ? $_FILES['files'] : null;
  $oldFiles = isset($_POST['old_files']) ? explode(',', $_POST['old_files']) : [];

  // Process uploaded files
  $uploadedFiles = [];
  if ($files) {
    foreach ($files['name'] as $index => $name) {
      $tmpName = $files['tmp_name'][$index];
      $ext = pathinfo($name, PATHINFO_EXTENSION);
      $newName = uniqid() . '.' . $ext;
      $destination = '../../uploads/' . $newName;

      if (move_uploaded_file($tmpName, $destination)) {
        $uploadedFiles[] = $newName;
      }
    }
  }

  // Combine old and new files
  $allFiles = array_merge($oldFiles, $uploadedFiles);
  $allFilesStr = implode(',', $allFiles);

  // Update the report in the database
  $stmt = $dbh->prepare("UPDATE tblrequest SET request_name_2 = :request_name, description_2 = :description WHERE id = :report_id");
  $stmt->bindParam(':request_name', $requestName, PDO::PARAM_STR);
  $stmt->bindParam(':description', $description, PDO::PARAM_STR);
  $stmt->bindParam(':report_id', $reportId, PDO::PARAM_INT);

  // Delete removed files from database
  $stmtDelete = $dbh->prepare("DELETE FROM tblrequest_attachments WHERE request_id = :report_id AND filename NOT IN (:all_files)");
  $stmtDelete->bindParam(':report_id', $reportId, PDO::PARAM_INT);
  $stmtDelete->bindParam(':all_files', $allFilesStr, PDO::PARAM_STR);
  $stmtDelete->execute();

  // Insert new files into the database
  $stmtInsert = $dbh->prepare("INSERT INTO tblrequest_attachments (request_id, file_path) VALUES (:report_id, :filename)");
  foreach ($uploadedFiles as $file) {
    $stmtInsert->bindParam(':report_id', $reportId, PDO::PARAM_INT);
    $stmtInsert->bindParam(':filename', $file, PDO::PARAM_STR);
    $stmtInsert->execute();
  }

  if ($stmt->execute()) {
    header('Location: success_page.php');
  } else {
    echo "Error updating report";
  }
}
