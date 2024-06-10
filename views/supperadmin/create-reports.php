<?php
session_start();
include('../../config/dbconn.php');

// Redirect to the index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

$pageTitle = "ទំព័រដើម";
$sidebar = "report";
ob_start(); // Start output buffering
include('../../controllers/form_process.php');
?>
<style>
  @font-face {
    font-family: 'Khmer MEF2';
    src: url('../../assets/vendor/fonts/Khmer-MEF2.woff') format('woff2'),
      url('../../assets/vendor/fonts/Khmer-MEF2.woff') format('woff');
    font-weight: normal;
    font-style: normal;
  }

  body {
    font-family: 'Khmer MEF1', 'khmer mef2', Arial, sans-serif;
    /* Other body styles */
  }
</style>
<h2 class="mef2">របាយការណ៍សវនកម្ម</h2>
<!-- First card (always present) -->
<div class="card">
  <div class="card-header">Row 1</div>
  <div class="card-body">
    <form id="formValidationExamples" class="row g-3 needs-validation" method="POST">
      <input type="hidden" name="login_type" value="report">
      <input type="hidden" name="adminid" value="<?php echo $_SESSION['userid'] ?>">
      <div class="mb-3">
        <label for="headline" class="form-label">ចំណងជើង</label>
        <input type="text" class="form-control" id="headline" required autofocus name="headline" placeholder="Input">
      </div>
      <div class="mb-3">
        <label for="reports" class="form-label">របាយការណ៍</label>
        <!-- Replace textarea with Quill editor -->
        <div id="editor-container-reports" style="height: 300px;"></div>
        <textarea class="d-none" id="hiddenEditorContent-reports" name="reports"></textarea>
      </div>
      <div class="d-flex">
        <button class="btn btn-success mt-3">Submit</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
  var editorReports = new Quill('#editor-container-reports', {
    theme: 'snow',
  });

  editorReports.on('text-change', function(delta, oldDelta, source) {
    document.getElementById('hiddenEditorContent-reports').value = editorReports.root.innerHTML;
  });
</script>

<?php $content = ob_get_clean(); ?>
<?php include('../../includes/layout.php'); ?>
