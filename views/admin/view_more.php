<?php
session_start();
include('../../config/dbconn.php');
require_once '../../includes/translate.php';

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

$pageTitle = "View More";
$sidebar = "view_more.php";
ob_start(); // Start output buffering

$action = isset($_GET['action']) ? $_GET['action'] : 'pending';

// Fetch filters from GET parameters
$current_year = date("Y");
$last_year = $current_year - 1;

// Fetch all distinct years from your request table
$yearsQuery = "SELECT DISTINCT YEAR(created_at) as request_year FROM tblrequest ORDER BY request_year DESC";
$yearsStmt = $dbh->prepare($yearsQuery);
$yearsStmt->execute();
$available_years = $yearsStmt->fetchAll(PDO::FETCH_COLUMN);

// If there are no records in the database, set default available years as the last 10 years including the current year
if (empty($available_years)) {
  $available_years = range($current_year - 10, $current_year);
}

$year = isset($_GET['year']) ? $_GET['year'] : $current_year;

// Build the base query
$query = "SELECT r.id AS request_id, u.Honorific, u.FirstName, u.LastName, u.Email, u.Profile, r.status, r.request_name_1, r.admin_comment, r.created_at
          FROM tblrequest r
          INNER JOIN tbluser u ON r.user_id = u.id
          WHERE YEAR(r.created_at) = :year
          ORDER BY r.id DESC";

$stmt = $dbh->prepare($query);
$stmt->bindParam(':year', $year);
$stmt->execute();
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group requests by status
$groupedRequests = [
  'pending' => [],
  'approved' => [],
  'rejected' => [],
  'completed' => []
];

foreach ($requests as $request) {
  $groupedRequests[$request['status']][] = $request;
}
?>
<!-- Smart Breadcrumb -->
<nav aria-label="breadcrumb mb-2">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="dashboard.php" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= translate('Go back to Dashboard') ?>">
        <span class="d-flex align-items-center mb-0">
          <i class='bx bxs-chevron-left-square mb-0'></i>
          <?= translate('Dashboard') ?>
        </span>
      </a>
    </li>
    <?php if ($action !== 'pending') : ?>
      <li class="breadcrumb-item text-uppercase text-primary active" aria-current="page"><?= ucfirst($action) ?></li>
    <?php endif; ?>
  </ol>
</nav>

<div class="row mb-3">
  <div class="col-12">
    <div class="card mb-3">
      <!-- Nav tabs -->
      <ul class="nav nav-tabs nav-fill" id="tabs-tab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link <?= $action == 'pending' ? 'active' : '' ?>" id="tabs-pending-tab" data-bs-toggle="tab" data-bs-target="#tabs-pending" type="button" role="tab" aria-controls="tabs-pending" aria-selected="true">
            <i class='bx bx-time'></i> Pending <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-danger ms-1"><?= count($groupedRequests['pending']) ?></span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link <?= $action == 'approved' ? 'active' : '' ?>" id="tabs-approved-tab" data-bs-toggle="tab" data-bs-target="#tabs-approved" type="button" role="tab" aria-controls="tabs-approved" aria-selected="false">
            <i class='bx bx-check-circle'></i> Approved <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-danger ms-1"><?= count($groupedRequests['approved']) ?></span>
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link <?= $action == 'rejected' ? 'active' : '' ?>" id="tabs-rejected-tab" data-bs-toggle="tab" data-bs-target="#tabs-rejected" type="button" role="tab" aria-controls="tabs-rejected" aria-selected="false">
            <i class='bx bx-x-circle'></i> Rejected <span class="badge rounded-pill badge-center h-px-20 w-px-20 bg-label-danger ms-1"><?= count($groupedRequests['rejected']) ?></span>
          </button>
        </li>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content" id="tabs-tabContent">
        <?php foreach (['pending', 'approved', 'rejected'] as $status) : ?>
          <div class="tab-pane fade <?= $action == $status ? 'show active' : '' ?>" id="tabs-<?= $status ?>" role="tabpanel" aria-labelledby="tabs-<?= $status ?>-tab">
            <?php if (empty($groupedRequests[$status])) : ?>
              <div class="text-center">
                <i class="bx bxs-error-circle fs-1 text-muted mb-3"></i>
                <p class="text-muted">No <?= $status ?> requests found.</p>
              </div>
            <?php else : ?>
              <table class="table table-bordered table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Avatar</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Request Name</th>
                    <?php if ($status != 'pending') : ?>
                      <th>Admin Comment</th>
                    <?php endif; ?>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($groupedRequests[$status] as $request) : ?>
                    <tr>
                      <td><img src="<?= $request['Profile'] ?>" alt="Avatar" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;"></td>
                      <td><?= $request['Honorific'] . " " . $request['FirstName'] . " " . $request['LastName'] ?></td>
                      <td><?= $request['Email'] ?></td>
                      <td><span class="badge bg-label-<?= $status == 'approved' ? 'success' : ($status == 'rejected' ? 'danger' : 'warning') ?>"><?= $request['status'] ?></span></td>
                      <td><?= $request['request_name_1'] ?></td>
                      <?php if ($status != 'pending') : ?>
                        <td><?= $request['admin_comment'] ?></td>
                      <?php endif; ?>
                      <td>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal<?= ucfirst($status) . $request['request_id'] ?>">View Detail</button>
                      </td>
                    </tr>

                    <!-- Modal for Request Details -->
                    <div class="modal fade" id="exampleModal<?= ucfirst($status) . $request['request_id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Request Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <p><strong>Username:</strong> <?= $request['Honorific'] . " " . $request['FirstName'] . " " . $request['LastName'] ?></p>
                            <p><strong>Email:</strong> <?= $request['Email'] ?></p>
                            <p><strong>Status:</strong> <?= $request['status'] ?></p>
                            <p><strong>Request Name:</strong> <?= $request['request_name_1'] ?></p>
                            <!-- Display file attachments -->
                            <?php
                            // Fetch file attachments for this request
                            $attachmentsStmt = $dbh->prepare("SELECT * FROM tblrequest_attachments WHERE request_id = :request_id");
                            $attachmentsStmt->bindParam(":request_id", $request['request_id']);
                            $attachmentsStmt->execute();
                            $attachments = $attachmentsStmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            <p><strong>Attachments:</strong></p>
                            <ul>
                              <?php foreach ($attachments as $attachment) : ?>
                                <li><a href="<?= $attachment['file_path'] ?>" target="_blank"><?= $attachment['file_path'] ?></a></li>
                              <?php endforeach; ?>
                            </ul>
                            <?php if ($status != 'pending') : ?>
                              <p><strong>Admin Comment:</strong> <?= $request['admin_comment'] ?></p>
                            <?php endif;                            ?>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <?php if ($status == 'completed') : ?>
                              <!-- Button trigger modal for viewing reports -->
                              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reportModal<?= $request['request_id'] ?>">
                                View Reports
                              </button>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    </div>

                    <?php if ($status == 'completed') : ?>
                      <!-- Modal for Reports -->
                      <div class="modal fade" id="reportModal<?= $request['request_id'] ?>" tabindex="-1" aria-labelledby="reportModalLabel<?= $request['request_id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="reportModalLabel<?= $request['request_id'] ?>">Reports for Request ID <?= $request['request_id'] ?></h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <?php
                              // Fetch reports related to this request
                              $reportsStmt = $dbh->prepare("SELECT * FROM tblreport_step3 WHERE request_id = :request_id");
                              $reportsStmt->bindParam(":request_id", $request['request_id']);
                              $reportsStmt->execute();
                              $reports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC);

                              if (empty($reports)) {
                                echo "<p>No reports found for this request.</p>";
                              } else {
                                echo "<ul>";
                                foreach ($reports as $report) {
                                  echo "<li><strong>Report ID:</strong> {$report['report_id']}</li>";
                                  echo "<li><strong>Report Name:</strong> {$report['report_name']}</li>";
                                  echo "<li><strong>Report Details:</strong> {$report['report_details']}</li>";
                                  echo "<hr>";
                                }
                                echo "</ul>";
                              }
                              ?>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </tbody>
              </table>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Completed Requests Section -->
    <div class="card mb-3">
      <div class="card-header border-bottom mb-3">
        <h5 class="card-title"><i class='bx bx-check-double bg-label-primary p-2 rounded-circle'></i> Completed</h5>
        <form method="GET" action="">
          <div class="row mb-0">
            <div class="col-md-3">
              <label for="year" class="form-label">Year</label>
              <select id="year" name="year" class="form-control w-100 select2">
                <?php foreach ($available_years as $yr) : ?>
                  <option value="<?= $yr ?>" <?= $year == $yr ? 'selected' : '' ?>><?= $yr ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button type="submit" class="btn btn-primary">Filter</button>
            </div>
          </div>
        </form>
      </div>
      <div class="card-body">
        <?php if (empty($groupedRequests['completed'])) : ?>
          <div class="text-center">
            <i class="bx bxs-error-circle fs-1 text-muted mb-3"></i>
            <p class="text-muted">No completed requests found.</p>
          </div>
        <?php else : ?>
          <table class="table table-bordered table-hover">
            <thead class="table-light">
              <tr>
                <th>Avatar</th>
                <th>Username</th>
                <th>Email</th>
                <th>Status</th>
                <th>Request Name</th>
                <th>Admin Comment</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($groupedRequests['completed'] as $request) : ?>
                <tr>
                  <td><img src="<?= $request['Profile'] ?>" alt="Avatar" class="rounded-circle" style="width: 50px; height: 50px; object-fit:cover;"></td>
                  <td><?= $request['Honorific'] . " " . $request['FirstName'] . " " . $request['LastName'] ?></td>
                  <td><?= $request['Email'] ?></td>
                  <td><span class="badge bg-label-primary"><?= $request['status'] ?></span></td>
                  <td><?= $request['request_name_1'] ?></td>
                  <td><?= $request['admin_comment'] ?></td>
                  <td>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalCompleted<?= $request['request_id'] ?>">View Detail</button>
                  </td>
                </tr>

                <!-- Modal for Completed Request Details -->
                <div class="modal fade" id="exampleModalCompleted<?= $request['request_id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Request Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <p><strong>Username:</strong> <?= $request['Honorific'] . " " . $request['FirstName'] . " " . $request['LastName'] ?></p>
                        <p><strong>Email:</strong> <?= $request['Email'] ?></p>
                        <p><strong>Status:</strong><span class="mx-2 badge bg-label-primary"> <?= $request['status'] ?></span> </p>
                        <p><strong>Request Name:</strong> <?= $request['request_name_1'] ?></p>
                        <!-- Display file attachments -->
                        <?php
                        // Fetch file attachments for this request
                        $attachmentsStmt = $dbh->prepare("SELECT * FROM tblrequest_attachments WHERE request_id = :request_id");
                        $attachmentsStmt->bindParam(":request_id", $request['request_id']);
                        $attachmentsStmt->execute();
                        $attachments = $attachmentsStmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <p><strong>Attachments:</strong></p>
                        <ul>
                          <?php foreach ($attachments as $attachment) : ?>
                            <li><a href="<?= $attachment['file_path'] ?>" target="_blank"><?= $attachment['file_path'] ?></a></li>
                          <?php endforeach; ?>
                        </ul>
                        <p><strong>Admin Comment:</strong> <?= $request['admin_comment'] ?></p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <!-- Button trigger modal for viewing reports -->
                        <a href="view_reports.php?request_id=<?= $request['request_id'] ?>" class="btn btn-primary"><i class="bx bx-file"></i>View Reports</a>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
include('../../layouts/admin_layout.php');
?>
