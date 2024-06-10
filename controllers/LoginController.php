<?php
session_start();
require '../../vendor/autoload.php';
include('../../config/dbconn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $loginType = $_POST['login_type'];
  if ($loginType == 'login') {
    if (!empty($_POST["username"]) && !empty($_POST["password"])) {
      $username = $_POST["username"];
      $password = $_POST["password"];

      try {
        // Database connection should be initialized here, assumed $dbh is the PDO object
        // Check against tbluser table
        $query = "SELECT u.*, r.RoleName FROM tbluser u
                            INNER JOIN tblrole r ON u.RoleId = r.id
                            WHERE u.UserName = :username";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check against admin table
        $query = "SELECT * FROM admin WHERE UserName = :username";
        $stmt = $dbh->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        $account = $user ?: $admin;
        $accountType = $user ? 'user' : ($admin ? 'supperadmin' : null);

        if ($account) {
          if ($account['Status'] == 'locked') {
            sleep(1);
            header('Location: account-locked.php');
          } elseif (password_verify($password, $account['Password'])) {
            if (isset($account['authenticator_enabled']) && $account['authenticator_enabled'] == 1) {
              // Redirect to 2FA verification
              $_SESSION['temp_userid'] = $account['id'];
              $_SESSION['temp_username'] = $account['UserName'];
              $_SESSION['temp_role'] = $account['RoleName'];
              $_SESSION['temp_usertype'] = $accountType;
              $_SESSION['temp_secret'] = $account['TwoFASecret'];
              sleep(1);
              header('Location: 2fa.php');
              exit; // Make sure to exit after redirection
            } else {
              // Set session variables for user or admin
              $_SESSION['userid'] = $account['id'];
              $_SESSION['role'] = $account['RoleName'];
              $_SESSION['username'] = $account['Honorific'] . " " . $account['FirstName'] . " " . $account['LastName'];

              // Define role to dashboard mapping
              $roleToDashboard = [
                'ប្រធានអង្គភាព' => 'pages/admin/dashboard.php',
                'អនុប្រធានអង្គភាព' => 'pages/admin/dashboard.php',
                'ប្រធាននាយកដ្ឋាន' => 'pages/manager/dashboard.php',
                'អនុប្រធាននាយកដ្ឋាន' => 'pages/manager/dashboard.php',
                'ប្រធានការិយាល័យ' => 'pages/office_manager/dashboard.php',
                'អនុប្រធានការិយាល័យ' => 'pages/office_manager/dashboard.php',
                'supperadmin' => 'pages/supperadmin/dashboard.php'
              ];

              // Redirect to appropriate dashboard
              $role = $_SESSION['role'];
              if (isset($roleToDashboard[$role])) {
                header('Location: ' . $roleToDashboard[$role]);
              } else {
                header('Location: ../../pages/user/dashboard.php');
              }
            }
          } else {
            $error = 'Invalid username or password';
          }
        } else {
          $error = 'Invalid username or password';
        }
      } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
      }
    } else {
      sleep(1);
      $error = 'Please enter both username and password';
    }
  } else {
    $error = "Invalid login type.";
  }
}


