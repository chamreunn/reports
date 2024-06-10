<?php
session_start();
include('../../config/dbconn.php');

// Redirect to the index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

$pageTitle = "ទំព័រដើម";
$sidebar = "home";
ob_start(); // Start output buffering
include('../../controllers/form_process.php');
$getid = $_GET['request_id'];

// Fetch data from the tblreport_step1 table where ID matches $getid
$stmt = $dbh->prepare("SELECT headline, data FROM tblreport_step2 WHERE request_id = :id");
$stmt->bindParam(':id', $getid, PDO::PARAM_INT);
$stmt->execute();
$insertedData = $stmt->fetch(PDO::FETCH_ASSOC);

// Assuming $insertedData contains the inserted data
?>
<div class="row mb-3 align-items-center">
  <div class="col-12 col-lg-6 mb-3 mb-lg-0">
    <h3 class="khmer-font">Report Details</h3>
  </div>
</div>

<form id="wizard-validation-form" method="post">
  <input type="hidden" name="login_type" value="make_report3">
  <input type="hidden" name="reportid" value="<?php echo $getid ?>">
  <div class="accordion mt-3 accordion-header-primary" id="reportAccordion">
    <?php
    // Use newline as the delimiter to split the headlines and data
    $headlines = explode("\n", trim($insertedData['headline']));
    $data = explode("\n", trim($insertedData['data']));

    foreach ($headlines as $index => $headline) {
      $headline = htmlspecialchars_decode($headline);
      $dataLine = htmlspecialchars_decode($data[$index] ?? '');
    ?>
      <div class="accordion-item card mt-1">
        <h2 class="accordion-header" id="heading<?php echo $index; ?>">
          <button class="accordion-button collapsed mef2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
            <?php echo $headline; ?>
          </button>
        </h2>
        <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#reportAccordion">
          <div class="accordion-body">
            <input type="text" class="form-control mef2 mb-3" name="updatedHeadlines[]" value="<?php echo trim(htmlspecialchars($headline)); ?>">
            <div id="editor-<?php echo $index; ?>" class="quill-editor"></div>
            <textarea class="d-none" name="updatedData[]" id="hiddenEditorContent-<?php echo $index; ?>"><?php echo $dataLine; ?></textarea>
          </div>
        </div>
      </div>
    <?php } ?>
    <!-- Additional accordion item -->
    <div class="accordion-item card mt-1">
      <h2 class="accordion-header" id="heading-new">
        <button class="accordion-button collapsed mef2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-new" aria-expanded="false" aria-controls="collapse-new">
          ១៦.បញ្ហាប្រឈម និងសំណូមពររបស់សវនករទទួលបន្ទុក
        </button>
      </h2>
      <div id="collapse-new" class="accordion-collapse collapse" aria-labelledby="heading-new" data-bs-parent="#reportAccordion">
        <div class="accordion-body">
          <div class="row g-3 mb-4">
            <div class="col-sm-12">
              <input type="text" name="updatedHeadlines[]" value="១៦.បញ្ហាប្រឈម និងសំណូមពររបស់សវនករទទួលបន្ទុក" class="form-control mef2 mb-3">
              <div id="editor-new" class="quill-editor"></div>
              <textarea class="form-control d-none" id="hiddenEditorContent-new" rows="10" name="updatedData[]">១៦.១.បញ្ហាប្រឈម
ដោយអនុលោមទៅតាមផែនការសវនកម្មប្រចាំឆ្នាំ ប្រតិភូសវនកម្ម និងសវនករទទួលបន្ទុកបានខិតខំប្រឹងប្រែងអនុវត្តការងារសវនកម្ម រហូតទទួលបានជោគជ័យលើការរៀបចំរបាយការណ៍សវនកម្មប្រចាំឆ្នាំ២០២៣ របស់ ឈ្មោះសវនដ្ឋាន។ ស្របជាមួយលទ្ធផលគួរជាទីមោទនៈនេះ ប្រតិភូសវនកម្ម និងសវនករទទួលបន្ទុក ពុំមានជួបប្រទះនូវបញ្ហាប្រឈមក្នុងការអនុវត្តការងារសវនកម្មឆ្នាំ២០២៣ នេះទេ។
១៦.២.សំណូមពរ
ប្រតិភូសវនកម្ម និងសវនករទទួលបន្ទុកពុំមានសំណូមពរបន្ថែមនោះទេ។
</textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="accordion-item card mt-1">
      <h2 class="accordion-header" id="heading-new">
        <button class="accordion-button collapsed mef2" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-fixed" aria-expanded="false" aria-controls="collapse-new">
          ឧបសម្ព័ន្ធ
        </button>
      </h2>
      <div id="collapse-fixed" class="accordion-collapse collapse" aria-labelledby="heading-new" data-bs-parent="#reportAccordion">
        <div class="accordion-body">
          <div class="row g-3 mb-4">
            <div class="col-sm-12">
              <input type="text" name="updatedHeadlines[]" value="ឧបសម្ព័ន្ធ" class="form-control mef2 mb-3">
              <div id="editor-fixed" class="quill-editor"></div>
              <textarea class="form-control d-none" id="hiddenEditorContent-new" rows="10" name="updatedData[]"></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 d-flex justify-content-between mt-3">
    <button type="submit" class="btn btn-primary btn-submit">Submit</button>
  </div>
</form>
<?php
// Output the form after the header
$content = ob_get_clean();
include('../../layouts/layout_report3.php');
?>
