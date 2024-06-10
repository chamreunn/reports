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
  <style>
    @font-face {
      font-family: 'Khmer MEF1';
      src: url('../../assets/vendor/fonts/KhmerMEF1.ttf') format('truetype');
    }

    @font-face {
      font-family: 'Khmer MEF2';
      src: url('../../assets/vendor/fonts/KhmerMEF2.ttf') format('truetype');
    }

    .ql-font-KhmerMEF1 {
      font-family: 'Khmer MEF1';
    }

    .ql-font-KhmerMEF2 {
      font-family: 'Khmer MEF2';
    }
  </style>
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
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          // Initialize Quill editor for all editors
          document.querySelectorAll('.quill-editor').forEach(function(editor, index) {
            var quill = new Quill(editor, {
              theme: 'snow',
              modules: {
                toolbar: [
                  [{
                    'font': ['KhmerMEF1', 'KhmerMEF2', 'sans-serif', 'serif', 'monospace']
                  }, {
                    'size': ['small', false, 'large', 'huge']
                  }],
                  ['bold', 'italic', 'underline', 'strike'],
                  [{
                    'color': []
                  }, {
                    'background': []
                  }],
                  [{
                    'script': 'sub'
                  }, {
                    'script': 'super'
                  }],
                  ['blockquote', 'code-block'],
                  [{
                    'list': 'ordered'
                  }, {
                    'list': 'bullet'
                  }],
                  [{
                    'indent': '-1'
                  }, {
                    'indent': '+1'
                  }, {
                    'align': []
                  }],
                  ['link', 'image', 'video'],
                  ['clean']
                ]
              }
            });

            // Set initial content for the editor
            var hiddenTextarea = document.getElementById('hiddenEditorContent-' + (editor.id.split('-')[1]));
            quill.root.innerHTML = hiddenTextarea.value;

            // Sync data between Quill editor and hidden textarea
            quill.on('text-change', function() {
              hiddenTextarea.value = quill.root.innerHTML;
            });
          });
        });
      </script>

    </div>
  </div>

</body>

</html>
