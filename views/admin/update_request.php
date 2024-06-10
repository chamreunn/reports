<?php
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
    header('Location: ../../index.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['id'], $_POST['action'], $_POST['confirm']) && $_POST['confirm'] === 'yes') {
    $requestId = $_POST['id'];
    $action = $_POST['action'];

    $status = '';
    if ($action == 'approved') {
        $status = 'approved';
    } else if ($action == 'rejected') {
        $status = 'rejected';
    }

    if ($status) {
        // Check current status of the request
        $sql_check = "SELECT status FROM tblrequests WHERE id = :requestId";
        $stmt_check = $dbh->prepare($sql_check);
        $stmt_check->bindParam(':requestId', $requestId, PDO::PARAM_INT);
        $stmt_check->execute();
        $currentStatus = $stmt_check->fetchColumn();

        // If the current status is 'inprocess', set the new status to 'approved'
        if ($currentStatus === 'inprocess') {
            $status = 'approve';
        }

        // Update tblrequests table
        $sql_requests = "UPDATE tblrequests SET status = :status WHERE id = :requestId";
        $stmt_requests = $dbh->prepare($sql_requests);
        $stmt_requests->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt_requests->bindParam(':requestId', $requestId, PDO::PARAM_INT);

        if ($stmt_requests->execute()) {
            $msg = "Request updated successfully.";
        } else {
            $msg = "Failed to update the request.";
        }
    } else {
        $msg = "Invalid action.";
    }
  } else {
    sleep(1);
    $msg = "Invalid request.";
  }
}