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
  <!-- <script src="https://cdn.ckeditor.com/ckeditor5/35.3.0/classic/ckeditor.js"></script> -->

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
        include('navbar.php');
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
            include('sidebar.php');
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
      <script>
        // Function to handle file input change for profile image
        function handleProfileImageChange(input) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
              $('#profileImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
          }
        }

        // Function to handle file input change for cover image
        function handleCoverImageChange(input) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
              $('#coverImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
          }
        }

        // Trigger the corresponding file input when the profile or cover image is clicked
        $(document).ready(function() {
          $('#profileImage').click(function() {
            $('#profileInput').click();
          });

          $('#coverImage').click(function() {
            $('#coverInput').click();
          });
        });
      </script>
      <script>
        // Assuming your form submission function is named `submitForm`
        function submitForm() {
          // Check if form fields are filled
          var form = document.getElementById('formAuthentication');
          var inputs = form.querySelectorAll(
            'input[type="text"], input[type="password"], input[type="email"], input[type="number"], textarea'
          );
          var isFormFilled = true;

          inputs.forEach(function(input) {
            if (input.value.trim() === '') {
              isFormFilled = false;
              return;
            }
          });

          // If form is filled, proceed with reload
          if (isFormFilled) {
            // Show the reload overlay
            $('#reload-overlay').css('display', 'block');

            // Reload the page after a short delay
            setTimeout(function() {
              // Reload the page
              window.location.reload();
              document.getElementById('formAuthentication').submit();
            }, 2000); // Change the timeout value according to your needs
          }
        }
      </script>
    </div>
  </div>

</body>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var textareas = document.querySelectorAll('.ckeditor');
    textareas.forEach(function(textarea) {
      ClassicEditor
        .create(textarea)
        .catch(error => {
          console.error(error);
        });
    });
  });
</script>
<script>
  // Function to remove success message parameters from URL without reloading the page
  function removeSuccessMessage() {
    var url = window.location.href;
    // Check if URL contains success message parameters
    if (url.includes("status=success") && url.includes("msg=")) {
      // Remove status and msg parameters from URL
      var newUrl = url.split("?")[0];
      window.history.replaceState({}, document.title, newUrl);
    }
  }

  // Call the function when the page loads
  window.onload = function() {
    removeSuccessMessage();
  };
</script>

</html>
