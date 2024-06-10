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
$stmt = $dbh->prepare("SELECT headline, data FROM tblreport_step1 WHERE request_id = :id");
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

  <form id="wizard-validation-form" method="post" action="../../controllers/save_report.php">
    <input type="hidden" name="login_type" value="edit_report2">
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
            ១៥.ការតាមដានការអនុវត្តអនុសាសន៍
          </button>
        </h2>
        <div id="collapse-new" class="accordion-collapse collapse" aria-labelledby="heading-new" data-bs-parent="#reportAccordion">
          <div class="accordion-body">
            <div class="row g-3 mb-4">
              <div class="col-sm-12">
                <input type="text" name="updatedHeadlines[]" value="១៥.ការតាមដានការអនុវត្តអនុសាសន៍" class="form-control mef2 mb-3">
                <div id="editor-new" class="quill-editor"></div>
                <textarea class="form-control d-none" id="hiddenEditorContent-new" rows="10" name="updatedData[]">ក្នុងគោលបំណងធ្វើឱ្យប្រសើរឡើងនូវប្រព័ន្ធត្រួតពិនិត្យផ្ទៃក្នុងរបស់ ន.គ.ស. សវនករទទួលបន្ទុកបានផ្តល់អនុសាសន៍សវនកម្មមួយចំនួនក្នុងរបាយការណ៍សវនកម្មអនុលោមភាពនេះ។ អង្គភាពសវនកម្មផ្ទៃក្នុងនៃ អ.ស.ហ. នឹងធ្វើការតាមដានលើវឌ្ឍនភាពនៃការអនុវត្តតាមអនុសាសន៍ដែលបានផ្តល់ជូន ន.គ.ស. ខាងលើនៅគ្រាបន្ទាប់។</textarea>
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
include('../../layouts/layout_report2.php');
?>
