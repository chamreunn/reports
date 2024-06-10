<?php
session_start();
// Include database connection
include('../../config/dbconn.php');
include('../../includes/translate.php');
// Redirect to the index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

// Set page-specific variables
$pageTitle = "ទំព័រដើម";
$sidebar = "dashboard";
$userId = $_SESSION['userid']; // Assuming the user ID is stored in the session
$_SESSION['prevPageTitle'] = $pageTitle;
ob_start();

// Fetch user activities from the database
$query = "SELECT * FROM tblactivity WHERE UserId = :userId ORDER BY ActivityDate DESC LIMIT 5";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':userId', $userId);
$stmt->execute();
$userActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="col-12 d-flex align-items-center justify-content-between mb-3">
  <h3 class="mb-0">wellcome<span class="mef2 text-primary mx-2 me-0 mb-0"><?php echo  $_SESSION['username'] ?></span></h3>
  <div class="dropdown">
    <button class="btn btn-primary"><i class="bx bx-calendar me-2"></i><?php echo date('D-m-Y h:i A') ?></button>
  </div>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 g-4">
  <!-- Activity Card -->
  <div class="col">
    <div class="card h-100">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center mb-3">
        <h5 class="card-title mef2 mb-0"><?php echo translate('Recent Activity') ?></h5>
        <button class="btn btn-sm btn-outline-primary mb-0">View More <i class="bx bx-chevron-right"></i></button>
      </div>
      <div class="card-body">
        <div class="list-group">
          <?php if (!empty($userActivities)) : ?>
            <?php foreach ($userActivities as $activity) : ?>
              <?php
              // Determine the color based on activity type
              $colorClass = 'text-primary'; // Default color is blue
              $activityType = $activity['ActivityType']; // Default activity type

              if ($activity['ActivityType'] === 'warning') {
                $colorClass = 'text-warning'; // Yellow color for warning activities
                $activityType = 'Warning';
              } elseif ($activity['ActivityType'] === 'danger') {
                $colorClass = 'text-danger'; // Red color for danger activities
                $activityType = 'Danger';
              }
              ?>
              <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <span class="badge bg-label-primary <?php echo $colorClass; ?>"><?php echo htmlspecialchars($activityType); ?></span>
                  <?php echo htmlspecialchars($activity['ActivityDescription']); ?>
                </div>
                <small class="text-muted"><?php echo htmlspecialchars($activity['ActivityDate']); ?></small>
              </div>
            <?php endforeach; ?>
          <?php else : ?>
            <div class="alert alert-warning">No recent activities found.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- progressbar  -->
  <?php
  // Define the default request ID if not provided in the URL
  $requestId = isset($_GET['id']) ? $_GET['id'] : null;

  $query = "SELECT * FROM tblrequest WHERE user_id = :userid ORDER BY created_at DESC LIMIT 5";
  $stmt = $dbh->prepare($query); // Prepare the statement
  $stmt->bindParam(':userid', $userId); // Bind the user ID parameter
  $stmt->execute(); // Execute the query

  $latestRequests = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows

  function calculateTaskProgress($status, $requests, $requestId)
  {
    $completedTasks = 0;

    foreach ($requests as $request) {
      if ($request['id'] == $requestId) {
        $step = $request['step'];
        $requestStatus = $request['status'];

        // Calculate progress based on step and status
        if ($step == 1 && $requestStatus == 'pending') {
          // Progress is waiting for approval
        } elseif ($step == 1 && $requestStatus == 'approved') {
          $completedTasks = 35; // Step 1 approved, progress is making reports (35% complete)
        } elseif ($step == 1 && $requestStatus == 'completed') {
          $completedTasks = 35; // Step 1 completed, progress is complete (35% complete)
        } elseif ($step == 2 && $requestStatus == 'pending') {
          // Progress is waiting for approval
          $completedTasks = 50; // Assuming 50% progress for pending at step 2
        } elseif ($step == 2 && $requestStatus == 'approved') {
          $completedTasks = 75; // Step 2 approved, progress is making reports (75% complete)
        } elseif ($step == 2 && $requestStatus == 'completed') {
          $completedTasks = 75; // Step 2 completed, progress is complete (75% complete)
        } elseif ($step == 3 && $requestStatus == 'pending') {
          // Progress is waiting for approval
          $completedTasks = 85; // Assuming 85% progress for pending at step 3
        } elseif ($step == 3 && $requestStatus == 'approved') {
          $completedTasks = 100; // Step 3 approved, progress is making reports (100% complete)
        } elseif ($step == 3 && $requestStatus == 'completed') {
          $completedTasks = 100; // Step 3 completed, progress is complete (100% complete)
        } elseif ($requestStatus == 'rejected') {
          // Assuming 100% progress for rejected status
        }
      }
    }

    return $completedTasks;
  }
  ?>
  <div class="col">
    <div class="card h-100">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Task Progress Tracker</h5>
      </div>
      <div class="card-body">
        <?php if (empty($latestRequests)) : ?>
          <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
            <div class="text-center">
              <img src="../../assets/img/illustrations/empty-box.png" alt="No Requests Found" style="max-width: 15%; height: auto;" />
              <h5 class="text-muted mt-3">No recent requests found.</h5>
            </div>
          </div>
        <?php else : ?>
          <!-- Request Title -->
          <?php if ($requestId !== null) : ?>
            <?php $requestTitle = ''; ?>
            <?php foreach ($latestRequests as $request) : ?>
              <?php if ($request['id'] == $requestId) $requestTitle = htmlspecialchars($request['request_name_1']); ?>
            <?php endforeach; ?>
            <h6 class="mb-1 mt-3">Request Title: <?php echo $requestTitle; ?></h6>
          <?php endif; ?>
          <!-- Progress Bar -->
          <div class="progress mt-3 mb-3">
            <?php if ($requestId !== null) : ?>
              <?php $progress = calculateTaskProgress('completed', $latestRequests, $requestId); ?>
              <div class="progress-bar <?php echo $progress == 100 ? 'bg-primary' : ($progress > 50 ? 'bg-success' : ($progress > 0 ? 'bg-warning' : 'bg-danger')); ?>" role="progressbar" style="width: <?php echo $progress; ?>%;" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100">
                <?php echo $progress; ?>% Complete
              </div>
            <?php else : ?>
              <div class="progress-bar bg-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0% Complete</div>
            <?php endif; ?>
          </div>
          <!-- Latest Requests Section -->
          <div class="mt-4">
            <h5 class="card-title mb-3">Latest Requests</h5>
            <ul class="list-group">
              <?php foreach ($latestRequests as $request) : ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <?php echo htmlspecialchars($request['request_name_1']); ?>
                  <span class="badge <?php echo $request['status'] == 'completed' ? 'bg-label-primary' : ($request['status'] == 'approved' ? 'bg-success' : ($request['status'] == 'rejected' ? 'bg-danger' : 'bg-warning')); ?>"><?php echo htmlspecialchars($request['status']); ?></span>
                  <!-- Link to view progress for each request ID -->
                  <a href="?id=<?php echo $request['id']; ?>">View Progress</a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>


  <!-- Current Report Card -->
  <?php
  // Available date options for the dropdown filter
  $dateOptions = array(
    'last_week' => 'Last 7 Days',
    'last_month' => 'Last 30 Days',
    'last_year' => 'Last 365 Days',
  );

  // Check if the date filter option is selected
  if (isset($_GET['dateFilter']) && array_key_exists($_GET['dateFilter'], $dateOptions)) {
    $selectedDateOption = $_GET['dateFilter'];
    switch ($selectedDateOption) {
      case 'last_week':
        $startDate = date('Y-m-d', strtotime('-7 days'));
        break;
      case 'last_month':
        $startDate = date('Y-m-d', strtotime('-30 days'));
        break;
      case 'last_year':
        $startDate = date('Y-m-d', strtotime('-365 days'));
        break;
      default:
        // Default to last 7 days
        $startDate = date('Y-m-d', strtotime('-7 days'));
        break;
    }
    $endDate = date('Y-m-d');
  } else {
    // Default to last 7 days if no option is selected
    $startDate = date('Y-m-d', strtotime('-7 days'));
    $endDate = date('Y-m-d');
  }

  $query = "SELECT * FROM tblrequest WHERE user_id = :userid AND DATE(created_at) BETWEEN :startDate AND :endDate ORDER BY created_at DESC LIMIT 5";
  $stmt = $dbh->prepare($query); // Prepare the statement
  $stmt->bindParam(':userid', $userId); // Bind the user ID parameter
  $stmt->bindParam(':startDate', $startDate); // Bind the start date parameter
  $stmt->bindParam(':endDate', $endDate); // Bind the end date parameter
  $stmt->execute(); // Execute the query

  $latestRequests = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all rows
  ?>

  <div class="col">
    <div class="card h-100">
      <div class="card-header border-bottom d-flex justify-content-between align-items-center">
        <div>
          <h5 class="card-title mb-0">Current Report</h5>
        </div>
        <!-- Date Filter Dropdown -->
        <form id="dateFilterForm" method="GET" class="d-flex align-items-center">
          <div class="dropdown">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dateFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <?php echo isset($selectedDateOption) ? $dateOptions[$selectedDateOption] : 'Select Date Range'; ?>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dateFilterDropdown">
              <?php foreach ($dateOptions as $value => $label) : ?>
                <li><a class="dropdown-item" href="?dateFilter=<?php echo $value; ?>"><?php echo $label; ?></a></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </form>
      </div>

      <div class="card-body p-0">
        <?php if (!empty($latestRequests)) : ?>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Requested</th>
                  <th>Details</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($latestRequests as $request) : ?>
                  <tr>
                    <td><?php echo htmlspecialchars($request['id']); ?></td>
                    <td><?php echo htmlspecialchars($request['request_name_1']); ?></td>
                    <td><?php echo htmlspecialchars($request['status']); ?></td>
                    <td><?php echo htmlspecialchars($request['created_at']); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else : ?>
          <p class="m-3">No recent requests found.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php
  // Assume you have a database connection established

  // Get the selected month from the request or set a default value
  $selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

  // Handle pagination for previous and next months
  if (isset($_GET['action'])) {
    $currentMonth = new DateTime($selectedMonth);
    if ($_GET['action'] === 'prev') {
      $currentMonth->modify('-1 month');
    } elseif ($_GET['action'] === 'next') {
      $currentMonth->modify('+1 month');
    }
    $selectedMonth = $currentMonth->format('Y-m');
  }

  // Fetch data from tblrequest for the selected month and the logged-in user
  $query = "SELECT status FROM tblrequest WHERE DATE_FORMAT(created_at, '%Y-%m') = :selectedMonth AND user_id = :user_id";
  $stmt = $dbh->prepare($query);
  $stmt->bindParam(':selectedMonth', $selectedMonth);
  $stmt->bindParam(':user_id', $_SESSION['userid']); // Filter by user_id from session
  $stmt->execute();
  $requestData = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Process the data to count statuses
  $statusCounts = [
    'pending' => 0,
    'approved' => 0,
    'rejected' => 0,
    'completed' => 0
  ];

  foreach ($requestData as $request) {
    switch ($request['status']) {
      case 'pending':
      case 'approved':
      case 'rejected':
      case 'completed':
        $statusCounts[$request['status']]++;
        break;
      default:
        // Handle other status values if needed
        break;
    }
  }

  $monthText = date('F', strtotime($selectedMonth));
  $monthIcon = 'bx bx-calendar';
  if (array_sum($statusCounts) === 0) {
    // No data available, show a dot indicating no data
    $monthIcon = 'bx bx-x-circle text-danger'; // Change the icon as per your preference
    $monthText = 'No Data Available';
  }
  ?>
  <!-- Summary Card -->
  <div class="col">
    <div class="card h-100">
      <div class="card-header border-bottom mb-3">
        <!-- Month Selector Dropdown and Title -->
        <form id="monthFilterForm" method="GET" class="d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Summary</h5>
          <div class="btn-group btn-group-sm" role="group" aria-label="Month Navigation">
            <a href="?month=<?= $selectedMonth ?>&action=prev" class="btn btn-outline-secondary"><i class="bx bx-chevron-left"></i></a>
            <span class="btn btn-outline-primary">
              <i class="<?= $monthIcon ?>"></i>
              <?= $monthText ?>
            </span>
            <a href="?month=<?= $selectedMonth ?>&action=next" class="btn btn-outline-secondary"><i class="bx bx-chevron-right"></i></a>
          </div>
        </form>
      </div>
      <div class="card-body">
        <!-- Set a fixed height for the canvas -->
        <canvas id="summaryChart" style="height: 250px;"></canvas>
      </div>
      <div class="card-footer">
        <?php if (array_sum($statusCounts) === 0) : ?>
          <div class="text-center">
            No data available for <?= $monthText ?>
          </div>
        <?php else : ?>
          <div class="row">
            <div class="col">
              <div class="d-flex align-items-center mb-2">
                <span class="badge badge-dot bg-warning"></span>
                <div class="ms-2">Pending</div>
                <div class="ms-auto"><?= $statusCounts['pending'] ?></div>
              </div>
              <div class="d-flex align-items-center mb-2">
                <span class="badge badge-dot bg-success"></span>
                <div class="ms-2">Approved</div>
                <div class="ms-auto"><?= $statusCounts['approved'] ?></div>
              </div>
              <div class="d-flex align-items-center mb-2">
                <span class="badge badge-dot bg-danger"></span>
                <div class="ms-2">Rejected</div>
                <div class="ms-auto"><?= $statusCounts['rejected'] ?></div>
              </div>
              <div class="d-flex align-items-center mb-0">
                <span class="badge badge-dot bg-primary"></span>
                <div class="ms-2">Completed</div>
                <div class="ms-auto"><?= $statusCounts['completed'] ?></div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

</div>

<?php $content = ob_get_clean(); ?>
<?php include('../../layouts/user_layout.php'); ?>
