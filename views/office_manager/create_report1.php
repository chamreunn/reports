<?php
session_start();
include('../../config/dbconn.php');

if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

$pageTitle = "Make Report - Step 1";
$sidebar = "home";

// Check if the request ID is provided in the URL
if (!isset($_GET['request_id']) || empty($_GET['request_id']) || !isset($_GET['shortname'])) {
  header('Location: ../../index.php');
  exit();
}

// Fetch the request ID from the URL
$requestId = $_GET['request_id'];
$getshortname = $_GET['shortname'];

// Fetch the form data from the database
$stmt = $dbh->prepare("SELECT headline, data FROM form_data"); // Limit to 14 records
$stmt->execute();
$form_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
ob_start();
?>

<div class="row">
  <div class="col-12 mb-4">
    <h4><?= $pageTitle ?></h4>
    <div id="wizard-validation" class="bs-stepper mt-2 linear">
      <div class="bs-stepper-header p-1">
        <?php foreach ($form_data as $index => $step) : ?>
          <?php
          $active_class = ($index == 0) ? 'active' : '';
          $completed_class = ($index > 0) ? 'completed' : '';
          ?>
          <div class="step <?= $active_class ?> <?= $completed_class ?>" data-target="#stepContent<?= $index ?>">
            <button type="button" class="step-trigger" aria-selected="<?= ($index == 0 ? 'true' : 'false') ?>">
              <span class="bs-stepper-circle"><?= ($index + 1) ?></span>
              <div class="d-block d-md-none"><?= htmlspecialchars($step['headline']) ?></div>
            </button>
          </div>
          <?php if ($index < count($form_data) - 1) : ?>
            <div class="line">
              <i class="bx bx-chevron-right d-none"></i>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <div class="bs-stepper-content">
        <!-- HTML form -->
        <form id="wizard-validation-form" method="post" action="../../controllers/process_report1.php">
          <input type="hidden" name="request_id" value="<?= $requestId ?>">
          <?php foreach ($form_data as $index => $step) : ?>
            <?php $active_class = ($index == 0) ? 'active' : ''; ?>
            <div id="stepContent<?= $index ?>" class="content <?= $active_class ?>">
              <div class="content-header mb-3">
                <?php
                $headline = ($step['headline'] === 'ឈ្មោះសវនដ្ឋាន') ? $getshortname : $step['headline'];
                echo "<input type='text' name='headline{$index}' class='form-control mef2 mb-0' value='" . htmlspecialchars_decode(str_replace('ឈ្មោះសវនដ្ឋាន', $getshortname, $headline)) . "' />";
                ?>
                <small>Enter Your <?= htmlspecialchars($headline); ?>.</small>
              </div>
              <div class="row g-3 mb-4">
                <div class="col-sm-12">
                  <div id="editor<?= $index ?>" class="quill-editor"><?= htmlspecialchars_decode(str_replace('ឈ្មោះសវនដ្ឋាន', $getshortname, $step['data'])) ?></div>
                  <!-- Hidden textarea to store Quill content -->
                  <textarea name="formValidationTextarea<?= $index ?>" style="display:none;"></textarea>
                </div>
              </div>
              <div class="card-footer sticky-bottom">
                <?php if ($index == count($form_data) - 1) : ?>
                  <div class="col-12 d-flex justify-content-between">
                    <button type="button" class="btn btn-label-primary btn-prev" onclick="prevStep(<?= $index ?>)">
                      <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                      <span class="align-middle d-sm-inline-block d-none">Previous</span>
                    </button>
                    <button class="btn btn-primary btn-submit" type="submit">Submit</button>
                  </div>
                <?php else : ?>
                  <div class="col-12 d-flex justify-content-between">
                    <button type="button" class="btn btn-label-primary btn-prev" onclick="prevStep(<?= $index ?>)">
                      <i class="bx bx-chevron-left bx-sm ms-sm-n2"></i>
                      <span class="align-middle d-sm-inline-block d-none">Previous</span>
                    </button>
                    <button type="button" class="btn btn-primary btn-next" onclick="nextStep(<?= $index ?>)">
                      <span class="align-middle d-sm-inline-block d-none me-sm-1">Next</span>
                      <i class="bx bx-chevron-right bx-sm me-sm-n2"></i>
                    </button>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); ?>

<?php include('../../layouts/layout_report1.php'); ?>
