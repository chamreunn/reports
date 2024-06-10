<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../config/dbconn.php'; // Adjust the path as necessary
// include '../pages/admin/fuctions.php'; // Adjust the path as necessary

header('Content-Type: application/json');

$adminId = $_SESSION['userid'] ?? null;

if ($adminId) {
  try {
    // Get the unread and read notifications count for the admin
    $countSql = "
      SELECT
        (SELECT COUNT(*)
         FROM notifications n
         JOIN tblrequest r ON n.request_id = r.id
         WHERE r.admin_id = :admin_id AND n.is_read = 0) AS unread_count,
        (SELECT COUNT(*)
         FROM notifications n
         JOIN tblrequest r ON n.request_id = r.id
         WHERE r.admin_id = :admin_id AND n.is_read = 1) AS read_count,
        (SELECT COUNT(*)
         FROM tblrequest
         WHERE admin_id = :admin_id AND status = 'pending') AS pending_count
    ";
    $countQuery = $dbh->prepare($countSql);
    $countQuery->bindParam(':admin_id', $adminId);
    $countQuery->execute();
    $countResult = $countQuery->fetch(PDO::FETCH_ASSOC);

    // Get the notifications details
    $detailsSql = "
      SELECT n.id, n.message, n.created_at, u.UserName, u.Honorific, u.FirstName, u.LastName, u.Profile, r.request_name_1, r.report_link, n.is_read
      FROM notifications n
      JOIN tblrequest r ON n.request_id = r.id
      JOIN tbluser u ON r.user_id = u.id
      WHERE r.admin_id = :admin_id
      ORDER BY n.created_at DESC
    ";
    $detailsQuery = $dbh->prepare($detailsSql);
    $detailsQuery->bindParam(':admin_id', $adminId);
    $detailsQuery->execute();
    $notifications = $detailsQuery->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
      'unread_count' => $countResult['unread_count'],
      'read_count' => $countResult['read_count'],
      'pending_count' => $countResult['pending_count'],
      'notifications' => $notifications
    ]);
  } catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
  }
} else {
  echo json_encode(['error' => 'Admin not authenticated']);
}
