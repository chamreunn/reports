<?php
include('../../config/dbconn.php');

// Check if the form is submitted for password reset
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Validate new password and confirm password
  $newPassword = $_POST['newpassword'];
  $confirmPassword = $_POST['confirm-newpassword'];
  $resetToken = $_POST['token']; // Retrieve the token from the hidden input field

  if (!empty($newPassword) && !empty($confirmPassword) && $newPassword === $confirmPassword) {
    // Check if the reset token is provided
    if (!empty($resetToken)) {
      // Fetch user ID associated with the provided reset token
      $query = "SELECT user_id FROM password_reset_tokens WHERE token = :token";
      $stmt = $dbh->prepare($query);
      $stmt->bindParam(':token', $resetToken);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($row) {
        $userId = $row['user_id'];

        // Fetch user email using the user ID
        $emailQuery = "SELECT email FROM tbluser WHERE id = :id";
        $emailStmt = $dbh->prepare($emailQuery);
        $emailStmt->bindParam(':id', $userId);
        $emailStmt->execute();
        $emailRow = $emailStmt->fetch(PDO::FETCH_ASSOC);

        if ($emailRow) {
          $userEmail = $emailRow['email'];

          // Update user's password in the database
          $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
          $updateQuery = "UPDATE tbluser SET Password = :password WHERE id = :id";
          $updateStmt = $dbh->prepare($updateQuery);
          $updateStmt->bindParam(':password', $hashedPassword);
          $updateStmt->bindParam(':id', $userId);
          $updateStmt->execute();

          // Delete the reset token from the database
          $deleteQuery = "DELETE FROM password_reset_tokens WHERE token = :token";
          $deleteStmt = $dbh->prepare($deleteQuery);
          $deleteStmt->bindParam(':token', $resetToken);
          $deleteStmt->execute();

          $msg = "Your password has been successfully reset.";
        } else {
          $error = "User email not found.";
        }
      } else {
        $error = "Invalid or expired reset token.";
      }
    } else {
      $error = "Reset token is missing.";
    }
  } else {
    $error = "Passwords do not match.";
  }
} else if (isset($_GET['token'])) {
  // Fetch user email using the token for displaying in the form
  $resetToken = $_GET['token'];
  $query = "SELECT user_id FROM password_reset_tokens WHERE token = :token";
  $stmt = $dbh->prepare($query);
  $stmt->bindParam(':token', $resetToken);
  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($row) {
    $userId = $row['user_id'];

    // Fetch user email using the user ID
    $emailQuery = "SELECT email FROM tbluser WHERE id = :id";
    $emailStmt = $dbh->prepare($emailQuery);
    $emailStmt->bindParam(':id', $userId);
    $emailStmt->execute();
    $emailRow = $emailStmt->fetch(PDO::FETCH_ASSOC);

    if ($emailRow) {
      $userEmail = $emailRow['email'];
    } else {
      $error = "User email not found.";
    }
  } else {
    $error = "Invalid or expired reset token.";
    sleep(1);
    header('Location: index.php');
  }
}
include('../../config/dbconn.php');
try {
  // Retrieve existing data if available
  $sql = "SELECT * FROM tblsystemsettings";
  $result = $dbh->query($sql);

  if ($result->rowCount() > 0) {
    // Fetch data and pre-fill the form fields
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $system_name = $row["system_name"];

    // Assign the paths as they are stored in the database
    $icon_path = $row["icon_path"];
    $cover_path = $row["cover_path"];
  } else {
    // If no data available, set default values
    $system_name = "";
    $icon_path = "assets/img/avatars/no-image.jpg";
    $cover_path = "assets/img/pages/profile-banner.png";
  }
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="horizontal-menu-template">

<?php
$pageTitle = "ផ្លាស់ប្តូរពាក្យសម្ងាត់";
include('../../includes/header-login-page.php');
include('../../includes/alert.php');
?>

<body>
  <div class="authentication-wrapper authentication-cover">
    <div class="authentication-inner row m-0">

      <!-- /Left Text -->
      <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5">
        <div class="w-100 d-flex justify-content-center">
          <img src="../../assets/img/illustrations/boy-with-laptop-light.png" class="img-fluid" alt="Login image" width="600" data-app-dark-img="illustrations/boy-with-laptop-dark.png" data-app-light-img="illustrations/boy-with-laptop-light.png">

        </div>
      </div>
      <!-- /Left Text -->

      <!-- Reset Password -->
      <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-4 p-sm-5">
        <div class="w-px-400 mx-auto">
          <!-- Logo -->
          <div class="app-brand mb-5 d-flex align-items-center justify-content-center">
            <a href="index.php" class="app-brand-link gap-2">
              <span class="app-brand-log demo">
                <img src="<?php echo htmlspecialchars($icon_path); ?>" class="avatar avatar-xl" alt="">
              </span>
            </a>
          </div>
          <h4 class="mb-3 mx-0 fw-bold mef2" data-i18n="Reset Password">Reset Password </h4>
          <p class="mb-4">របស់ <span class="fw-medium text-primary"><?php echo $userEmail; ?></span></p>

          <form id="formAuthentication" class="mb-3 fv-plugins-bootstrap5 fv-plugins-framework" method="POST" novalidate="novalidate">
            <input type="hidden" name="token" value="<?php echo isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>">
            <div class="mb-3 form-password-toggle fv-plugins-icon-container">
              <label class="form-label" data-i18n="New Password" for="password">New Password</label>
              <div class="input-group input-group-merge has-validation">
                <input type="password" id="password" class="form-control" name="newpassword" placeholder="············" aria-describedby="password">
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
              </div>
              <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
              </div>
            </div>
            <div class="mb-3 form-password-toggle fv-plugins-icon-container">
              <label class="form-label" data-i18n="Confirm Password" for="confirm-password">Confirm
                Password</label>
              <div class="input-group input-group-merge has-validation">
                <input type="password" id="confirm-newpassword" class="form-control" name="confirm-newpassword" placeholder="············" aria-describedby="password">
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
              </div>
              <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
              </div>
            </div>
            <button class="btn btn-primary d-grid w-100 mb-3">
              Set new password
            </button>
            <div class="text-center">
              <a href="index.php">
                <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                Back to login
              </a>
            </div>
          </form>
        </div>
      </div>
      <!-- /Reset Password -->
    </div>
  </div>
</body>
<?php include('../../includes/scripts-login-page.php'); ?>

</html>
