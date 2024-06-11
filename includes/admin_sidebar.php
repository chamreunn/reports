<?php
include('../../config/dbconn.php');

// Include translation function
include('../../includes/translate.php');

// Fetch user permissions from tbluser
$userId = $_SESSION['userid']; // Assuming you have the user's ID stored in the session
$query = "SELECT PermissionId FROM tbluser WHERE id = :userId";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':userId', $userId);
$stmt->execute();
$userPermissions = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Prepare a comma-separated string of permission IDs for the SQL query
$permissionIds = implode(',', $userPermissions);

// Query to fetch sidebar menu details based on user permissions
$query = "SELECT p.PermissionName, p.NavigationUrl, p.IconClass, p.EngName
          FROM tblpermission p
          WHERE p.id IN ($permissionIds)";

// Execute the query and fetch sidebar menu details
$stmt = $dbh->query($query);
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php
$currentUrl = basename($_SERVER['PHP_SELF']); // Get the current page filename

$sidebarRelationships = array(
  'dashboard' => array('dashboard.php', 'index.php'), // Add all pages related to the dashboard sidebar
  'audits' => array('audits.php', 'create_report_page.php'), // Corrected 'create_report_page.php' instead of 'create_reports_page.php'
  // Add more relationships as needed
);

// Determine the active page based on the sidebar variable
$activePage = '';

if (isset($sidebar) && array_key_exists($sidebar, $sidebarRelationships)) {
  foreach ($sidebarRelationships[$sidebar] as $relatedPage) {
    if ($currentUrl === $relatedPage) {
      $activePage = $relatedPage;
      break;
    }
  }
}

// If no related pages match the current URL, use the current URL itself
if (empty($activePage)) {
  $activePage = $currentUrl;
}
?>
<aside id="layout-menu" class="layout-menu-horizontal menu menu-horizontal container-fluid flex-grow-0 bg-menu-theme" data-bg-class="bg-menu-theme" style="touch-action: none; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
  <div class="container-xxl d-flex h-100">
    <ul class="menu-inner">
      <!-- Add the dashboard menu item directly -->
      <li class="menu-item <?php echo ($activePage === 'dashboard.php') ? 'active' : ''; ?>">
        <a href="dashboard.php" class="menu-link">
          <i class="menu-icon tf-icons bx bxs-dashboard"></i>
          <div data-i18n="Dashboard"><?php echo translate('Audit Reports'); ?></div>
        </a>
      </li>
    </ul>
  </div>
</aside>
