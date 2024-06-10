<?php
session_start();
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

$pageTitle = "ទំព័រដើម";
$sidebar = "home";
ob_start(); // Start output buffering
include('../../controllers/form_process.php');
$getid = $_GET['request_id'];
$getregulator = $_GET['regulator'];
// Fetch data from the tblreport_step1 table where ID matches $getid
$stmt = $dbh->prepare("SELECT headline, data FROM tblreport_step2 WHERE request_id = :id");
$stmt->bindParam(':id', $getid, PDO::PARAM_INT);
$stmt->execute();
$insertedData = $stmt->fetch(PDO::FETCH_ASSOC);

// Assuming $insertedData contains the inserted data
?>
<style>
  @font-face {
    font-family: 'Khmer MEF2';
    src: url('../../fonts/KhmerMEF2.woff2') format('woff2'),
      url('../../fonts/KhmerMEF2.woff') format('woff');
    font-weight: normal;
    font-style: normal;
  }

  .khmer-font {
    font-family: 'Khmer MEF2', sans-serif;
  }

  p {
    font-size: 16px;
    text-align: justify;
    line-height: 2.0;
    /* Adjust line height for better readability */
  }
</style>
<div class="row">
  <div class="card">
    <div class="card-body">
      <div class="col-12 mb-4">
        <div class="row mb-3 align-items-center">
          <div class="col-12 col-lg-6 mb-3 mb-lg-0">
            <h3 class="khmer-font">Report Details</h3>
          </div>
          <div class="col-12 col-lg-6 text-lg-end">
            <a href="edit2.php?id=<?php echo $getid; ?>&regulator=<?php echo $getregulator; ?>" class="btn btn-outline-info custom-button">
              <i class="bx bx-edit-alt me-2 mx-0"></i> Edit
            </a>
            <button class="btn btn-primary dropdown-toggle custom-button mb-2 mb-lg-0 me-lg-2" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bx bxs-file-export me-2 mx-0"></i> Export
            </button>
            <ul class="dropdown-menu" aria-labelledby="exportDropdown">
              <li><a class="dropdown-item" href="../../controllers/export2.php?id=<?php echo $getid; ?>&regulator=<?php echo $getregulator; ?>&type=word"><i class="bx bxs-file-export me-2"></i> Export to Word</a></li>
              <!-- <li><a class="dropdown-item" href="export1.php?id=<?php echo $getid; ?>&type=pdf"><i class="bx bxs-file-pdf me-2"></i> Export to PDF</a></li> -->
            </ul>
          </div>
        </div>
        <div class="content">
          <?php
          // Use newline as the delimiter to split the headlines and data
          $headlines = explode("\n", trim($insertedData['headline']));
          $data = explode("\n", trim($insertedData['data']));

          foreach ($headlines as $index => $headline) {
            // Decode HTML entities and trim leading spaces for headlines
            $headline = preg_replace('/^(&nbsp;|\s)+/', '', htmlspecialchars_decode($headline));
            // Decode HTML entities for data lines without escaping HTML again
            $dataLine = htmlspecialchars_decode($data[$index] ?? '');
          ?>
            <div class="row g-3 mb-4">
              <div class="col-sm-12">
                <h5 class="khmer-font h4"><?php echo $headline; ?></h5>
                <p><?php echo nl2br($dataLine); ?></p>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); ?>

<?php include('../../includes/layout.php'); ?>
