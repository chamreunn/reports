<?php
session_start();
include('../../config/dbconn.php');

if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}
// translate
include('../../includes/translate.php');
$requestId = $_SESSION['userid'];
$pageTitle = "របាយការណ៍សវនកម្ម";
$sidebar = "audits";
// Fetch ongoing requests from the database along with their attachments
$ongoingRequests = [];
try {
  // Prepare the SQL query with a condition to filter requests for the logged-in user
  $sql = "SELECT r.*, GROUP_CONCAT(ra.file_path) AS file_paths
            FROM tblrequest r
            LEFT JOIN tblrequest_attachments ra ON r.id = ra.request_id
            WHERE (r.status != 'completed' AND r.status != 'rejected')
            AND r.user_id = :user_id
            GROUP BY r.id";

  // Prepare and execute the statement with the user_id parameter
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':user_id', $_SESSION['userid'], PDO::PARAM_INT);
  $stmt->execute();
  $ongoingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Calculate counts
  $pendingRequestsCount = 0;
  $approvedRequestsCount = 0;
  $rejectedRequestsCount = 0;

  foreach ($ongoingRequests as $request) {
    switch ($request['status']) {
      case 'pending':
        $pendingRequestsCount++;
        break;
      case 'approved':
        $approvedRequestsCount++;
        break;
      case 'rejected':
        $rejectedRequestsCount++;
        break;
      default:
        break;
    }
  }
} catch (PDOException $e) {
  echo "Database error: " . $e->getMessage();
}


// Fetch completed requests from the database
$completedRequests = [];
try {
  $sql = "SELECT r.*, GROUP_CONCAT(ra.file_path) AS file_paths
            FROM tblrequest r
            LEFT JOIN tblrequest_attachments ra ON r.id = ra.request_id
            WHERE r.status = 'completed'
            GROUP BY r.id";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $completedRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Calculate completed count
  $completedRequestsCount = count($completedRequests);
} catch (PDOException $e) {
  echo "Database error: " . $e->getMessage();
}
try {
  $sql = "SELECT RegulatorName, ShortName FROM tblregulator";
  $query = $dbh->prepare($sql);
  $query->execute();
  $regulators = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo "Database error: " . $e->getMessage();
}
date_default_timezone_set('Asia/Bangkok');
function formatDateKhmer($date)
{
  // Convert the date to a timestamp
  $timestamp = strtotime($date);

  // Format the date parts
  $dayOfWeek = date('l', $timestamp);
  $day = date('d', $timestamp);
  $month = date('m', $timestamp);
  $year = date('Y', $timestamp);
  $hour = date('h', $timestamp);
  $minute = date('i', $timestamp);
  $amPm = date('A', $timestamp);

  // Translate English day of the week to Khmer
  $daysOfWeekKhmer = [
    'Sunday' => 'អាទិត្យ',
    'Monday' => 'ច័ន្ទ',
    'Tuesday' => 'អង្គារ',
    'Wednesday' => 'ពុធ',
    'Thursday' => 'ព្រហស្បតិ៍',
    'Friday' => 'សុក្រ',
    'Saturday' => 'សៅរ៍'
  ];

  // Translate AM/PM to Khmer
  $amPmKhmer = [
    'AM' => 'ព្រឹក',
    'PM' => 'ល្ងាច'
  ];

  // Build the formatted date string
  $formattedDate = sprintf(
    '%s-%s-%s, %s:%s%s',
    $daysOfWeekKhmer[$dayOfWeek],
    $day,
    $year,
    $hour,
    $minute,
    $amPmKhmer[$amPm]
  );

  return $formattedDate;
}
// Check if data exists in tblreport_step1 for the given request ID
$stmt = $dbh->prepare("SELECT COUNT(*) AS count FROM tblreport_step1 WHERE request_id = :request_id");
$stmt->bindParam(':request_id', $requestId, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

ob_start();
?>
<style>
  .card {
    transition: transform 0.2s ease-in-out;
  }

  .status-pending {
    color: #FFFFFF;
    background-color: #FFC107;
    /* Yellow */
  }

  .status-approved {
    color: #FFFFFF;
    background-color: #28A745;
    /* Green */
  }

  .status-rejected {
    color: #FFFFFF;
    background-color: #DC3545;
    /* Red */
  }

  .status-completed {
    color: #FFFFFF;
    background-color: #007BFF;
    /* Blue */
  }

  .card-title {
    font-size: 1.25rem;
  }

  .card-footer {
    background-color: #f8f9fa;
    /* Light Gray */
  }
</style>
<div class="row mb-3">
  <div class="col-md-12">
    <h2 class="mb-0"><?php echo $pageTitle; ?></h2>
  </div>
</div>
<div class="row mb-3">
  <div class="col-12">
    <div class="card mb-4">
      <div class="card-widget-separator-wrapper">
        <div class="card-body card-widget-separator">
          <div class="row gy-4 gy-sm-1">
            <!-- Pending Requests -->
            <div class="col-sm-6 col-lg-3">
              <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                <div>
                  <h3 class="mb-1"><?php echo $pendingRequestsCount; ?></h3>
                  <p class="mb-0">Pending Requests</p>
                </div>
                <span class="badge bg-label-warning rounded p-2 me-sm-4">
                  <i class="bx bx-time bx-sm"></i>
                </span>
              </div>
              <hr class="d-none d-sm-block d-lg-none me-4">
            </div>
            <!-- Approved Requests -->
            <div class="col-sm-6 col-lg-3">
              <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                <div>
                  <h3 class="mb-1"><?php echo $approvedRequestsCount; ?></h3>
                  <p class="mb-0">Approved Requests</p>
                </div>
                <span class="badge bg-label-success rounded p-2 me-lg-4">
                  <i class="bx bx-check bx-sm"></i>
                </span>
              </div>
              <hr class="d-none d-sm-block d-lg-none">
            </div>
            <!-- Rejected Requests -->
            <div class="col-sm-6 col-lg-3">
              <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                <div>
                  <h3 class="mb-1"><?php echo $rejectedRequestsCount; ?></h3>
                  <p class="mb-0">Rejected Requests</p>
                </div>
                <span class="badge bg-label-danger rounded p-2 me-sm-4">
                  <i class="bx bx-x-circle bx-sm"></i>
                </span>
              </div>
            </div>
            <!-- Completed Requests -->
            <div class="col-sm-6 col-lg-3">
              <div class="d-flex justify-content-between align-items-start pb-3 pb-sm-0 card-widget-4">
                <div>
                  <h3 class="mb-1"><?php echo $completedRequestsCount; ?></h3>
                  <p class="mb-0">Completed Requests</p>
                </div>
                <span class="badge bg-label-primary rounded p-2">
                  <i class="bx bx-check-double bx-sm"></i>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- show accordion  -->
<div class="row">
  <div class="col-md-12">
    <div class="mb-3 d-flex align-items-center justify-content-between">
      <h3 class="mb-0">Ongoing Requests</h3>
      <button type="button" class="btn btn-primary dropdown-toggle show" data-bs-toggle="dropdown" aria-expanded="false">
        <?php echo translate('Make A Request'); ?>
      </button>
      <!-- Dropdown Menu -->
      <ul class="dropdown-menu">
        <?php if (!empty($regulators)) : ?>
          <?php foreach ($regulators as $regulator) : ?>
            <li>
              <a class="dropdown-item" href="make_request.php?rep=<?php echo htmlentities($regulator['RegulatorName']) ?>&&shortname=<?php echo htmlentities($regulator['ShortName']) ?>">
                <?php echo htmlentities($regulator['RegulatorName']); ?>
              </a>
            </li>
          <?php endforeach; ?>
        <?php else : ?>
          <li><a class="dropdown-item" href="javascript:void(0);">No regulators found</a></li>
        <?php endif; ?>
      </ul>
    </div>
    <?php if (!empty($ongoingRequests)) : ?>
      <!-- Display ongoing requests as separate accordions -->
      <?php foreach ($ongoingRequests as $index => $request) : ?>
        <div class="accordion mt-3 mb-0" id="ongoingRequestAccordion<?php echo $index; ?>">
          <div class="card accordion-item">
            <h2 class="accordion-header mef2" id="heading<?php echo $index; ?>">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
                <?php echo htmlentities($request['request_name_1']); ?>
                <span class="mx-2 badge <?php echo 'status-' . str_replace('_', '-', htmlentities($request['status'])); ?>">
                  <?php echo ucfirst(htmlentities($request['status'])); ?>
                </span>
              </button>
            </h2>
            <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#ongoingRequestAccordion<?php echo $index; ?>">
              <div class="accordion-body">
                <p><strong>Regulator:</strong> <?php echo htmlentities($request['Regulator']); ?></p>
                <p><strong>Shortname:</strong> <?php echo htmlentities($request['shortname']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlentities($request['description_1']); ?></p>
                <p><strong>Created At:</strong> <?php echo formatDateKhmer($request['created_at']); ?></p>

                <?php
                // Fetch attachments for the current request
                $sql = "SELECT id, file_path FROM tblrequest_attachments WHERE request_id = :request_id";
                $stmt = $dbh->prepare($sql);
                $stmt->bindParam(':request_id', $request['id'], PDO::PARAM_INT);
                $stmt->execute();
                $attachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <?php if (!empty($attachments)) : ?>
                  <p><strong>Attachments:</strong></p>
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>File</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($attachments as $attachment) : ?>
                        <tr>
                          <td>
                            <a href="<?php echo htmlentities($attachment['file_path']); ?>" target="_blank">
                              <?php echo basename(htmlentities($attachment['file_path'])); ?>
                            </a>
                          </td>
                          <td>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateFileModal<?php echo $attachment['id']; ?>"><i class="bx bxs-edit-alt me-2"></i>Update</button>
                          </td>
                        </tr>

                        <!-- Modal for updating file -->
                        <div class="modal animate__animated animate__bounceIn" id="updateFileModal<?php echo $attachment['id']; ?>" tabindex="-1" aria-labelledby="updateFileModalLabel<?php echo $attachment['id']; ?>" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="updateFileModalLabel<?php echo $attachment['id']; ?>">Update File</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <form method="POST" enctype="multipart/form-data" action="../../controllers/replace_file.php">
                                <div class="modal-body">
                                  <input type="hidden" name="attachment_id" value="<?php echo $attachment['id']; ?>">
                                  <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                  <div class="mb-3">
                                    <label class="form-label">Current File:</label>
                                    <a href="<?php echo htmlentities($attachment['file_path']); ?>" target="_blank">
                                      <?php echo basename(htmlentities($attachment['file_path'])); ?>
                                    </a>
                                  </div>
                                  <div class="mb-3">
                                    <label for="fileInput<?php echo $attachment['id']; ?>" class="form-label">Choose New File:</label>
                                    <input type="file" class="form-control" id="fileInput<?php echo $attachment['id']; ?>" name="new_file" accept=".pdf,.doc,.docx" required>
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                  <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                <?php endif; ?>

                <?php
                // Check if data exists in tblreport_step1 for this user ID
                $stmt_count1 = $dbh->prepare("SELECT COUNT(*) AS count FROM tblreport_step1 AS rs1 JOIN tblrequest AS tr ON rs1.request_id = tr.id WHERE tr.user_id = :user_id");
                $stmt_count1->bindParam(':user_id', $_SESSION['userid'], PDO::PARAM_INT);
                $stmt_count1->execute();
                $row1 = $stmt_count1->fetch(PDO::FETCH_ASSOC);

                // Check if data exists in tblreport_step2 for this user ID
                $stmt_count2 = $dbh->prepare("SELECT COUNT(*) AS count FROM tblreport_step2 AS rs2 JOIN tblrequest AS tr ON rs2.request_id = tr.id WHERE tr.user_id = :user_id");
                $stmt_count2->bindParam(':user_id', $_SESSION['userid'], PDO::PARAM_INT);
                $stmt_count2->execute();
                $row2 = $stmt_count2->fetch(PDO::FETCH_ASSOC);

                // Check if data exists in tblreport_step3 for this user ID
                $stmt_count3 = $dbh->prepare("SELECT COUNT(*) AS count FROM tblreport_step3 AS rs3 JOIN tblrequest AS tr ON rs3.request_id = tr.id WHERE tr.user_id = :user_id");
                $stmt_count3->bindParam(':user_id', $_SESSION['userid'], PDO::PARAM_INT);
                $stmt_count3->execute();
                $row3 = $stmt_count3->fetch(PDO::FETCH_ASSOC);
                ?>
                <div class="d-flex justify-content-end">
                  <?php if ($request['status'] == 'pending' && $request['step'] == 1) : ?>
                    <a href="create_reports_page2.php?request_id=<?php echo $request['id']; ?>&&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary mt-3 disabled">បង្កើតរបាយការណ៍<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                  <?php elseif ($request['status'] == 'approved' && $row1['count'] > 0 && $request['step'] == 1) : ?>
                    <!-- View Report Step 1 -->
                    <a href="view_report_step1.php?request_id=<?php echo $request['id']; ?>&&shortname=<?php echo $request['shortname'] ?>&&regulator=<?php echo $request['Regulator'] ?>" class="btn btn-primary mt-3 me-2">ពិនិត្យ និងកែប្រែ<i class="bx bx-edit-alt mx-2 me-0"></i></a>
                    <a href="make_request2.php?request_id=<?php echo $request['id']; ?>&&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary mt-3">បង្កើតសំណើបន្ត<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                  <?php elseif ($request['status'] == 'approved' && $row2['count'] > 0 && $request['step'] == 2) : ?>
                    <!-- View Report Step 2 -->
                    <a href="view_report_step2.php?request_id=<?php echo $request['id']; ?>&&shortname=<?php echo $request['shortname'] ?>&&regulator=<?php echo $request['Regulator'] ?>" class="btn btn-primary mt-3 me-2">ពិនិត្យ និងកែប្រែ<i class="bx bx-edit-alt mx-2 me-0"></i></a>
                    <a href="make_request3.php?request_id=<?php echo $request['id']; ?>&&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary mt-3">បង្កើតសំណើបន្ត<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                  <?php elseif ($request['status'] == 'completed' && $row3['count'] > 0 && $request['step'] == 3) : ?>
                    <!-- View Report Step 3 -->
                    <a href="view_report_step3.php?request_id=<?php echo $request['id']; ?>&&shortname=<?php echo $request['shortname'] ?>&&regulator=<?php echo $request['Regulator'] ?>" class="btn btn-primary mt-3 me-2">ពិនិត្យ និងកែប្រែ<i class="bx bx-edit-alt mx-2 me-0"></i></a>
                    <a href="create_reports_final.php?request_id=<?php echo $request['id']; ?>&&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary mt-3">បង្កើតសំណើបន្ត<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                  <?php elseif ($request['status'] == 'pending' && $request['step'] == 3) : ?>
                    <!-- Create Report Step 2 -->
                    <a href="create_reports_page2.php?request_id=<?php echo $request['id']; ?>&&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary mt-3 disabled">បង្កើតរបាយការណ៍<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                  <?php elseif ($request['status'] == 'approved' && $request['step'] == 3) : ?>
                    <!-- Make Report Step 2 -->
                    <a href="create_report3.php?request_id=<?php echo $request['id']; ?>&&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary mt-3">បង្កើតរបាយការណ៍<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                  <?php elseif ($request['status'] == 'pending' && $request['step'] == 2) : ?>
                    <!-- Create Report Step 2 -->
                    <a href="create_reports_page2.php?request_id=<?php echo $request['id']; ?>&&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary mt-3 disabled">បង្កើតរបាយការណ៍<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                  <?php elseif ($request['status'] == 'approved' && $request['step'] == 2) : ?>
                    <!-- Make Report Step 2 -->
                    <a href="create_report2.php?request_id=<?php echo $request['id']; ?>&&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary mt-3">បង្កើតរបាយការណ៍<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                  <?php elseif ($request['status'] == 'approved' && $request['step'] == 1) : ?>
                    <!-- Make Report Step 1 -->
                    <a href="create_report1.php?request_id=<?php echo $request['id']; ?>&&shortname=<?php echo $request['shortname'] ?>" class="btn btn-primary mt-3">បង្កើតរបាយការណ៍<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else : ?>
      <!-- Show a card with a Boxicons icon and message if there are no ongoing requests -->
      <div class="card-body text-center">
        <div class="card mt-3">
          <div class="card-body text-center">
            <i class='bx bxs-info-circle bx-4x text-muted mb-3'></i>
            <h5 class="card-title">No ongoing requests</h5>
            <p class="card-text">There are no ongoing requests at the moment.</p>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php
// Fetch completed and rejected requests from the database
$completedAndRejectedRequests = [];
try {
  // Prepare the SQL query with a condition to filter requests for the logged-in user
  $sql = "SELECT r.*, GROUP_CONCAT(ra.file_path) AS file_paths
            FROM tblrequest r
            LEFT JOIN tblrequest_attachments ra ON r.id = ra.request_id
            WHERE r.status IN ('completed', 'rejected')
            AND r.user_id = :user_id
            GROUP BY r.id";

  // Prepare and execute the statement with the user_id parameter
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':user_id', $_SESSION['userid'], PDO::PARAM_INT);
  $stmt->execute();
  $completedAndRejectedRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  // Handle database errors by setting an empty array and displaying an error message
  $completedAndRejectedRequests = [];
  $errorMessage = "Database error: " . $e->getMessage();
}

?>
<!-- HTML code for displaying completed and rejected requests -->
<div class="row mt-4">
  <div class="col-md-12">
    <h3 class="mb-3">Completed and Rejected Requests</h3>
    <?php if (empty($completedAndRejectedRequests)) : ?>
      <div class="card">
        <div class="card-body text-center">
          <i class="bx bx-folder-open bx-lg text-muted mb-3"></i>
          <p class="mb-0">No completed or rejected requests available.</p>
          <?php if (isset($errorMessage)) : ?>
            <p class="text-danger"><?php echo $errorMessage; ?></p>
          <?php endif; ?>
        </div>
      </div>
    <?php else : ?>
      <div class="card">
        <div class="card-datatable table-responsive">
          <table id="notificationsTable" class="dt-responsive table border-top">
            <thead>
              <tr>
                <th>Request Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Admin Comment</th>
                <th>Last Update</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($completedAndRejectedRequests as $request) : ?>
                <tr>
                  <td><?php echo htmlentities($request['request_name_1']); ?></td>
                  <td><?php echo htmlentities($request['description_1']); ?></td>
                  <td>
                    <span class="badge <?php echo ($request['status'] == 'completed') ? 'bg-label-primary' : 'bg-label-danger'; ?>">
                      <?php echo ucfirst($request['status']); ?>
                    </span>
                  </td>
                  <td><?php echo $request['admin_comment'] ? htmlentities($request['admin_comment']) : 'N/A'; ?></td>
                  <td><?php echo formatDateKhmer($request['updated_at']); ?></td>
                  <td>
                    <a href="review.php?request_id=<?php echo $request['id']; ?>">Review</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>
<?php
// Get the content from output buffer
$content = ob_get_clean();

// Include layout or template file
include('../../layouts/user_layout.php');
?>
