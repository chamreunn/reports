<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../config/dbconn.php'; // Adjust the path as necessary
include '../pages/admin/fuctions.php'; // Adjust the path as necessary

header('Content-Type: application/json');

$userId = $_SESSION['userid'] ?? null;

if ($userId) {
  try {
    // Get the unread and read notifications count for the user
    $countSql = "
      SELECT
        (SELECT COUNT(*)
         FROM notifications n
         JOIN tblrequest r ON n.request_id = r.id
         WHERE r.user_id = :user_id AND n.is_read = 0 AND r.status IN ('approved', 'rejected')) AS unread_count,
        (SELECT COUNT(*)
         FROM notifications n
         JOIN tblrequest r ON n.request_id = r.id
         WHERE r.user_id = :user_id AND n.is_read = 1 AND r.status IN ('approved', 'rejected')) AS read_count
    ";
    $countQuery = $dbh->prepare($countSql);
    $countQuery->bindParam(':user_id', $userId);
    $countQuery->execute();
    $countResult = $countQuery->fetch(PDO::FETCH_ASSOC);

    // Get the notifications details
    $detailsSql = "
      SELECT n.id, n.message, n.created_at, u.UserName AS approver_name, u.Honorific AS approver_honorific, u.FirstName AS approver_firstname, u.LastName AS approver_lastname, u.Profile AS approver_profile, r.request_name_1, r.report_link, n.is_read
      FROM notifications n
      JOIN tblrequest r ON n.request_id = r.id
      JOIN tbluser u ON r.approved_by = u.id
      WHERE r.user_id = :user_id AND r.status IN ('approved', 'rejected')
      ORDER BY n.created_at DESC
    ";
    $detailsQuery = $dbh->prepare($detailsSql);
    $detailsQuery->bindParam(':user_id', $userId);
    $detailsQuery->execute();
    $notifications = $detailsQuery->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
      'unread_count' => $countResult['unread_count'],
      'read_count' => $countResult['read_count'],
      'notifications' => $notifications
    ]);
  } catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
  }
} else {
  echo json_encode(['error' => 'User not authenticated']);
}
