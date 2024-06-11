<?php
$pageTitle = "View More";
$sidebar = "home";
ob_start(); // Start output buffering

$action = isset($_GET['action']) ? $_GET['action'] : 'pending';

// Define the page title and query based on the action
switch ($action) {
  case 'approved':
    $pageTitle = "Approved Requests";
    $query = "SELECT r.id AS request_id, u.Honorific, u.FirstName, u.LastName, u.Email, u.Profile, r.status, r.request_name_1, r.admin_comment
                  FROM tblrequest r
                  INNER JOIN tbluser u ON r.user_id = u.id
                  WHERE r.status = 'approved'
                  ORDER BY r.id DESC";
    break;
  case 'rejected':
    $pageTitle = "Rejected Requests";
    $query = "SELECT r.id AS request_id, u.Honorific, u.FirstName, u.LastName, u.Email, u.Profile, r.status, r.request_name_1, r.admin_comment
                  FROM tblrequest r
                  INNER JOIN tbluser u ON r.user_id = u.id
                  WHERE r.status = 'rejected'
                  ORDER BY r.id DESC";
    break;
  case 'completed':
    $pageTitle = "Completed Requests";
    $query = "SELECT r.id AS request_id, u.Honorific, u.FirstName, u.LastName, u.Email, u.Profile, r.status, r.request_name_1, r.admin_comment
                  FROM tblrequest r
                  INNER JOIN tbluser u ON r.user_id = u.id
                  WHERE r.status = 'completed'
                  ORDER BY r.id DESC";
    break;
  default:
    $pageTitle = "Pending Requests";
    $query = "SELECT r.id AS request_id, u.Honorific, u.FirstName, u.LastName, u.Email, u.Profile, r.status, r.request_name_1
                  FROM tblrequest r
                  INNER JOIN tbluser u ON r.user_id = u.id
                  WHERE r.status = 'pending'
                  ORDER BY r.id DESC";
    break;
}

$stmt = $dbh->prepare($query);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
