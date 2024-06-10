<?php
session_start();
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
    header('Location: ../../index.php');
    exit();
}

$pageTitle = "កែប្រែរបាយការណ៍";
$sidebar = "report";
ob_start(); // Start output buffering
include('../../config/dbconn.php');
include('../../controllers/form_process.php');

// Check if $_POST['erid'] is set to avoid errors
$getid = $_GET['erid'];

if ($getid) {
    $sql = "SELECT * FROM form_data WHERE id = :getid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':getid', $getid, PDO::PARAM_INT);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
}
?>
<div class="container">
    <h2>Dynamic Form Rows Addition</h2>
    <div class="card">
        <div class="card-header">Edit Report</div>
        <div class="card-body">
            <?php if (!empty($results)): ?>
            <?php foreach ($results as $result): ?>
            <form id="formValidationExamples" class="row g-3" onsubmit="submitForm()" method="POST">
                <input type="hidden" name="login_type" value="ereport">
                <input type="hidden" name="erid" value="<?php echo $getid; ?>">
                <div class="mb-3">
                    <label for="headline" class="form-label">ចំណងជើង</label>
                    <input type="text" class="form-control" id="headline" required name="headline"
                        value="<?php echo $result->headline; ?>">
                </div>
                <div class="mb-3">
                    <label for="paragraph" class="form-label">Paragraph</label>
                    <textarea class="form-control" rows="4" cols="3" id="paragraph" required name="paragraph"
                        placeholder="Textarea"><?php echo $result->paragraph; ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="reports" class="form-label">របាយការណ</label>
                    <textarea class="form-control" rows="4" cols="3" id="reports" required name="reports"
                        placeholder="Textarea"><?php echo $result->data; ?></textarea>
                </div>
                <div class="d-flex">
                    <button class="btn btn-success mt-3">Submit</button>
                </div>
            </form>
            <?php endforeach; ?>
            <?php else: ?>
            <p>No data found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>

<?php include('../../includes/layout.php'); ?>