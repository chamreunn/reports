<?php
session_start();
require 'vendor/autoload.php';
include('config/dbconn.php');

if (!isset($_SESSION['temp_userid'])) {
  header('Location: index.php');
  exit();
}

$error = '';
$max_attempts = 5;
$remaining_attempts = $max_attempts;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (!isset($_SESSION['2fa_attempts'])) {
    $_SESSION['2fa_attempts'] = 0;
  }

  $g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();

  // Combine all OTP inputs into a single code
  $userCode = implode('', [
    $_POST['digit1'],
    $_POST['digit2'],
    $_POST['digit3'],
    $_POST['digit4'],
    $_POST['digit5'],
    $_POST['digit6']
  ]);

  $secret = $_SESSION['temp_secret'];

  if ($g->checkCode($secret, $userCode)) {
    // Authenticator code is correct, complete the login
    $_SESSION['userid'] = $_SESSION['temp_userid'];
    $_SESSION['username'] = $_SESSION['temp_username'];
    $_SESSION['role'] = $_SESSION['temp_role'];

    // Clear the temporary session variables
    // Unset session variables
    unset($_SESSION['temp_userid'], $_SESSION['temp_username'], $_SESSION['temp_role'], $_SESSION['temp_usertype'], $_SESSION['temp_secret'], $_SESSION['2fa_attempts']);

    // Check if $_SESSION['role'] and $_SESSION['temp_usertype'] are set before accessing them
    if (isset($_SESSION['role'])) {
      if ($_SESSION['role'] == 'ប្រធានអង្គភាព') {
        header('Location: pages/admin/dashboard.php');
        exit(); // Terminate script execution after redirection
      }
    }

    if (isset($_SESSION['temp_usertype'])) {
      if ($_SESSION['temp_usertype'] == 'supperadmin') {
        header('Location: pages/supperadmin/dashboard.php');
        exit(); // Terminate script execution after redirection
      }
    }

    // If none of the conditions above are met, redirect to the user dashboard
    header('Location: pages/user/dashboard.php');
    exit(); // Terminate script execution after redirection
  } else {
    $_SESSION['2fa_attempts']++;
    $remaining_attempts = $max_attempts - $_SESSION['2fa_attempts'];

    if ($remaining_attempts > 0) {
      $error = "លេខកូដមិនត្រឹមត្រូវ សូមព្យាយាមម្តងទៀត! You have $remaining_attempts attempts remaining.";
    } else {
      // Lock the user's account
      $userId = $_SESSION['temp_userid'];
      $updateQuery = "UPDATE tbluser SET Status = 'locked' WHERE id = :userid";
      $stmt = $dbh->prepare($updateQuery);
      $stmt->bindParam(':userid', $userId);
      $stmt->execute();

      // Clear the temporary session variables
      unset($_SESSION['temp_userid'], $_SESSION['temp_username'], $_SESSION['temp_role'], $_SESSION['temp_usertype'], $_SESSION['temp_secret'], $_SESSION['2fa_attempts']);

      header('Location: account-locked.php');
      exit();
    }
  }
}

try {
  // Retrieve existing data if available
  $sql = "SELECT * FROM tblsystemsettings";
  $result = $dbh->query($sql);

  if ($result->rowCount() > 0) {
    // Fetch data and pre-fill the form fields
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $system_name = $row["system_name"];
    // Assuming icon and cover paths are stored in the database with ../../
    $icon_path_relative = $row["icon_path"];
    $cover_path_relative = $row["cover_path"];

    // Remove ../../ from the paths
    $icon_path = str_replace('../../', '', $icon_path_relative);
    $cover_path = str_replace('../../', '', $cover_path_relative);
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
<html lang="en" class="light-style layout-wide customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="assets/" data-template="horizontal-menu-template">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Default Title'; ?></title>

    <meta name="description"
        content="Most Powerful &amp; Comprehensive Bootstrap 5 HTML Admin Dashboard Template built for developers!" />
    <meta name="keywords" content="dashboard, bootstrap 5 dashboard, bootstrap 5 design, bootstrap 5" />
    <!-- Canonical SEO -->
    <link rel="canonical" href="../search-horizontal.json" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- ? PROD Only: Google Tag Manager (Default ThemeSelection: GTM-5DDHKGP, PixInvent: GTM-5J3LMKC) -->
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="assets/vendor/fonts/flag-icons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css" />
    <!-- Page CSS -->
    <link rel="stylesheet" href="assets/vendor/libs/bs-stepper/bs-stepper.css">
    <link rel="stylesheet" href="assets/vendor/libs/@form-validation/form-validation.css">
    <link rel="stylesheet" href="assets/vendor/libs/select2/select2.css">
    <link rel="stylesheet" href="assets/vendor/libs/bootstrap-select/bootstrap-select.css">
    <link rel="stylesheet" href="assets/vendor/libs/tagify/tagify.css">
    <link rel="stylesheet" href="assets/vendor/libs/toastr/toastr.css">
    <link rel="stylesheet" href="assets/vendor/libs/flatpickr/flatpickr.css">
    <link rel="stylesheet" href="assets/vendor/libs/spinkit/spinkit.css">
    <!-- Helpers -->
    <link rel="stylesheet" href="assets/vendor/libs/@form-validation/form-validation.css" />
    <link rel="stylesheet" href="assets/vendor/libs/animate-css/animate.css">
    <link rel="stylesheet" href="assets/vendor/libs/dropzone/dropzone.css">
    <!-- Page CSS -->
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <!-- Page -->
    <link rel="stylesheet" href="assets/vendor/css/pages/page-auth.css" />
    <!-- full edit textarea css  -->
    <link rel="stylesheet" href="assets/vendor/libs/quill/editor.css">
    <link rel="stylesheet" href="assets/vendor/libs/quill/katex.css">
    <link rel="stylesheet" href="assets/vendor/libs/quill/typography.css">
    <!-- end full edit textarea css  -->
    <link rel="stylesheet" href="assets/vendor/libs/datatables-bs/datatables.bootstrap5.css">
    <link rel="stylesheet" href="assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css">
    <!-- Helpers -->
    <script src="assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="assets/js/config.js"></script>
    <script>
    // Example starter JavaScript for disabling form submissions if there are invalid fields
    (() => {
        'use strict'

        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        const forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }

                form.classList.add('was-validated')
            }, false)
        })
    })()
    </script>
</head>

<body>
    <div class="authentication-wrapper authentication-cover">
        <div class="authentication-inner row m-0">
            <div class="d-none d-lg-flex col-lg-7 col-xl-8 align-items-center p-5">
                <div class="w-100 d-flex justify-content-center">
                    <img src="../../assets/img/illustrations/girl-verify-password-light.png" class="img-fluid"
                        alt="Login image" width="600" data-app-dark-img="illustrations/girl-verify-password-dark.png"
                        data-app-light-img="illustrations/girl-verify-password-light.png">
                </div>
            </div>
            <!-- Two Steps Verification -->
            <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-4 p-sm-5">
                <div class="w-px-400 mx-auto">
                    <!-- Logo -->
                    <div class="app-brand mb-5 d-flex align-items-center justify-content-center">
                        <a href="2fa.php" class="app-brand-link gap-2">
                            <span class="app-brand-log demo">
                                <img src="<?php echo htmlspecialchars($icon_path); ?>" class="avatar avatar-xl" alt="">
                            </span>
                        </a>
                    </div>
                    <!-- /Logo -->

                    <h4 class="mb-3">Two Step Verification <i class="bx bx-qr-scan"></i></h4>
                    <p class="text-start mb-4">
                        សូមប្រើប្រាស់ Google Authenticator ដើម្បីយកលេខកូដ៦ខ្ទង់មកបំពេញក្នុងការចូលទៅកាន់ប្រព័ន្ធ
                    </p>
                    <p class="mb-0 fw-medium">វាយបញ្ចូលលេខកូដ ៦ខ្ទង់</p>
                    <form id="twoStepsForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                        novalidate="novalidate" method="POST">
                        <div class="mb-3">
                            <div
                                class="auth-input-wrapper d-flex align-items-center justify-content-sm-between numeral-mask-wrapper">
                                <input type="tel"
                                    class="form-control auth-input border-2 h-px-50 text-center numeral-mask mx-1 my-2"
                                    maxlength="1" name="digit1" autofocus required>
                                <input type="tel"
                                    class="form-control auth-input border-2 h-px-50 text-center numeral-mask mx-1 my-2"
                                    maxlength="1" name="digit2" required>
                                <input type="tel"
                                    class="form-control auth-input border-2 h-px-50 text-center numeral-mask mx-1 my-2"
                                    maxlength="1" name="digit3" required>
                                <input type="tel"
                                    class="form-control auth-input border-2 h-px-50 text-center numeral-mask mx-1 my-2"
                                    maxlength="1" name="digit4" required>
                                <input type="tel"
                                    class="form-control auth-input border-2 h-px-50 text-center numeral-mask mx-1 my-2"
                                    maxlength="1" name="digit5" required>
                                <input type="tel"
                                    class="form-control auth-input border-2 h-px-50 text-center numeral-mask mx-1 my-2"
                                    maxlength="1" name="digit6" required>
                            </div>
                            <?php if ($error) : ?>
                            <div class="text-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                        </div>
                        <button type="submit" class="btn btn-primary d-grid w-100 mb-3">
                            ចូលទៅកាន់ប្រព័ន្ធ
                        </button>
                        <div class="text-center">មិនមានកូដ?
                            <a href="index.php">ត្រឡប់ទៅកាន់ Login</a>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Two Steps Verification -->
        </div>
    </div>
    <!-- Bootstrap JS -->
    <script src="assets/vendor/libs/jquery/jquery.js"></script>
    <script src="assets/vendor/libs/popper/popper.js"></script>
    <script src="assets/vendor/js/bootstrap.js"></script>
    <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="assets/vendor/libs/hammer/hammer.js"></script>
    <script src="assets/vendor/libs/i18n/i18n.js"></script>
    <script src="assets/vendor/libs/typeahead-js/typeahead.js"></script>
    <script src="assets/vendor/js/menu.js"></script>
    <!-- endbuild -->
    <!-- Vendors JS -->
    <script src="assets/vendor/libs/@form-validation/popular.js"></script>
    <script src="assets/vendor/libs/@form-validation/bootstrap5.js"></script>
    <script src="assets/vendor/libs/@form-validation/auto-focus.js"></script>
    <script src="assets/vendor/libs/block-ui/block-ui.js"></script>
    <!-- Main JS -->
    <script src="assets/js/main.js"></script>
    <!-- Page JS -->
    <script src="assets/js/pages-auth.js"></script>
    <script src="assets/js/ui-toasts.js"></script>
    <script src="assets/vendor/libs/toastr/toastr.js"></script>
    <!-- Automatically move focus to the next input field after filling one -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.numeral-mask');
        const form = document.getElementById('twoStepsForm');
        inputs.forEach((input, index) => {
            input.addEventListener('input', function() {
                if (input.value.length === input.maxLength) {
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                }
                // If all inputs are filled, submit the form
                if ([...inputs].every(input => input.value.length === input.maxLength)) {
                    form.submit();
                }
            });
        });
    });
    </script>
</body>
<script>
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>

</html>