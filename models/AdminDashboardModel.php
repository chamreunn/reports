<?php

$pageTitle = "Admin Dashboard";
$sidebar = "home";
ob_start(); // Start output buffering

// Fetch counts for the summary section
$countStmt = $dbh->prepare("SELECT
  (SELECT COUNT(*) FROM tblrequest WHERE status = 'pending') AS pending_count,
  (SELECT COUNT(*) FROM tblrequest WHERE status = 'approved') AS approved_count,
  (SELECT COUNT(*) FROM tblrequest WHERE status = 'rejected') AS rejected_count,
  (SELECT COUNT(*) FROM tblrequest WHERE status = 'completed') AS completed_count,
  (SELECT COUNT(*) FROM tblrequest) AS total_count
");
$countStmt->execute();
$counts = $countStmt->fetch(PDO::FETCH_ASSOC);

// Fetch recent pending requests
$recentPendingStmt = $dbh->prepare("SELECT r.id AS request_id, u.Honorific, u.FirstName, u.LastName, u.Email, u.Profile, r.status, r.request_name_1, r.admin_comment
                                    FROM tblrequest r
                                    INNER JOIN tbluser u ON r.user_id = u.id
                                    WHERE r.status = 'pending'
                                    ORDER BY r.id DESC
                                    LIMIT 5");
$recentPendingStmt->execute();
$pendingRequests = $recentPendingStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent approved requests
$recentApprovedStmt = $dbh->prepare("SELECT r.id AS request_id, u.Honorific, u.FirstName, u.LastName, u.Email, u.Profile, r.status, r.request_name_1, r.admin_comment
                                     FROM tblrequest r
                                     INNER JOIN tbluser u ON r.user_id = u.id
                                     WHERE r.status = 'approved'
                                     ORDER BY r.id DESC
                                     LIMIT 5");
$recentApprovedStmt->execute();
$approvedRequests = $recentApprovedStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent rejected requests
$recentRejectedStmt = $dbh->prepare("SELECT r.id AS request_id, u.Honorific, u.FirstName, u.LastName, u.Email, u.Profile, r.status, r.request_name_1, r.admin_comment
                                     FROM tblrequest r
                                     INNER JOIN tbluser u ON r.user_id = u.id
                                     WHERE r.status = 'rejected'
                                     ORDER BY r.id DESC
                                     LIMIT 5");
$recentRejectedStmt->execute();
$rejectedRequests = $recentRejectedStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent completed requests
$recentCompletedStmt = $dbh->prepare("SELECT r.id AS request_id, u.Honorific, u.FirstName, u.LastName, u.Email, u.Profile, r.status, r.request_name_1, r.admin_comment
                                      FROM tblrequest r
                                      INNER JOIN tbluser u ON r.user_id = u.id
                                      WHERE r.status = 'completed'
                                      ORDER BY r.id DESC
                                      LIMIT 5");
$recentCompletedStmt->execute();
$completedRequests = $recentCompletedStmt->fetchAll(PDO::FETCH_ASSOC);
