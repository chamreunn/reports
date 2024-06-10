<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
include '../../config/dbconn.php';
include('translate.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../index.php');
  exit();
}

// Include the admin functions file
include 'fuctions.php';

$userId = $_SESSION['userid'];
$notifications = getNotifications($userId);

// Fetch user-specific data from the database
$sqlUser = "SELECT u.*, r.RoleName FROM tbluser u
            INNER JOIN tblrole r ON u.RoleId = r.id
            WHERE u.id = :userId";
$stmtUser = $dbh->prepare($sqlUser);
$stmtUser->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmtUser->execute();
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Check if user data includes 'languages' key
$userLanguage = isset($user['languages']) ? $user['languages'] : 'kh'; // Default to 'kh' if not set
$default_language = "kh";

// Define language options
$languages = array(
  'kh' => translate('ភាសាខ្មែរ'),
  'en' => translate('English')
);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['language']) && array_key_exists($_POST['language'], $languages)) {
    $selectedLanguage = $_POST['language'];

    // Update the user's language preference in the database
    try {
      $updateLanguageSql = "UPDATE tbluser SET languages = :language WHERE id = :userId";
      $stmtUpdateLanguage = $dbh->prepare($updateLanguageSql);
      $stmtUpdateLanguage->bindParam(':language', $selectedLanguage);
      $stmtUpdateLanguage->bindParam(':userId', $userId, PDO::PARAM_INT);
      $stmtUpdateLanguage->execute();

      // Update the session variable to reflect the updated language immediately
      $_SESSION['user_language'] = $selectedLanguage;

      // Set a success message
      sleep(1);
      $msg = urlencode(translate("Languages have been successfully updated"));
    } catch (PDOException $e) {
      die("Database error: " . $e->getMessage());
    }
  } else {
    // Set an error message
    sleep(1);
    $error = translate("Invalid language selected");
  }
}

// Fetch notification count for the current user
try {
  $sqlNotifications = "SELECT COUNT(*) AS notification_count
                       FROM tblrequest
                       WHERE user_id = :userId AND status = 'approved'";
  $stmtNotifications = $dbh->prepare($sqlNotifications);
  $stmtNotifications->bindParam(':userId', $userId, PDO::PARAM_INT);
  $stmtNotifications->execute();
  $notificationCount = $stmtNotifications->fetch(PDO::FETCH_ASSOC)['notification_count'];
} catch (PDOException $e) {
  die("Database error: " . $e->getMessage());
}
?>

<?php include('alert.php'); ?>
<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="container-xxl">
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4">
      <a href="" class="app-brand-link gap-2">
        <span class="app-brand-logo demo">
          <img src="../../assets/img/icons/brands/logo2.png" class="avatar avat" alt="">
        </span>
        <span class="app-brand-text demo menu-text fw-bold d-xl-block d-none d-sm-none text-uppercase" style="font-family:'khmer mef2','Sans Serif';font-size: 1.2rem"><?php echo translate('INTERNAL AUDIT UNIT'); ?></span>
      </a>
      <a href="" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
        <i class="bx bx-chevron-left bx-sm align-middle"></i>
      </a>
    </div>
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
      <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="bx bx-menu bx-sm"></i>
      </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
      <ul class="navbar-nav flex-row align-items-center ms-auto">
        <!-- Language Selector -->
        <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
          <form id="language-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="dropdown-toggle hide-arrow">
            <input type="hidden" name="language" id="selected-language" value="<?php echo isset($userLanguage) ? $userLanguage : $default_language; ?>">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <i class="bx bx-globe bx-sm"></i>
            </a>
            <!-- Default Language Option -->
            <ul class="dropdown-menu dropdown-menu-end">
              <?php foreach ($languages as $langCode => $langName) : ?>
                <li>
                  <button type="submit" name="language" value="<?php echo $langCode; ?>" class="dropdown-item language-option <?php echo (isset($userLanguage) && $userLanguage == $langCode) ? 'active' : ''; ?>">
                    <span class="align-middle"><?php echo $langName; ?></span>
                  </button>
                </li>
              <?php endforeach; ?>
            </ul>
          </form>

        </li>
        <!-- /Language Selector -->

        <!-- Style Switcher -->
        <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <i class="bx bx-sm"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
            <li>
              <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                <span class="align-middle"><i class="bx bx-sun me-2"></i><?php echo translate('Light'); ?></span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                <span class="align-middle"><i class="bx bx-moon me-2"></i><?php echo translate('Dark'); ?></span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                <span class="align-middle"><i class="bx bx-desktop me-2"></i><?php echo translate('System'); ?></span>
              </a>
            </li>
          </ul>
        </li>
        <!-- Notification Bell -->
        <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
          <a class="nav-link dropdown-toggle hide-arrow show" href="javascript:void(0);" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="true">
            <i class="bx bx-bell bx-sm"></i>
            <span id="notification-badge-wrapper">
              <span class="badge bg-danger rounded-pill badge-notifications" id="notification-count">0</span>
            </span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end py-0" data-bs-popper="static">
            <li class="dropdown-menu-header border-bottom">
              <div class="dropdown-header d-flex align-items-center py-3">
                <h5 class="text-body mb-0 me-auto">Notification</h5>
                <a href="javascript:void(0)" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Mark all as read" data-bs-original-title="Mark all as read"><i class="bx fs-4 bx-envelope-open"></i></a>
              </div>
            </li>
            <li class="dropdown-notifications-list scrollable-container ps">
              <ul class="list-group list-group-flush" id="notification-list">
                <li class="list-group-item list-group-item-action dropdown-notifications-item">
                  <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                      <!-- Notifications will be dynamically populated here by JavaScript -->
                    </div>
                  </div>
                </li>
              </ul>
            </li>
            <li class="dropdown-menu-footer border-top p-3">
              <button class="btn btn-primary text-uppercase w-100">View All Notifications</button>
            </li>
          </ul>
        </li>

        <!-- Add this part to your HTML to include the audio element -->
        <audio id="notification-sound" src="../../assets/notification/sound/notification.mp3" preload="auto"></audio>
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            // Check if the badge element is present
            var notificationBadge = document.getElementById('notification-count');
            if (notificationBadge) {
              // Fetch notification count from the server
              fetch('../ajax/notification_count.php')
                .then(response => response.json())
                .then(data => {
                  var count = data.notification_count;
                  // Update the badge with the notification count
                  notificationBadge.textContent = count;
                  // Show/hide badge based on the notification count
                  if (count > 0) {
                    notificationBadge.style.display = 'inline-block'; // Show the badge
                    playNotificationSound(); // Play the notification sound
                  } else {
                    notificationBadge.style.display = 'none'; // Hide the badge
                  }
                })
                .catch(error => {
                  console.error('Error fetching notification count:', error);
                });
            }
          });

          function playNotificationSound() {
            var sound = document.getElementById('notification-sound');
            sound.play();
          }
        </script>
        <!-- User Profile -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar avatar-online">
              <img src="../../assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle">
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="#">
                <div class="d-flex">
                  <div class="flex-shrink-0 me-3">
                    <div class="avatar avatar-online">
                      <img src="../../assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle">
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <span class="fw-semibold d-block"><?php echo $user['username']; ?></span>
                    <small class="text-muted"><?php echo $user['RoleName']; ?></small>
                  </div>
                </div>
              </a>
            </li>
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <li>
              <a class="dropdown-item" href="../Account/profile.php">
                <i class="bx bx-user me-2"></i>
                <span class="align-middle"><?php echo translate('My Profile'); ?></span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="#">
                <i class="bx bx-cog me-2"></i>
                <span class="align-middle"><?php echo translate('Settings'); ?></span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="#">
                <span class="d-flex align-items-center align-middle">
                  <i class="bx bx-credit-card me-2"></i>
                  <span class="flex-grow-1 align-middle"><?php echo translate('Billing'); ?></span>
                  <span class="badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                </span>
              </a>
            </li>
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <li>
              <a class="dropdown-item" href="../logout.php">
                <i class="bx bx-power-off me-2"></i>
                <span class="align-middle"><?php echo translate('Log Out'); ?></span>
              </a>
            </li>
          </ul>
        </li>
        <!--/ User Profile -->
      </ul>
    </div>
  </div>
</nav>
