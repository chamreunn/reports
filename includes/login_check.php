<?php
//session_start();
//error_reporting(0);
include('../../config/dbconn.php');

// Fetch user details from the tbluser table based on user ID
$userId = $_SESSION['userid'];
$query = "SELECT * FROM tbluser WHERE id = :userId";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':userId', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the user is a supperadmin in the admin table
$query = "SELECT * FROM admin WHERE id = :userId";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':userId', $userId);
$stmt->execute();
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the user is a supperadmin
if (!$user && !$admin) {
  // User not found in both tables, redirect to login page
  //header('Location: ../../index.php');
  //exit();
}
$isSupperAdmin = $admin && $admin['role'] === 'supperadmin';
// Debugging: Check if user and admin data are correctly retrieved
if ($user) {
  error_log("User Details: " . print_r($user, true));
} else {
  error_log("User not found in tbluser");
}

if ($admin) {
  error_log("Admin Details: " . print_r($admin, true));
} else {
  error_log("Admin not found in admin");
}
