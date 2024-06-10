<?php
session_start();
include('../../config/dbconn.php');

if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}
// translate
include('../../includes/translate.php');

$pageTitle = "ទំព័រដើម";
$sidebar = "home";
ob_start(); // Start output buffering
// Get request ID from the URL parameter
$requestId = isset($_GET['request_id']) ? $_GET['request_id'] : null;

// Fetch data from tblrequest for the specified request ID
$requestData = [];
try {
  $sql = "SELECT * FROM tblrequest WHERE id = :request_id";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':request_id', $requestId, PDO::PARAM_INT);
  $stmt->execute();
  $requestData = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  // Handle database errors by setting an empty array and displaying an error message
  $requestData = [];
  $errorMessage = "Database error: " . $e->getMessage();
}

// Fetch attachments for the specified request ID from tblrequest_attachments
$requestAttachments = [];
try {
  $sql = "SELECT * FROM tblrequest_attachments WHERE request_id = :request_id";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':request_id', $requestId, PDO::PARAM_INT);
  $stmt->execute();
  $requestAttachments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  // Handle database errors by setting an empty array and displaying an error message
  $requestAttachments = [];
  $errorMessage = "Database error: " . $e->getMessage();
}

// Fetch data from tblreport_step3 for the specified request ID
$reportStep3Data = [];
try {
    $sql = "SELECT rs.*, tr.Regulator
            FROM tblreport_step3 rs
            INNER JOIN tblrequest tr ON rs.request_id = tr.id
            WHERE rs.request_id = :request_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':request_id', $requestId, PDO::PARAM_INT);
    $stmt->execute();
    $reportStep3Data = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle database errors by setting an empty array and displaying an error message
    $reportStep3Data = [];
    $errorMessage = "Database error: " . $e->getMessage();
}

?>
<!-- HTML code for displaying review data -->
<div class="row">
  <div class="col-md-12">
    <h3 class="mb-3">Review Request</h3>
    <?php if (empty($requestData)) : ?>
      <div class="card">
        <div class="card-body text-center">
          <i class="bx bx-folder-open bx-lg text-muted mb-3"></i>
          <p class="mb-0">No data available for this request.</p>
          <?php if (isset($errorMessage)) : ?>
            <p class="text-danger"><?php echo $errorMessage; ?></p>
          <?php endif; ?>
        </div>
      </div>
    <?php else : ?>
      <div class="accordion" id="accordionReview">
        <!-- Accordion Section for Request Details -->
        <div class="card accordion-item">
          <h2 class="accordion-header" id="headingRequest">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRequest" aria-expanded="false" aria-controls="collapseRequest">
              <i class="bx bx-detail me-2"></i> Request Details
            </button>
          </h2>
          <div id="collapseRequest" class="accordion-collapse collapse" aria-labelledby="headingRequest" data-bs-parent="#accordionReview">
            <div class="accordion-body">
              <div class="row mb-3 align-items-center">
                <div class="col-md-3"><strong>Request Name:</strong></div>
                <div class="col-md-9"><?php echo htmlentities($requestData['request_name_1']); ?></div>
              </div>
              <div class="row mb-3 align-items-center">
                <div class="col-md-3"><strong>Description:</strong></div>
                <div class="col-md-9"><?php echo htmlentities($requestData['description_1']); ?></div>
              </div>
              <div class="row mb-3 align-items-center">
                <div class="col-md-3"><strong>Status:</strong></div>
                <div class="col-md-9">
                  <?php
                  $statusClass = ($requestData['status'] == 'completed') ? 'bg-label-primary' : 'bg-label-danger';
                  echo '<span class="badge ' . $statusClass . '">' . ucfirst($requestData['status']) . '</span>';
                  ?>
                </div>
              </div>
              <div class="row mb-3 align-items-center">
                <div class="col-md-3"><strong>Created At:</strong></div>
                <div class="col-md-9"><?php echo htmlentities($requestData['created_at']); ?></div>
              </div>
              <div class="row mb-3 align-items-center">
                <div class="col-md-3"><strong>Updated At:</strong></div>
                <div class="col-md-9"><?php echo htmlentities($requestData['updated_at']); ?></div>
              </div>
            </div>
          </div>
        </div>

        <?php if (!empty($requestAttachments)) : ?>
          <div class="card accordion-item">
            <h2 class="accordion-header" id="headingAttachments">
              <button class="accordion-button collapsed custom-accordion" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAttachments" aria-expanded="false" aria-controls="collapseAttachments">
              <i class="bx bx-paperclip me-2"></i>Attachments
              </button>
            </h2>
            <div id="collapseAttachments" class="accordion-collapse collapse" aria-labelledby="headingAttachments" data-bs-parent="#accordionReview">
              <div class="accordion-body">
                <ul class="list-group">
                  <?php foreach ($requestAttachments as $attachment) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                      <?php echo basename(htmlentities($attachment['file_path'])); ?>
                      <a href="<?php echo htmlentities($attachment['file_path']); ?>" target="_blank" class="btn btn-primary btn-sm">View</a>
                    </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- Accordion Section for Report Step 3 Details -->
        <?php if (!empty($reportStep3Data)) : ?>
          <div class="card accordion-item">
            <h2 class="accordion-header" id="headingReportStep3">
              <button class="accordion-button collapsed custom-accordion" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReportStep3" aria-expanded="false" aria-controls="collapseReportStep3">
                <i class="bx bxs-report me-2"></i> របាយការណ៍សវនកម្ម
              </button>
            </h2>
            <div id="collapseReportStep3" class="accordion-collapse collapse" aria-labelledby="headingReportStep3" data-bs-parent="#accordionReview">
              <div class="accordion-body">
                <div class="col-12 mb-4">
                  <div class="row mb-3 align-items-center">
                    <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                      <h3 class="khmer-font">Report Details</h3>
                    </div>
                    <div class="col-12 col-lg-6 text-lg-end">
                      <a href="edit3.php?id=<?php echo $requestId; ?>&type=word&regulator=<?php echo $regulator = $reportStep3Data['Regulator']; ?>" class="btn btn-outline-info custom-button">
                        <i class="bx bx-edit-alt me-2 mx-0"></i> Edit
                      </a>
                      <button class="btn btn-primary dropdown-toggle custom-button mb-2 mb-lg-0 me-lg-2" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bxs-file-export me-2 mx-0"></i> Export
                      </button>
                      <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                        <li><a class="dropdown-item" href="../../controllers/export3.php?id=<?php echo $requestId; ?>&type=word&regulator=<?php echo $regulator = $reportStep3Data['Regulator']; ?>"><i class="bx bxs-file-export me-2"></i> Export to Word</a></li>
                        <!-- <li><a class="dropdown-item" href="export1.php?id=<?php echo $requestId; ?>&type=pdf"><i class="bx bxs-file-pdf me-2"></i> Export to PDF</a></li> -->
                      </ul>
                    </div>
                  </div>
                  <div class="content">
                    <?php
                    // Use newline as the delimiter to split the headlines and data
                    $headlines = explode("\n", trim($reportStep3Data['headline']));
                    $data = explode("\n", trim($reportStep3Data['data']));

                    foreach ($headlines as $index => $headline) {
                      $headline = htmlspecialchars_decode($headline);
                      $dataLine = htmlspecialchars_decode($data[$index]);
                    ?>
                      <div class="row g-3 mb-4">
                        <div class="col-sm-12">
                          <h5 class="mef2 h4"><?php
                          echo $headline; ?></h5>
                          <p><?php echo $dataLine; ?></p>
                        </div>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</div>


<?php $content = ob_get_clean(); ?>

<?php include('../../includes/layout.php'); ?>
