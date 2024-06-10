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
          var quillEditors = [];
          <?php foreach ($form_data as $index => $step) : ?>
            var quill<?= $index ?> = new Quill('#editor<?= $index ?>', {
              theme: 'bubble',
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
            quillEditors.push(quill<?= $index ?>);
          <?php endforeach; ?>

          document.getElementById('wizard-validation-form').onsubmit = function() {
            quillEditors.forEach(function(quill, index) {
              document.querySelector(`textarea[name="formValidationTextarea${index}"]`).value = quill.root.innerHTML;
            });
          };
        });

        function nextStep(currentIndex) {
          var currentStep = document.getElementById('stepContent' + currentIndex);
          var nextStep = document.getElementById('stepContent' + (currentIndex + 1));
          var currentHead = document.querySelector('.step.active');
          var nextHead = document.querySelector('.step[data-target="#stepContent' + (currentIndex + 1) + '"]');
          var prevHead = document.querySelector('.step[data-target="#stepContent' + currentIndex + '"]');

          if (nextStep && nextHead) {
            currentStep.classList.remove('active');
            nextStep.classList.add('active');
            currentHead.classList.remove('active');
            currentHead.classList.add('completed');
            nextHead.classList.add('active');
            prevHead.classList.add('crossed');
            enableDisableButtons(currentIndex + 1);

            // If next step is the summary step, display the summary
            if (currentIndex + 1 === <?= count($form_data) - 1; ?>) {
              displaySummary();
              // Change the button text and onclick event for the last step
              document.querySelector('.btn-next').innerText = 'Submit';
              document.querySelector('.btn-next').setAttribute('onclick', 'submitForm()');
            }
          }
        }

        function prevStep(currentIndex) {
          var currentStep = document.getElementById('stepContent' + currentIndex);
          var prevStep = document.getElementById('stepContent' + (currentIndex - 1));
          var currentHead = document.querySelector('.step.active');
          var prevHead = document.querySelector('.step[data-target="#stepContent' + (currentIndex - 1) + '"]');
          var nextHead = document.querySelector('.step[data-target="#stepContent' + currentIndex + '"]');

          if (prevStep && prevStep.classList) {
            currentStep.classList.remove('active');
            prevStep.classList.add('active');
            currentHead.classList.remove('active');
            currentHead.classList.remove('completed');
            prevHead.classList.add('active');
            nextHead.classList.remove('crossed');
            enableDisableButtons(currentIndex - 1);
          }
        }

        function enableDisableButtons(currentIndex) {
          var prevButton = document.querySelector('.btn-prev');
          var nextButton = document.querySelector('.btn-next');
          if (currentIndex === 0) {
            prevButton.setAttribute('disabled', 'disabled');
          } else {
            prevButton.removeAttribute('disabled');
          }
          if (currentIndex === <?= count($form_data) - 1; ?>) {
            nextButton.setAttribute('disabled', 'disabled');
          } else {
            nextButton.removeAttribute('disabled');
          }
        }
      </script>
    </div>
  </div>

</body>

</html>
