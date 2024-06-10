<?php
session_start(); // Start session at the beginning of the script
ob_start(); // Start output buffering to prevent premature output

$pageTitle = "ចូលប្រើប្រាស់ប្រព័ន្ធ";
include('../../includes/header-login-page.php');
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
    $icon_path = "../../assets/img/avatars/no-image.jpg";
    $cover_path = "../../assets/img/pages/profile-banner.png";
  }
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}
ob_end_flush(); // Flush the buffer and send output
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="horizontal-menu-template">
<head>
  <!-- Add your header content here (meta tags, title, etc.) -->
  <?php include('../../includes/header-login-page.php'); ?>
</head>
<body>
  <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme position-fixed w-100 shadow-sm z-index-0 px-3 px-md-5" id="layout-navbar">
    <div class="container">
      <div class="navbar-brand app-brand demo d-xl-flex py-0 me-4">
        <a href="index.html" class="app-brand-link gap-2">
          <span class="app-brand-logo demo">
            <img src="<?php echo htmlspecialchars($icon_path); ?>" class="avatar avat" alt="">
          </span>
          <span class="app-brand-text demo menu-text fw-bold mef2 d-xl-block d-none d-sm-none" style="font-size: 1.2rem"><?php echo htmlspecialchars($system_name); ?></span>
        </a>
      </div>
    </div>
    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <li class="nav-item dropdown-style-switcher dropdown me-4 me-xl-0">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <i class="bx bx-sm"></i>
        </a>
        <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
              <span class="align-middle"><i class="bx bx-sun me-2"></i>Light</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
              <span class="align-middle"><i class="bx bx-moon me-2"></i>Dark</span>
            </a>
          </li>
          <li>
            <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
              <span class="align-middle"><i class="bx bx-desktop me-2"></i>System</span>
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
  <!-- Content -->
  <div class="authentication-wrapper authentication-cover content ">
    <div class="authentication-inner row m-0">
      <!-- Left Text -->
      <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5">
        <div class="w-100 d-flex justify-content-center mt-5">
          <div>
            <img src="<?php echo htmlspecialchars($cover_path); ?>" style="width: 100%;height: 70vh; object-fit: cover;" alt="">
          </div>
        </div>
      </div>
      <!-- /Left Text -->
      <!-- Login -->
      <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4 shadow-none">
        <div class="w-px-400 mx-auto">
          <!-- Logo -->
          <div class="app-brand mb-5 d-flex align-items-center justify-content-center">
            <a href="index.php" class="app-brand-link gap-2">
              <span class="app-brand-log demo">
                <img src="<?php echo htmlspecialchars($icon_path); ?>" class="avatar avatar-xl" alt="">
              </span>
            </a>
          </div>
          <form id="formAuthentication" class="mb-3" method="POST" action="../../controllers/AuthController.php">
            <?php if (isset($_SESSION['error'])) : ?>
              <div class="alert alert-danger alert-dismissible" role="alert">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php unset($_SESSION['error']); ?> <!-- Clear the error message from session -->
            <?php endif; ?>
            <input type="hidden" name="login_type" value="login">
            <div class="mb-3">
              <label for="email" class="form-label" data-i18n="Username">ឈ្មោះមន្ត្រី </label>
              <span class="text-danger fw-bold">*</span>
              <input type="text" class="form-control" id="email" name="username" placeholder="សូមបញ្ចូលឈ្មោះមន្ត្រី" autofocus required />
            </div>
            <div class="mb-3 form-password-toggle">
              <div class="d-flex">
                <label class="form-label" for="password" data-i18n="Password">ពាក្យសម្ងាត់ </label>
                <span class="text-danger fw-bold mx-1">*</span>
              </div>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
              </div>
            </div>
            <button class="btn btn-primary d-grid w-100 mt-4" data-i18n="Login">ចូលប្រើប្រាស់ប្រព័ន្ធ</button>
          </form>
          <div class="divider my-4">
            <div class="divider-text" data-i18n="OR">ឬ</div>
          </div>

          <div class="d-flex justify-content-center mb-3">
            <a href="forgot-password.php" data-i18n="Forgot Password" class="btn btn-label-secondary w-100">Forgot Password</a>
          </div>

          <div class="d-flex justify-content-center">
            <a href="" class="btn btn-label-primary w-100" data-i18n="Back">ត្រឡប់ទៅកាន់ប្រព័ន្ធឌីជីថល</a>
          </div>
        </div>
      </div>
      <!-- /Login -->
    </div>
  </div>
  <!-- Include your scripts here -->
  <?php include('../../includes/scripts-login-page.php'); ?>
  <script>
    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }
  </script>
</body>
</html>
