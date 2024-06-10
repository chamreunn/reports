<?php
session_start();
// Include database connection
include('../../config/dbconn.php');

// Redirect to the index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

$pageTitle = "ទំព័រដើម";
$sidebar = "home";
ob_start(); // Start output buffering

// Fetch data from the tblreport_step1 table where ID matches $getid
$getid = $_GET['id'];

$stmt = $dbh->prepare("SELECT headline, data FROM tblreport_step3 WHERE request_id = :id");
$stmt->bindParam(':id', $getid, PDO::PARAM_INT);
$stmt->execute();
$insertedData = $stmt->fetch(PDO::FETCH_ASSOC);

// Initialize updatedHeadlines as an empty array
$updatedHeadlines = [];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login_type']) && $_POST['login_type'] == 'edit_report2') {
  $request_id = $_POST['reportid'];
  $updatedHeadlines = $_POST['updatedHeadlines'];
  $updatedQuillData = $_POST['updatedQuillData']; // Get updated Quill data from form

  // Check if the number of headlines matches the number of Quill data elements
  if (count($updatedHeadlines) !== count($updatedQuillData)) {
    // Redirect back to the edit page with an error message
    header('Location: edit3.php?id=' . $request_id . '&error=1');
    exit();
  }

  try {
    // Prepare statement to update Quill data
    $stmt = $dbh->prepare("UPDATE tblreport_step3 SET data = :data WHERE request_id = :id");

    // Loop through each headline and its corresponding Quill data
    foreach ($updatedQuillData as $index => $quillData) {
      // Bind parameters and execute the query for each record
      $stmt->bindParam(':data', $quillData);
      $stmt->bindParam(':id', $request_id);
      $stmt->execute();
    }

    // Update headlines
    $stmt = $dbh->prepare("UPDATE tblreport_step3 SET headline = :headline WHERE request_id = :id");

    foreach ($updatedHeadlines as $index => $headline) {
      $stmt->bindParam(':headline', $headline);
      $stmt->bindParam(':id', $request_id);
      $stmt->execute();
    }

    // Redirect back to the page with a success message
    header('Location: edit3.php?id=' . $request_id . '&success=1');
    exit();
  } catch (PDOException $e) {
    // Handle any database errors
    echo "Error: " . $e->getMessage();
  }
}
?>

<div class="container">
  <h2 class="khmer-font">Edit Report Details</h2>
  <form id="wizard-validation-form" method="post">
    <input type="hidden" name="login_type" value="edit_report2">
    <input type="hidden" name="reportid" value="<?php echo $getid ?>">

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
                <input type="text" class="form-control mb-3" name="updatedHeadlines[]" value="<?php echo htmlspecialchars($headline); ?>">
                <div id="editor-container-<?php echo $index; ?>"></div>
                <!-- Hidden input for storing Quill content -->
                <input type="hidden" name="updatedQuillData[]" id="hiddenQuillContent-<?php echo $index; ?>">
              </div>
            </div>
          </div>
        <?php } ?>

      <?php } else { ?>
        <p>No headlines found.</p>
      <?php } ?>
    </div>

    <div class="col-12 d-flex justify-content-between mt-3">
      <button type="submit" class="btn btn-primary">Submit</button>
      <a href="audits.php?id=<?php echo $getid; ?>" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php $content = ob_get_clean(); ?>
<?php include('../../layouts/layout_edit_report3.php'); ?>
