<?php
session_start();
// Include database connection
include('../../config/dbconn.php');

// Redirect to the index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

$pageTitle = "កែប្រែព័ត៌មានរបាយការណ៍";
$sidebar = "home";
ob_start(); // Start output buffering

// Fetch data from the tblreport_step2 table where ID matches $getid
$getid = $_GET['id'];

$stmt = $dbh->prepare("SELECT headline, data FROM tblreport_step2 WHERE request_id = :id");
$stmt->bindParam(':id', $getid, PDO::PARAM_INT);
$stmt->execute();
$insertedData = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_type']) && $_POST['login_type'] == 'edit_report') {
  $request_id = $_POST['reportid'];
  $updatedHeadline = $_POST['updatedHeadline'];
  $updatedQuillData = $_POST['updatedQuillData']; // Get updated Quill data from form

  try {
    // Start transaction
    $dbh->beginTransaction();

    // Prepare statement to update Quill data and headline
    $stmt = $dbh->prepare("UPDATE tblreport_step2 SET headline = :headline, data = :data WHERE request_id = :id");

    // Bind parameters and execute the query for the specific record
    $stmt->bindParam(':headline', $updatedHeadline);
    $stmt->bindParam(':data', $updatedQuillData);
    $stmt->bindParam(':id', $request_id);
    $stmt->execute();

    // Commit transaction
    $dbh->commit();

    // Redirect back to the page with a success message
    header('Location: edit2.php?id=' . $request_id . '&success=1');
    exit();
  } catch (PDOException $e) {
    // Rollback transaction on failure
    $dbh->rollBack();

    // Handle any database errors
    echo "Error: " . $e->getMessage();
  }
}
?>

<div class="container">
  <h2 class="khmer-font">Edit Report Details</h2>
  <div class="accordion mb-3" id="accordionExample">
    <?php if (!empty($insertedData)) { ?>
      <?php $headlines = explode("\n", trim($insertedData['headline']));
      $data = explode("\n", trim($insertedData['data']));
      ?>
      <?php foreach ($headlines as $index => $headline) { ?>
        <div class="card accordion-item">
          <h2 class="accordion-header" id="heading<?php echo $index; ?>">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $index; ?>" aria-expanded="false" aria-controls="collapse<?php echo $index; ?>">
              <?php echo htmlspecialchars($headline); ?>
            </button>
          </h2>
          <div id="collapse<?php echo $index; ?>" class="accordion-collapse collapse" aria-labelledby="heading<?php echo $index; ?>" data-bs-parent="#accordionExample">
            <div class="accordion-body">
              <form method="post">
                <input type="hidden" name="login_type" value="edit_report">
                <input type="hidden" name="reportid" value="<?php echo $getid ?>">
                <input type="hidden" name="index" value="<?php echo $index ?>">

                <input type="text" class="form-control mb-3" name="updatedHeadline" value="<?php echo htmlspecialchars($headline); ?>">
                <div id="editor-container-<?php echo $index; ?>"></div>
                <!-- Hidden input for storing Quill content -->
                <input type="hidden" name="updatedQuillData" id="hiddenQuillContent-<?php echo $index; ?>" value="<?php echo htmlspecialchars($data[$index]); ?>">

                <div class="d-flex justify-content-end mt-3">
                  <button type="submit" class="btn btn-primary">Update</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php } ?>

    <?php } else { ?>
      <p>No headlines found.</p>
    <?php } ?>
  </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include('../../layouts/layout_edit_report2.php'); ?>

<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script>
  document.addEventListener('DOMContentLoaded', (event) => {
    <?php foreach ($headlines as $index => $headline) { ?>
      var quill<?php echo $index; ?> = new Quill('#editor-container-<?php echo $index; ?>', {
        theme: 'snow'
      });
      quill<?php echo $index; ?>.root.innerHTML = <?php echo json_encode($data[$index]); ?>;
      quill<?php echo $index; ?>.on('text-change', function() {
        document.getElementById('hiddenQuillContent-<?php echo $index; ?>').value = quill<?php echo $index; ?>.root.innerHTML;
      });
    <?php } ?>
  });
</script>
