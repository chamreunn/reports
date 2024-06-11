<?php
session_start();
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}
require_once('../../controllers/form_process.php');

$pageTitle = "ព័ត៌មានគណនី";
$sidebar = "alluser";
ob_start(); // Start output buffering

// Retrieve user information from the database based on the provided user ID
$getid = $_GET['uid'];
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
function generateInitials($name)
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
                  <img src="<?php echo htmlentities($userData['Profile']); ?>" alt="user image" class="d-block h-auto ms-0 ms-sm-5 rounded border p-1 bg-light user-profile-img shadow-sm" height="150px" width="150px" style="object-fit: cover;">
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
              <!-- Button trigger modal -->
              <a href="#" class="btn btn-primary text-nowrap" data-bs-toggle="modal" data-bs-target="#profileModal">
                <i class="bx bx-user-check me-1"></i> Change Profile
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
                      // Adjust this query based on your database schema
                      $sql = "SELECT * FROM tbluser WHERE id = :userId";
                      $query = $dbh->prepare($sql);
                      $query->bindParam(':userId', $getid, PDO::PARAM_INT);
                      $query->execute();
                      $user = $query->fetch(PDO::FETCH_ASSOC);

                      // Display fetched profile information within a form for editing
                      if ($user) {
                      ?>
                        <form id="formAuthentication" method="POST" enctype="multipart/form-data">
                          <input type="hidden" name="login_type" value="updatedimg">
                          <input type="hidden" name="userId" value="<?php echo $getid; ?>">

                          <!-- Profile Picture -->
                          <div class="mb-3 text-center">
                            <label for="profileInput">
                              <?php if (!empty($user['Profile'])) : ?>
                                <img src="<?php echo htmlentities($user['Profile']); ?>" alt="user image" id="profileImgPreview" class="img-fluid rounded-4 profile-image" style="cursor: pointer; object-fit: contain; width: 320px; height: 320px">
                              <?php else : ?>
                                <span class="avatar-initial rounded-circle bg-label-success" style="cursor: pointer;" id="profileImgPreview"><?php echo generateInitials($user['FirstName'] . ' ' . $user['LastName']); ?></span>
                              <?php endif; ?>
                            </label>
                            <input type="file" name="fileimg" id="profileInput" class="d-none" accept="image/*" onchange="previewProfileImage(event)">
                          </div>

                          <!-- Other Information
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
                          </div> -->

                          <!-- Modal Footer -->
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                          </div>
                        </form>

                        <script>
                          function previewProfileImage(event) {
                            var reader = new FileReader();
                            reader.onload = function() {
                              var output = document.getElementById('profileImgPreview');
                              output.src = reader.result;
                            }
                            reader.readAsDataURL(event.target.files[0]);
                          }
                        </script>
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
        <!-- <li class="nav-item">
          <a class="nav-link <?php if ($sidebar == 'all-user-permission') echo 'active'; ?>" href="all-users-permission.php?uid=<?php echo $getid; ?>">
            <i class="bx bx-lock me-1"></i>
            <span data-i18n="permission">Permission</span>
          </a>
        </li> -->
      </ul>
    </div>
  </div>

  <div class="row">
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
            <!-- <li class="d-flex align-items-center mb-3"><i class="bx bx-building"></i><span class="fw-medium mx-2" data-i18n="Office">Office:</span>
              <span><?php echo htmlentities($userData['OfficeName']); ?></span>
            </li> -->
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
          <!-- <small class="text-muted text-uppercase" data-i18n="Teams">Teams</small>
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
          </ul> -->
        </div>
      </div>
      <!--/ About User -->
    </div>

    <div class="col-xl-8 col-lg-7 col-md-7">
      <!-- Activity Timeline -->
      <div class="card card-action mb-4">
        <div class="card-header align-items-center">
          <h5 class="card-action-title mb-0"><i class="bx bx-list-ul me-2"></i>Activity Timeline</h5>
          <div class="card-action-element">
            <div class="dropdown">
              <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-dots-vertical-rounded"></i></button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="javascript:void(0);">Share timeline</a></li>
                <li><a class="dropdown-item" href="javascript:void(0);">Suggest edits</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="javascript:void(0);">Report bug</a></li>
              </ul>
            </div>
          </div>
        </div>
        <?php
        // Query to fetch activity timeline data for the user
        $sqlActivity = "SELECT * FROM tblactivity WHERE UserId = :id ORDER BY ActivityDate DESC";
        $queryActivity = $dbh->prepare($sqlActivity);
        $queryActivity->bindParam(':id', $getid, PDO::PARAM_INT);
        $queryActivity->execute();
        $activityData = $queryActivity->fetchAll(PDO::FETCH_ASSOC);
        if ($activityData) : ?>
          <!-- Display activity timeline events -->
          <div class="card-body">
            <div class="timeline ms-2">
              <?php foreach ($activityData as $activity) : ?>
                <ul class="timeline ms-2">
                  <li class="timeline-item timeline-item-transparent">
                    <span class="timeline-point-wrapper"><span class="timeline-point timeline-point-warning"></span></span>
                    <div class="timeline-event">
                      <div class="timeline-header mb-1">
                        <h6 class="mb-0"><?php echo htmlentities($activity['ActivityName']); ?></h6>
                        <small class="text-muted"><?php echo date('M d, Y h:iA', strtotime($activity['ActivityDate'])); ?>
                        </small>
                      </div>
                      <p class="mb-2"><?php echo htmlentities($activity['ActivityDescription']); ?></p>
                    </div>
                  </li>
                </ul>
              <?php endforeach; ?>
            </div>
          </div>
        <?php else : ?>
          <div class="card-body">
            <div class="d-flex flex-column align-items-center justify-content-center">
              <img src="../../assets/img/illustrations/no-data.svg" alt="" class="p-3 mb-2" width="150" height="150" style="object-fit: cover;">
              <p class="fw-bolder">មិនទាន់មានសកម្មភាពនៅឡើយ។</p>
            </div>
          </div>
        <?php endif; ?>
      </div>
      <!--/ Activity Timeline -->
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
<?php include('../../includes/layout.php'); ?>
