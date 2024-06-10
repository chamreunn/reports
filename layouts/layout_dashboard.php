<?php
// session_start(); // Start the session at the beginning
include('../../config/dbconn.php');
// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

// Determine if the current user is a superadmin
$isSuperAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'supperadmin';

?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="../../assets/" data-template="horizontal-menu-template">

<head>
  <title><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></title>
  <?php include('../../includes/header.php'); ?>
</head>
<?php
include('../../includes/translate.php'); // Include translation function
include('../../includes/alert.php');
?>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
    <div class="layout-container">
      <!-- Navbar -->
      <?php
      if ($isSuperAdmin) {
        include('../../includes/nav_admin.php');
      } else {
        include('../../includes/navbar.php');
      }
      ?>
      <!-- / Navbar -->
      <!-- Layout container -->
      <div class="layout-page">
        <!-- Content wrapper -->
        <div class="content-wrapper">
          <?php
          if ($isSuperAdmin) {
            include('../../includes/sidebar_admin.php');
          } else {
            include('../../includes/sidebar.php');
          }
          ?>
          <!-- / Menu -->
          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
            <?php include('../../includes/loading-overlay.php'); ?>
            <?php echo isset($content) ? $content : ""; ?>
            <!-- /single card  -->
          </div>
          <!--/ Layout container -->
          <?php include('../../includes/footer.php'); ?>
        </div>
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>

      <!--/ Layout wrapper -->

      <!-- Core JS -->
      <?php include('../../includes/scripts.php'); ?>
    </div>
  </div>
</body>

</html>
