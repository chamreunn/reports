<?php

declare(strict_types=1);
session_start();

include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}
require '../../vendor/autoload.php';

// Initialize Google Authenticator
$secret = 'XVQ2UIGO75XRUKJO';
$link = \Sonata\GoogleAuthenticator\GoogleQrUrl::generate('Long', $secret, 'Leaves');
$g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();

$pageTitle = "ព័ត៌មានគណនី";
$sidebar = "all-users-security";
ob_start(); // Start output buffering
include('../../controllers/form_process.php');

// Retrieve user information from the database based on the provided user ID
$getid = isset($_GET['uid']) ? intval($_GET['uid']) : 0;

$sql = "SELECT u.*, r.RoleName, oh.*, d.*
        FROM tbluser u
        LEFT JOIN tblrole r ON u.RoleId = r.id
        LEFT JOIN tbloffices oh ON u.Office = oh.id
        LEFT JOIN tbldepartments d ON u.Department = d.id
        WHERE u.id = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $getid, PDO::PARAM_INT);
$query->execute();
$userData = $query->fetch(PDO::FETCH_ASSOC);

// Function to generate initials from user's name
function generateInitials(string $name): string
{
  $initials = '';
  $nameParts = explode(' ', $name);
  foreach ($nameParts as $part) {
    $initials .= strtoupper(substr($part, 0, 1));
  }
  return $initials;
}

// Check if user data exists
if ($userData) {
  // Your code to display user information goes here
  // Example: echo '<p>' . htmlspecialchars($userData['UserName'], ENT_QUOTES, 'UTF-8') . '</p>';
?>

  <div class="row">
    <div class="col-12">
      <div class="card mb-3">
        <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
          <div class="user-profile-header-banner">
            <!-- Clickable profile banner to change cover -->
            <label for="coverInput" class="profile-image">
              <?php
              $coverImageSrc = !empty($userData['Cover']) ? $userData['Cover'] : '../../assets/img/pages/profile-banner.png';
              ?>
              <img id="coverImage" src="<?php echo htmlentities($coverImageSrc); ?>" alt="Banner image" class="rounded-top" style="height: 310px ; width: 100%; object-fit: cover;">
            </label>
          </div>
        </div>
        <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
          <form id="formAuthentication" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="login_type" value="updatedimg">
            <div class="flex-shrink-0 mt-n5 mx-sm-0 mx-auto">
              <!-- Clickable profile picture to change profile image -->
              <label for="profileInput" class="profile-image">
                <?php if (!empty($userData['Profile'])) : ?>
                  <img src="<?php echo htmlentities($userData['Profile']); ?>" alt="user image" class="d-block h-auto ms-0 ms-sm-5 rounded border p-1 bg-light user-profile-img" height="150" width="150" style="object-fit: cover;">
                <?php else : ?>
                  <!-- Placeholder image or initials -->
                  <span class="avatar-initial rounded-circle bg-label-success">
                    <?php echo generateInitials($userData['FirstName'] . ' ' . $userData['LastName']); ?>
                  </span>
                <?php endif; ?>
              </label>
              <input type="file" name="updateimg" class="d-none" accept="image/*">
            </div>
          </form>
          <div class="flex-grow-1 mt-3 mt-sm-5">
            <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
              <div class="user-profile-info">
                <h4 class="mef2 fw-bolder">
                  <?php echo htmlentities($userData['Honorific'] . ' ' . $userData['FirstName'] . ' ' . $userData['LastName']); ?>
                </h4>
                <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                  <li class="list-inline-item fw-medium">
                    <i class="bx bx-pen"></i> <?php echo htmlentities($userData['RoleName']); ?>
                  </li>
                  <li class="list-inline-item fw-medium">
                    <i class="bx bx-map"></i> <?php echo htmlentities($userData['Address']); ?>
                  </li>
                  <li class="list-inline-item fw-medium">
                    <i class="bx bx-calendar-alt"></i> Joined
                    <?php echo date('F Y', strtotime($userData['CreationDate'])); ?>
                  </li>
                </ul>
              </div>
              <!-- Additional buttons or actions -->
              <a href="#" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#profileModal">
                <i class="bx bx-user-check me-1"></i> View Profile
              </a>
              <!-- Profile Modal -->
              <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="profileModalLabel">User Profile</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <!-- PHP code to fetch and display user's profile information -->
                      <?php
                      // You may need to adjust this query based on your database schema
                      $sql = "SELECT * FROM tbluser WHERE id = :userId";
                      $query = $dbh->prepare($sql);
                      $query->bindParam(':userId', $getid, PDO::PARAM_INT);
                      $query->execute();
                      $user = $query->fetch(PDO::FETCH_ASSOC);

                      // Display fetched profile information within a form for editing
                      if ($user) {
                      ?>
                        <form id="editProfileForm" method="POST" enctype="multipart/form-data">
                          <input type="hidden" name="login_type" value="updatedimg">
                          <input type="hidden" name="userId" value="<?php echo $getid; ?>">

                          <!-- Profile Picture -->
                          <div class="mb-3 text-center">
                            <!-- Clickable profile picture to change profile image -->
                            <label for="profileInput">
                              <?php if (!empty($user['Profile'])) : ?>
                                <img src="<?php echo htmlentities($user['Profile']); ?>" alt="user image" class="img-fluid rounded-4 profile-image" style="cursor: pointer; object-fit:contain; width: 320px; height: 320px">
                              <?php else : ?>
                                <!-- Placeholder image or initials -->
                                <span class="avatar-initial rounded-circle bg-label-success" style="cursor: pointer;"><?php echo generateInitials($user['FirstName'] . ' ' . $user['LastName']); ?></span>
                              <?php endif; ?>
                            </label>
                            <!-- File input to choose new profile picture -->
                            <input type="file" name="updateimg" id="profileInput" class="d-none" accept="image/*">
                          </div>

                          <!-- Other Information -->
                          <div class="mb-3">
                            <label for="userName" class="form-label">Username:</label>
                            <input type="text" class="form-control" id="userName" name="userName" value="<?php echo htmlentities($user['UserName']); ?>">
                          </div>
                          <div class="mb-3">
                            <label for="firstName" class="form-label">First Name:</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlentities($user['FirstName']); ?>">
                          </div>
                          <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name:</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlentities($user['LastName']); ?>">
                          </div>
                          <!-- Add more fields as needed -->

                          <!-- Modal Footer -->
                          <div class="modal-footer">
                            <!-- Close Button -->
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                          </div>
                        </form>
                      <?php
                      } else {
                        echo "User not found.";
                      }
                      ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <ul class="nav nav-pills flex-column flex-sm-row mb-3">
        <!-- Profile Tab (Always Visible) -->
        <li class="nav-item">
          <a class="nav-link <?php if ($sidebar == 'alluser') echo 'active'; ?>" href="all-users-detail.php?uid=<?php echo $getid; ?>">
            <i class='bx bx-user-circle me-2'></i>
            <span data-i18n="profile.title">Profile</span>
          </a>
        </li>
        <!-- Security Tab -->
        <li class="nav-item">
          <a class="nav-link <?php if ($sidebar == 'all-users-security') echo 'active'; ?>" href="all-users-security.php?uid=<?php echo $getid; ?>">
            <i class="bx bx-shield me-1"></i>
            <span data-i18n="security">Security</span>
          </a>
        </li>
        <!-- Permission Tab -->
        <li class="nav-item">
          <a class="nav-link <?php if ($sidebar == 'all-users-permission') echo 'active'; ?>" href="all-users-permission.php?uid=<?php echo $getid; ?>">
            <i class="bx bx-lock me-1"></i>
            <span data-i18n="permission">Permission</span>
          </a>
        </li>
      </ul>
    </div>
  </div>

  <div class="row">
    <!-- user-detail -->
    <div class="col-xl-4 col-lg-5 col-md-5">
      <!-- About User -->
      <div class="card mb-3">
        <div class="card-body">
          <small class="text-muted text-uppercase" data-i18n="About">About</small>
          <ul class="list-unstyled mb-4 mt-3">
            <li class="d-flex align-items-center mb-3">
              <i class="bx bx-user"></i>
              <span class="fw-medium mx-2" data-i18n="Full Name">Full Name:</span>
              <span><?php echo htmlentities($userData['Honorific'] . ' ' . $userData['FirstName'] . ' ' . $userData['LastName']); ?></span>
            </li>
            <li class="d-flex align-items-center mb-3"><i class="bx bx-check"></i><span class="fw-medium mx-2" data-i18n="Status">Status:</span> <span data-i18n="Active">Active</span></li>
            <li class="d-flex align-items-center mb-3"><i class="bx bx-star"></i><span class="fw-medium mx-2" data-i18n="Role">Role:</span>
              <span><?php echo htmlentities($userData['RoleName']); ?></span>
            </li>
            <li class="d-flex align-items-center mb-3"><i class="bx bx-flag"></i><span class="fw-medium mx-2" data-i18n="Address">Address:</span>
              <span><?php echo htmlentities($userData['Address']); ?></span>
            </li>
            <li class="d-flex align-items-center mb-3"><i class="bx bx-buildings"></i><span class="fw-medium mx-2" data-i18n="Department">Department:</span>
              <span><?php echo htmlentities($userData['DepartmentName']); ?></span>
            </li>
            <li class="d-flex align-items-center mb-3"><i class="bx bx-building"></i><span class="fw-medium mx-2" data-i18n="Office">Office:</span>
              <span><?php echo htmlentities($userData['OfficeName']); ?></span>
            </li>
          </ul>
          <small class="text-muted text-uppercase" data-i18n="Contacts">Contacts</small>
          <ul class="list-unstyled mb-4 mt-3">
            <li class="d-flex align-items-center mb-3"><i class="bx bx-phone"></i><span class="fw-medium mx-2" data-i18n="Contact">Contact:</span>
              <span><?php echo htmlentities($userData['Contact']); ?></span>
            </li>
            <li class="d-flex align-items-center mb-3"><i class="bx bx-envelope"></i>
              <span class="fw-medium mx-2" data-i18n="Email">Email:</span>
              <span><?php echo htmlentities($userData['Email']); ?></span>
            </li>
          </ul>
          <small class="text-muted text-uppercase" data-i18n="Teams">Teams</small>
          <ul class="list-unstyled mt-3 mb-0">
            <li class="d-flex align-items-center mb-3"><i class="bx bx-user-circle  me-2"></i>
              <div class="d-flex flex-wrap">
                <span class="fw-medium mx-2" data-i18n="Head Of Department">Head Of Department:</span>
                <span class="fw-medium me-2 mef2"><?php echo htmlentities($userData['HeadOfDepartment']); ?></span>
              </div>
            </li>
            <li class="d-flex align-items-center mb-3"><i class="bx bx-user-circle  me-2"></i>
              <div class="d-flex flex-wrap">
                <span class="fw-medium mx-2" data-i18n="Deputy Head Of Department">Deputy Head
                  Of
                  Department:
                </span>
                <span class="fw-medium me-2 mef2"><?php echo htmlentities($userData['DepHeadOfDepartment']); ?></span>
              </div>
            </li>
            <li class="d-flex align-items-center mb-3"><i class="bx bx-user-circle  me-2"></i>
              <div class="d-flex flex-wrap">
                <span class="fw-medium mx-2 " data-i18n="Head Of Office">Head Of Office:</span>
                <span class="fw-medium me-2 mef2"><?php echo htmlentities($userData['HeadOfOffice']); ?></span>
              </div>
            </li>
            <li class="d-flex align-items-center"><i class="bx bx-user-circle  me-2"></i>
              <div class="d-flex flex-wrap">
                <span class="fw-medium mx-2 " data-i18n="Deputy Head Of Office">Deputy Head Of
                  Office:</span>
                <span class="fw-medium me-2 mef2"><?php echo htmlentities($userData['DepHeadOffice']); ?></span>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <!--/ About User -->
    </div>
    <!-- end-user-detail -->
    <!-- user-security -->
    <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
      <!-- change-password -->
      <div class="card mb-4">
        <h5 class="card-header mef2" data-i18n="card_header">Change Password</h5>
        <div class="card-body">
          <form id="formValidationExamples" onsubmit="submitForm()" class="row g-3 fv-plugins-bootstrap5 fv-plugins-framework" method="POST">
            <input type="hidden" name="login_type" value="updatepass">
            <input type="hidden" name="updatepassid" value="<?php echo $getid ?>">
            <div class="alert alert-warning" role="alert">
              <h6 class="alert-heading mb-1" data-i18n="requirements_heading">Ensure that
                these requirements are met</h6>
              <span data-i18n="requirements">Minimum 8 characters long, uppercase &amp;
                symbol</span>
            </div>
            <div class="row">
              <div class="col-md-6 fv-plugins-icon-container">
                <div class="form-password-toggle">
                  <label class="form-label" for="formValidationPass" data-i18n="password_label">Password</label>
                  <div class="input-group input-group-merge has-validation">
                    <input class="form-control" type="password" id="formValidationPass" name="formValidationPass" placeholder="············" aria-describedby="multicol-password2">
                    <span class="input-group-text cursor-pointer" id="multicol-password2">
                      <i class="bx bx-hide"></i>
                    </span>
                  </div>
                  <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                  </div>
                </div>
              </div>
              <div class="col-md-6 fv-plugins-icon-container">
                <div class="form-password-toggle">
                  <label class="form-label" for="formValidationConfirmPass" data-i18n="onfirm_password_label">Confirm Password</label>
                  <div class="input-group input-group-merge has-validation">
                    <input class="form-control" type="password" id="formValidationConfirmPass" name="formValidationConfirmPass" placeholder="············" aria-describedby="multicol-confirm-password2">
                    <span class="input-group-text cursor-pointer" id="multicol-confirm-password2"><i class="bx bx-hide"></i></span>
                  </div>
                  <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                  </div>
                </div>
              </div>
              <div class="mt-3">
                <button class="btn btn-primary me-2" data-i18n="save_button">
                  Save Changed
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!--end change password -->
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="card-title mb-2">Two-steps verification</h5>
          <span class="card-subtitle">Keep your account secure with an additional authentication step.</span>
        </div>
        <div class="card-body">
          <?php if ($userData['authenticator_enabled'] == 1) { ?>
            <div class="row">
              <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <div class="d-flex gap-2 align-items-center">
                  <i class="bx bx-cog bx-md"></i>
                  <span class="custom-option-header">
                    <span class="h6 mb-2 fw-bolder">Authenticator Apps</span>
                  </span>
                </div>
                <div class="d-flex align-items-center">
                  <a href="javascript:;" class="btn btn-primary d-flex align-items-center me-3" data-bs-toggle="modal" data-bs-target="#disconnectModal">
                    <i class="bx bx-user-check me-1"></i>Connected
                  </a>
                  <!-- Disconnect Button -->
                  <button type="button" class="btn btn-label-danger d-flex align-items-center me-3" data-bs-toggle="modal" data-bs-target="#disconnectModal">
                    <i class="bx bx-user-x me-1"></i>Disconnect
                  </button>
                  <!-- Disconnect Modal -->
                  <div class="modal fade" id="disconnectModal" tabindex="-1" aria-labelledby="disconnectModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="disconnectModalLabel">
                            Disconnect Authenticator
                          </h5>
                        </div>
                        <form id="formValidationExamples" class="mb-3" onsubmit="submitForm()" method="post">
                          <input type="hidden" name="login_type" value="disconnect_twofa">
                          <input type="hidden" name="twofacodeid" value="<?php echo $getid; ?>">
                          <div class="modal-body">
                            Are you sure you want to disconnect the authenticator app?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <!-- Use JavaScript to submit the form -->
                            <button class="btn btn-danger">Disconnect</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>

                </div>
              </div>
              <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <div class="d-flex gap-2 align-items-center">
                  <i class="bx bx-message bx-md"></i>
                  <span class="d-flex flex-column custom-option-header">
                    <span class="h6 mb-2 fw-bolder">SMS</span>
                    <span class="mb-0">
                      We will send a code via SMS if you need to use your backup login method.
                    </span>
                  </span>
                </div>
                <a href="javascript:;" class="btn btn-label-primary d-flex align-items-center me-3" data-bs-target="#twoFactorAuthTwo" data-bs-toggle="modal">
                  <i class="bx bx-user-plus me-1"></i>Connect
                </a>
              </div>
            </div>
          <?php } else { ?>

            <div class="row">
              <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <div class="d-flex gap-2 align-items-center">
                  <i class="bx bx-cog bx-md"></i>
                  <span class="custom-option-header">
                    <span class="h6 mb-2 fw-bolder">Authenticator Apps</span>
                  </span>
                </div>
                <a href="javascript:;" class="btn btn-label-primary d-flex align-items-center me-3" data-bs-target="#twoFactorAuthOne" data-bs-toggle="modal">
                  <i class="bx bx-user-plus me-1"></i>Connect
                </a>
              </div>

              <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-3">
                <div class="d-flex gap-2 align-items-center">
                  <i class="bx bx-message bx-md"></i>
                  <span class="d-flex flex-column custom-option-header">
                    <span class="h6 mb-2 fw-bolder">SMS</span>
                    <span class="mb-0">
                      We will send a code via SMS if you need to use your backup login method.
                    </span>
                  </span>
                </div>
                <a href="javascript:;" class="btn btn-label-primary d-flex align-items-center me-3" data-bs-target="#twoFactorAuthTwo" data-bs-toggle="modal">
                  <i class="bx bx-user-plus me-1"></i>Connect
                </a>
              </div>
            </div>
          <?php } ?>
          <p class="mb-0">Two-factor authentication adds an additional layer of security to your account by
            requiring more than just a password to log in.
            <a href="javascript:void(0);" class="text-body">Learn more.</a>
          </p>
        </div>
      </div>
    </div>
    <!-- end-user-security -->
  </div>
  <!-- modal QR-->
  <div class="modal fade" id="twoFactorAuthOne" tabindex="-1" aria-labelledby="twoFactorAuthOneLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="formValidationExamples" onsubmit="submitForm()" class="row g-3" method="POST">
          <input type="hidden" name="login_type" value="twofacode">
          <input type="hidden" name="twofacodeid" value="<?php echo $getid; ?>">
          <input type="hidden" name="secret" value="<?php echo $secret; ?>">
          <div class="modal-body p-5">
            <div class="text-center mb-2">
              <h3 class="mb-0">Add Authenticator App</h3>
            </div>
            <h5 class="mb-2 pt-1 text-break">Authenticator Apps</h5>
            <p class="mb-4">Using an authenticator app like Google Authenticator, Microsoft Authenticator,
              Authy, or 1Password, scan the QR code. It will generate a 6-digit code for you to enter
              below.
            </p>
            <div id="qrScanner" class="text-center">
              <img src="<?= $link; ?>" class="bg-light p-2 rounded shadow-lg">
            </div>
            <div class="alert alert-warning d-flex flex-column align-items-center justify-content-center mt-3" role="alert">

              <p class="mb-0">PLEASE ENTER THE 6 DIGIT CODE FROM GOOGLE AUTHENTICATION</p>
            </div>
            <div class="col-md-12">
              <input type="text" id="modalEnableOTPPhone" name="modalEnableOTPPhone" required class="form-control phone-number-otp-mask" placeholder="បញ្ចូលលេខកូដពី Google Authenticator">
              <div class="invalid-feedback">
                សូមវាយបញ្ចូលលេខកូដ។
              </div>
            </div>
            <div class="mt-3 text-end">
              <button class="btn btn-primary me-2" type="submit">
                បញ្ជូន
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- end-modal-QR -->
  <!-- modal-SMS -->
  <div class="modal fade" id="twoFactorAuthTwo" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-simple">
      <div class="modal-content p-3 p-md-5">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <h5 class="mb-2 pt-1">Verify Your Mobile Number for SMS</h5>
          <p class="mb-4">Enter your mobile phone number with country code
          <div class="mb-4">
            <input type="text" class="form-control" id="twoFactorAuthInputSms" placeholder="747 875 3459">
          </div>
          <div class="col-12 text-end">
            <button type="button" class="btn btn-label-secondary me-sm-3 me-2 px-3 px-sm-4" data-bs-toggle="modal" data-bs-target="#twoFactorAuth"><i class="bx bx-left-arrow-alt bx-xs me-1 scaleX-n1-rtl"></i><span class="align-middle">Back</span></button>
            <button type="button" class="btn btn-primary px-3 px-sm-4" data-bs-dismiss="modal" aria-label="Close"><span class="align-middle">Continue</span><i class="bx bx-right-arrow-alt bx-xs ms-1 scaleX-n1-rtl"></i></button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Display selected profile image
    document.getElementById('profileInput').addEventListener('change', function() {
      const profileImage = this.previousElementSibling.querySelector('img');
      const file = this.files[0];
      const reader = new FileReader();
      reader.onload = function(e) {
        profileImage.src = e.target.result;
        updateProfileImage(file); // Call function to update profile image
      };
      reader.readAsDataURL(file);
    });
  </script>
<?php
} else {
  // User data not found
  echo "<div class='text-center'>User not found.</div>";
}
$content = ob_get_clean(); ?>
<!-- <script src="../../assets/js/form-validat
ion.js"></script> -->
<?php include('../../includes/layout.php'); ?>
