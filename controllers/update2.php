<?php
session_start();
include('../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $request_id = $_POST['request_id'];
  $regulator = $_POST['regulator'];
  $headlines = $_POST['headline'];
  $data = $_POST['data'];

  // Sanitize input
  $sanitizedHeadlines = array_map('htmlspecialchars', $headlines);
  $sanitizedData = array_map('htmlspecialchars', $data);

  // Convert arrays to newline-separated strings
  $headlineString = implode("\n", $sanitizedHeadlines);
  $dataString = implode("\n", $sanitizedData);

  // Update the tblreport_step2 table
  $stmt = $dbh->prepare("UPDATE tblreport_step2 SET headline = :headline, data = :data WHERE request_id = :id");
  $stmt->bindParam(':headline', $headlineString, PDO::PARAM_STR);
  $stmt->bindParam(':data', $dataString, PDO::PARAM_STR);
  $stmt->bindParam(':id', $request_id, PDO::PARAM_INT);

  if ($stmt->execute()) {
    // Redirect to the view page after successful update
    header("Location: ../views/user/audits.php?id=$request_id&regulator=$regulator");
    exit();
  } else {
    echo "Error updating record.";
  }
} else {
  echo "Invalid request.";
}
?>
