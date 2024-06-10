<?php
session_start();
include('../config/dbconn.php');
include('../includes/translate.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
  // Retrieve form data
  $requestId = $_POST['request_id'];
  $headlines = [];
  $data = [];

  // Process each step of the form
  foreach ($_POST as $key => $value) {
    if (strpos($key, 'headline') !== false) {
      // Extract step index from key
      $stepIndex = substr($key, strlen('headline'));
      $headlines[] = $_POST['headline' . $stepIndex];
      $data[] = $_POST['formValidationTextarea' . $stepIndex];
    }
  }

  // Convert arrays to newline-separated strings
  $headlineString = implode("\n", $headlines);
  $dataString = implode("\n", $data);

  // Insert the combined headline and data into tblreport_step1
  try {
    $dbh->beginTransaction();
    $stmt = $dbh->prepare("INSERT INTO tblreport_step1 (request_id, headline, data) VALUES (:request_id, :headline, :data)");
    $stmt->bindParam(':request_id', $requestId, PDO::PARAM_INT);
    $stmt->bindParam(':headline', $headlineString, PDO::PARAM_STR);
    $stmt->bindParam(':data', $dataString, PDO::PARAM_STR);
    $stmt->execute();

    $dbh->commit();
    sleep(1);
    $msg = urlencode(translate("Reports have been successfully updated"));
    header("Location: ../views/user/audits.php?status=success&msg=" . $msg);
    exit();
  } catch (Exception $e) {
    $dbh->rollBack();
    $_SESSION['error'] = "Failed to create report: " . $e->getMessage();
    header("Location: your_page.php");
    exit();
  }
} else {
  $_SESSION['error'] = "Error: Invalid request!";
  header("Location: your_page.php");
  exit();
}
