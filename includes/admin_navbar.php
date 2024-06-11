<?php
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

$userLanguage = $user['languages']; // Get user's language preference
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
  } else {
    // Set an error message
    sleep(1);
    $error = translate("Invalid language selected");
  }
}

// Fetch notification count for the current user
$sqlNotifications = "SELECT COUNT(*) AS notification_count
                     FROM tblrequest
                     WHERE user_id = :userId AND status = 'approved'";
$stmtNotifications = $dbh->prepare($sqlNotifications);
$stmtNotifications->bindParam(':userId', $userId, PDO::PARAM_INT);
$stmtNotifications->execute();
$notificationCount = $stmtNotifications->fetch(PDO::FETCH_ASSOC)['notification_count'];
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
            <input type="hidden" name="language" id="selected-language" value="<?php echo $userLanguage; ?>">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <i class="bx bx-globe bx-sm"></i>
            </a>
            <!-- Default Language Option -->
            <ul class="dropdown-menu dropdown-menu-end">
              <?php foreach ($languages as $langCode => $langName) : ?>
                <li>
                  <button type="submit" name="language" value="<?php echo $langCode; ?>" class="dropdown-item language-option <?php echo ($userLanguage == $langCode) ? 'active' : ''; ?>">
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
            let userInteracted = false;
            const notificationSound = new Audio('../../assets/notification/sound/notification.mp3');
            let previousUnreadCount = 0;

            // Listen for user interaction
            function handleUserInteraction() {
              userInteracted = true;
              document.removeEventListener('click', handleUserInteraction);
              document.removeEventListener('keydown', handleUserInteraction);
            }

            document.addEventListener('click', handleUserInteraction);
            document.addEventListener('keydown', handleUserInteraction);

            // Request permission for browser notifications
            function requestNotificationPermission() {
              if (Notification.permission !== 'granted') {
                Notification.requestPermission().then(permission => {
                  if (permission === 'granted') {
                    console.log('Notification permission granted');
                  }
                });
              }
            }

            // Check if the browser supports notifications
            function checkNotificationSupport() {
              if (!('Notification' in window)) {
                console.log('This browser does not support desktop notification');
              } else {
                requestNotificationPermission();
              }
            }

            function fetchNotifications() {
              fetch('../../includes/fetch_notifications.php')
                .then(response => {
                  if (!response.ok) {
                    throw new Error('Network response was not ok ' + response.statusText);
                  }
                  return response.json();
                })
                .then(data => {
                  console.log('Fetched data:', data); // Log fetched data
                  if (userInteracted && data.unread_count > previousUnreadCount) {
                    notificationSound.play();
                    if (Notification.permission === 'granted') {
                      const notification = new Notification('New Notification', {
                        body: 'You have new notifications',
                        icon: '../../assets/notification/icon/notification-alert.svg'
                      });
                      notification.onclick = function() {
                        // Handle notification click event
                        window.focus(); // Focus the window when the notification is clicked
                      };
                    }
                  }
                  previousUnreadCount = data.unread_count; // Update the previous unread count
                  // Update the count badge
                  const notificationBadgeWrapper = document.getElementById('notification-badge-wrapper');
                  if (data.unread_count > 0) {
                    notificationBadgeWrapper.innerHTML = `
                        <span class="badge bg-danger rounded-pill badge-notifications" id="notification-count">${data.unread_count}</span>
                    `;
                  } else {
                    notificationBadgeWrapper.innerHTML = ''; // Remove the badge
                  }
                  // Update the notification list as well
                  updateNotificationList(data.notifications);
                })
                .catch(error => console.error('Error fetching notifications:', error));
            }

            function updateNotificationList(notifications) {
              const notificationList = document.getElementById('notification-list');
              notificationList.innerHTML = '';
              console.log('Updating notification list...'); // Log when updating the list

              if (notifications.length === 0) {
                const noNotifications = document.createElement('div');
                noNotifications.className = 'text-center my-5';
                noNotifications.innerHTML = `
                <i class='bx bx-bell-off' style='font-size: 50px; color: #ccc;'></i>
                <p class='mt-3 mb-0 text-muted'>No Notifications</p>
            `;
                notificationList.appendChild(noNotifications);
              } else {
                notifications.forEach(notification => {
                  console.log('Notification:', notification); // Log each notification
                  const listItem = document.createElement('li');
                  listItem.className = `list-group-item list-group-item-action dropdown-notifications-item ${notification.is_read == 1 ? 'mark-as-read' : ''}`;
                  listItem.innerHTML = `
                <a href="view_notification.php?id=${notification.id}" class="text-decoration-none d-flex justify-content-between text-reset">
                    <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <img src="${notification.Profile}" alt="" class="w-px-40 rounded-circle" style="object-fit: cover">
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="mb-0 text-primary"><strong>${notification.Honorific} ${notification.FirstName} ${notification.LastName}</strong> បានដាក់សំណើបង្កើត ${notification.request_name_1}</p>
                            <small class="text-muted">${notification.created_at}</small>
                        </div>
                        <div class="flex-shrink-0 dropdown-notifications-actions">
                            <a href="javascript:void(0)" class="dropdown-notifications-read">
                                <span class="badge badge-dot ${notification.is_read == 1 ? 'bg-secondary' : 'bg-primary'}"></span>
                            </a>
                            <a href="javascript:void(0)" class="dropdown-notifications-archive">
                                <span class="bx bx-x"></span>
                            </a>
                        </div>
                    </div>
                </a>
                `;
                  notificationList.appendChild(listItem);
                });
              }
              console.log('Notification list updated.'); // Log after updating the list
            }

            // Check for notification support and request permission
            checkNotificationSupport();

            // Fetch notifications initially and then every 30 seconds
            fetchNotifications();
            setInterval(fetchNotifications, 30000); // Set interval to 30 seconds
          });
        </script>
        <!-- User Profile Dropdown -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="d-flex">
              <div class="flex-shrink-0">
                <div class="avatar avatar-online">
                  <img src="<?php echo (!empty($user['Profile'])) ? htmlentities($user['Profile']) : '../../assets/img/avatars/no-image.jpg'; ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                </div>
              </div>
              <div class="flex-grow-1 mx-2 d-none d-md-block">
                <span class="fw-medium d-block"><?php echo htmlentities($user['Honorific']) . " " . htmlentities($user['FirstName']) . " " . htmlentities($user['LastName']); ?></span>
                <small class="text-muted"><?php echo htmlentities($user['RoleName']); ?></small>
              </div>
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="pages-account-settings-account.html">
                <div class="d-flex">
                  <div class="flex-shrink-0 me-3">
                    <div class="avatar avatar-online">
                      <img src="<?php echo (!empty($user['Profile'])) ? htmlentities($user['Profile']) : '../../assets/img/avatars/no-image.jpg'; ?>" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <span class="fw-medium d-block"><?php echo htmlentities($user['Honorific']) . " " . htmlentities($user['FirstName']) . " " . htmlentities($user['LastName']); ?></span>
                    <small class="text-muted"><?php echo htmlentities($user['RoleName']); ?></small>
                  </div>
                </div>
              </a>
            </li>
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <li>
              <a class="dropdown-item" href="all-users-detail.php?uid=<?php echo $_SESSION['userid']; ?>">
                <i class="bx bx-user me-2"></i>
                <span class="align-middle"><?php echo translate('My Account'); ?></span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="pages-account-settings-account.php">
                <i class="bx bx-cog me-2"></i>
                <span class="align-middle"><?php echo translate('Settings'); ?></span>
              </a>
            </li>
            <li>
              <div class="dropdown-divider"></div>
            </li>
            <li>
              <a class="dropdown-item" href="../../includes/logout.php">
                <i class="bx bx-power-off me-2"></i>
                <span class="align-middle"><?php echo translate('Logout'); ?></span>
              </a>
            </li>
          </ul>
        </li>
        <!-- /User Profile Dropdown -->
      </ul>
    </div>
  </div>
</nav>
