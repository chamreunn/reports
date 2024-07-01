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
  .progress{
    height: 6px;
  }
</style>

<div class="row">
  <div class="col-12">
    <div class="card border-2 mb-4">
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
      <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
          <?php echo translate('Make A Request'); ?>
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
          <?php if (!empty($regulators)) : ?>
            <?php foreach ($regulators as $regulator) : ?>
              <li>
                <a class="dropdown-item" href="make_request.php?rep=<?php echo htmlentities($regulator['RegulatorName']) ?>&shortname=<?php echo htmlentities($regulator['ShortName']) ?>">
                  <?php echo htmlentities($regulator['RegulatorName']); ?>
                </a>
              </li>
            <?php endforeach; ?>
          <?php else : ?>
            <li><a class="dropdown-item" href="javascript:void(0);">No regulators found</a></li>
          <?php endif; ?>
        </ul>

      </div>
      <!-- <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">ស្នើសុំបង្កើតរបាយការណ៍</button> -->

      <div class="offcanvas offcanvas-end w-50" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel">
        <div class="offcanvas-header border-bottom d-flex align-items-center p-3">
          <h5 class="offcanvas-title mef2" id="offcanvasWithBothOptionsLabel">ស្នើសុំបង្កើតរបាយការណ៍</h5>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
          <form id="requestForm" action="" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="regulatorSelect" class="form-label">ជ្រើសរើសនិយ័តករ: <span class="text-danger">*</span></label>
              <select id="regulatorSelect" class="form-select select2" name="regulator" aria-label="Default select example" required>
                <option selected disabled>ជ្រើសរើសនិយ័តករ</option>
                <?php if (!empty($regulators)) : ?>
                  <?php foreach ($regulators as $regulator) : ?>
                    <option value="<?php echo htmlentities($regulator['RegulatorName']) ?>&shortname=<?php echo htmlentities($regulator['ShortName']) ?>"><?php echo htmlentities($regulator['RegulatorName']); ?></option>
                  <?php endforeach; ?>
                <?php else : ?>
                  <option>No regulators found</option>
                <?php endif; ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="requestName" class="form-label">ឈ្មោះសំណើ: <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="requestName" name="request_name" value="សេចក្តីព្រាងរបាយការណ៍សវនកម្ម" required>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">មតិសំណើ: <span class="text-danger">*</span></label>
              <textarea class="form-control" id="description" placeholder="មតិសំណើ" name="description" rows="3" required></textarea>
            </div>

            <label for="fileInput" class="form-label">ឯកសារភ្ជាប់: <span class="text-danger">*</span></label>
            <div class="mb-3 custom-dropzone" id="dropzone">
              <label for="fileInput" class="form-label dropzone text-center">Drag and drop files here or click to upload:</label>
              <input type="file" class="form-control" id="fileInput" name="files[]" multiple accept=".pdf,.doc,.docx" style="display: none;" required>
              <small id="fileHelp" class="form-text text-muted">You can select files one by one, and they will be added to the list.</small>
            </div>
            <div class="list-group">
              <div id="fileList"></div>
            </div>
            <div class="mb-3">
              <label for="link1" class="form-label">Link (តំណភ្ជាប់ឯកសារ)</label>
              <input type="url" class="form-control" id="link1" name="link1" placeholder="https://example.com/example.pdf">
            </div>
          </form>
        </div>
        <div class="offcanvas-footer border-top p-3 d-flex justify-content-end">
          <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Cancel</button>
          <button type="submit" form="requestForm" class="btn btn-primary">Submit</button>
        </div>
      </div>

    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');
        const dropzone = document.getElementById('dropzone');

        // Hypothetical internet upload speed in bytes per second (e.g., 500 KB/s)
        const uploadSpeed = 500 * 1024;

        dropzone.addEventListener('click', function() {
          fileInput.click();
        });

        fileInput.addEventListener('change', handleFileSelect);
        dropzone.addEventListener('dragover', handleDragOver);
        dropzone.addEventListener('drop', handleFileDrop);

        function handleFileSelect(event) {
          const files = event.target.files;
          addFiles(files);
        }

        function handleDragOver(event) {
          event.stopPropagation();
          event.preventDefault();
          event.dataTransfer.dropEffect = 'copy';
        }

        function handleFileDrop(event) {
          event.stopPropagation();
          event.preventDefault();
          const files = event.dataTransfer.files;
          addFiles(files);
        }

        function addFiles(files) {
          for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const listItem = document.createElement('div');
            listItem.classList.add('list-group-item', 'd-flex','flex-column', 'justify-content-between', 'align-items-center', 'mb-2');

            const fileName = document.createElement('span');
            fileName.textContent = file.name;
            listItem.appendChild(fileName);

            const progressBarWrapper = document.createElement('div');
            progressBarWrapper.classList.add('progress', 'flex-grow-1','w-100', 'me-2', 'mx-2', 'mb-3','mt-3');
            const progressBar = document.createElement('div');
            progressBar.classList.add('progress-bar','h-4');
            progressBar.setAttribute('role', 'progressbar');
            progressBar.setAttribute('aria-valuemin', '0');
            progressBar.setAttribute('aria-valuemax', '100');
            progressBarWrapper.appendChild(progressBar);
            listItem.appendChild(progressBarWrapper);

            const deleteButton = document.createElement('button');
            deleteButton.classList.add('btn', 'btn-danger', 'btn-sm');
            deleteButton.textContent = 'Delete';
            deleteButton.addEventListener('click', function() {
              fileList.removeChild(listItem);
            });
            listItem.appendChild(deleteButton);

            fileList.appendChild(listItem);

            simulateUpload(file, progressBar);
          }
        }

        function simulateUpload(file, progressElement) {
          const totalSize = file.size;
          const totalUploadTime = totalSize / uploadSpeed; // in seconds
          let uploadedSize = 0;

          const interval = setInterval(() => {
            uploadedSize += uploadSpeed / 10; // update every 100ms
            const percentComplete = Math.min((uploadedSize / totalSize) * 100, 100);

            progressElement.style.width = percentComplete + '%';
            progressElement.textContent = Math.round(percentComplete) + '%';

            if (percentComplete >= 100) {
              clearInterval(interval);
              progressElement.classList.add('bg-success');
              progressElement.textContent = 'Upload complete';
            }
          }, 100); // Update every 100ms
        }
      });
    </script>

    <?php if (!empty($ongoingRequests)) : ?>
      <div class="row">
        <?php foreach ($ongoingRequests as $request) : ?>
          <div class="col-md-4 mb-4">
            <div class="card border-2">
              <div class="card-header d-flex justify-content-between border-bottom p-3">
                <h5 class="mef2 mb-0"><?php echo htmlentities($request['request_name_1']); ?></h5>
                <div class="badge mb-0 <?php echo 'status-' . str_replace('_', '-', htmlentities($request['status'])); ?>">
                  <?php echo ucfirst(htmlentities($request['status'])); ?>
                </div>
              </div>
              <div class="card-body p-3">
                <p><strong>Regulator:</strong> <?php echo htmlentities($request['Regulator']); ?></p>
                <p><strong>Shortname:</strong> <?php echo htmlentities($request['shortname']); ?></p>
                <p><strong>Description:</strong> <?php echo htmlentities($request['description_1']); ?></p>
                <?php if (!empty($request['link_1'])) : ?>
                  <p><a target="_blank" href="<?php echo htmlentities($request['link_1']); ?>">Link Documents</a></p>
                <?php endif; ?>
                <?php if (!empty($request['link_2'])) : ?>
                  <p><a target="_blank" href="<?php echo htmlentities($request['link_2']); ?>">Link Documents</a></p>
                <?php endif; ?>
                <?php if (!empty($request['link_3'])) : ?>
                  <p><a target="_blank" href="<?php echo htmlentities($request['link_3']); ?>">Link Documents</a></p>
                <?php endif; ?>
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
                  <ul class="list-unstyled">
                    <?php foreach ($attachments as $attachment) : ?>
                      <li>
                        <a href="<?php echo htmlentities($attachment['file_path']); ?>" target="_blank">
                          <?php echo basename(htmlentities($attachment['file_path'])); ?>
                        </a>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateFileModal<?php echo $attachment['id']; ?>"><i class="bx bxs-edit-alt me-2"></i>Update</button>
                      </li>
                      <!-- Modal for updating file -->
                      <div class="modal fade" id="updateFileModal<?php echo $attachment['id']; ?>" tabindex="-1" aria-labelledby="updateFileModalLabel<?php echo $attachment['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h5 class="modal-title" id="updateFileModalLabel<?php echo $attachment['id']; ?>">Update File</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form method="POST" enctype="multipart/form-data" action="../../controllers/replace_file/replace_file.php">
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
                  </ul>
                <?php endif; ?>
              </div>
              <div class="footer border-top p-3">
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
                if ($request['status'] == 'pending' && $request['step'] == 1) : ?>
                  <a href="create_reports_page2.php?request_id=<?php echo $request['id']; ?>&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary w-100 disabled">Create Report<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                <?php elseif ($request['status'] == 'approved' && $row1['count'] > 0 && $request['step'] == 1) : ?>
                  <!-- View Report Step 1 -->
                  <a href="view_report_step1.php?request_id=<?php echo $request['id']; ?>&shortname=<?php echo $request['shortname'] ?>&regulator=<?php echo $request['Regulator'] ?>" class="btn btn-primary mt-3 me-2">Review & Edit<i class="bx bx-edit-alt mx-2 me-0"></i></a>
                  <a href="make_request2.php?request_id=<?php echo $request['id']; ?>&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary w-100">Create Follow-Up Request<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                <?php elseif ($request['status'] == 'approved' && $row2['count'] > 0 && $request['step'] == 2) : ?>
                  <!-- View Report Step 2 -->
                  <a href="view_report_step2.php?request_id=<?php echo $request['id']; ?>&shortname=<?php echo $request['shortname'] ?>&regulator=<?php echo $request['Regulator'] ?>" class="btn btn-primary mt-3 me-2">Review & Edit<i class="bx bx-edit-alt mx-2 me-0"></i></a>
                  <a href="make_request3.php?request_id=<?php echo $request['id']; ?>&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary mt-3">Create Follow-Up Request<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                <?php elseif ($request['status'] == 'completed' && $row3['count'] > 0 && $request['step'] == 3) : ?>
                  <!-- View Report Step 3 -->
                  <a href="view_report_step3.php?request_id=<?php echo $request['id']; ?>&shortname=<?php echo $request['shortname'] ?>&regulator=<?php echo $request['Regulator'] ?>" class="btn btn-primary mt-3 me-2">Review & Edit<i class="bx bx-edit-alt mx-2 me-0"></i></a>
                  <a href="create_reports_final.php?request_id=<?php echo $request['id']; ?>&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary mt-3">Create Final Report<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                <?php elseif ($request['status'] == 'pending' && $request['step'] == 3) : ?>
                  <!-- Create Report Step 2 -->
                  <a href="create_reports_page2.php?request_id=<?php echo $request['id']; ?>&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary w-100 disabled">Create Report<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                <?php elseif ($request['status'] == 'approved' && $request['step'] == 3) : ?>
                  <!-- Make Report Step 2 -->
                  <a href="create_report3.php?request_id=<?php echo $request['id']; ?>&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary w-100">Create Report<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                <?php elseif ($request['status'] == 'pending' && $request['step'] == 2) : ?>
                  <!-- Create Report Step 2 -->
                  <a href="create_reports_page2.php?request_id=<?php echo $request['id']; ?>&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary w-100 disabled">Create Report<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                <?php elseif ($request['status'] == 'approved' && $request['step'] == 2) : ?>
                  <!-- Make Report Step 2 -->
                  <a href="create_report2.php?request_id=<?php echo $request['id']; ?>&shortname=<?php echo $request['shortname'] ?>" class="btn btn-outline-primary w-100">Create Report<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                <?php elseif ($request['status'] == 'approved' && $request['step'] == 1) : ?>
                  <!-- Make Report Step 1 -->
                  <a href="create_report1.php?request_id=<?php echo $request['id']; ?>&shortname=<?php echo $request['shortname'] ?>" class="btn btn-primary w-100">Create Report<i class="bx bx-chevrons-right mx-2 me-0"></i></a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else : ?>
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
