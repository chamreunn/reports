<?php
session_start();
include('../../config/dbconn.php');

if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

include('../../includes/translate.php');

$pageTitle = "សំណើបង្កើតរបាយការណ៍";
$sidebar = "audits";
$getid = isset($_GET['request_id']) ? $_GET['request_id'] : '';
$shortname = isset($_GET['shortname']) ? $_GET['shortname'] : '';
$regulator = isset($_GET['rep']) ? $_GET['rep'] : '';

// Fetch data from the tblrequest table where ID matches $getid
$stmt = $dbh->prepare("SELECT request_name_1, description_1 FROM tblrequest WHERE id = :id");
$stmt->bindParam(':id', $getid, PDO::PARAM_INT);
$stmt->execute();
$reportData = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch files from the tblrequest_attachments table
$stmt = $dbh->prepare("SELECT file_path FROM tblrequest_attachments WHERE request_id = :id");
$stmt->bindParam(':id', $getid, PDO::PARAM_INT);
$stmt->execute();
$oldFiles = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Process form data
  $report_id = $_POST['report_id'];
  $userid = $_POST['userid'];
  $request_name = $_POST['request_name'];
  $description = $_POST['description'];

  // Update tblrequest with request_name_2 and description_2
  $stmt = $dbh->prepare("UPDATE tblrequest SET request_name_1 = :request_name, description_2 = :description, status = 'pending', step ='2' WHERE id = :report_id");
  $stmt->bindParam(':request_name', $request_name, PDO::PARAM_STR);
  $stmt->bindParam(':description', $description, PDO::PARAM_STR);
  $stmt->bindParam(':report_id', $report_id, PDO::PARAM_INT);
  $stmt->execute();

  // Handle file uploads
  $old_files = explode(',', $_POST['old_files']);
  $uploaded_files = $_FILES['files'];

  foreach ($uploaded_files['name'] as $key => $file_name) {
    $file_tmp = $uploaded_files['tmp_name'][$key];
    $file_path = '../../uploads/' . $file_name;

    if (!in_array($file_name, $old_files)) {
      move_uploaded_file($file_tmp, $file_path);
      $stmt = $dbh->prepare("INSERT INTO tblrequest_attachments (request_id, file_path) VALUES (:userid, :file_path)");
      $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
      $stmt->bindParam(':file_path', $file_path, PDO::PARAM_STR);
      $stmt->execute();
    }
  }

  // Insert into notifications table
  $notification_msg = "A report has been requested for '{$request_name}'";
  $stmt = $dbh->prepare("INSERT INTO notifications (user_id, message, request_id) VALUES (:userid, :message, :userid)");
  $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
  $stmt->bindParam(':message', $notification_msg, PDO::PARAM_STR);
  $stmt->execute();

  // Insert into tblactivity table
  $activity_msg = "Report requested: '{$request_name}'";
  $stmt = $dbh->prepare("INSERT INTO tblactivity (UserId, ActivityName) VALUES (:userid, :activity_msg)");
  $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
  $stmt->bindParam(':activity_msg', $activity_msg, PDO::PARAM_STR);
  $stmt->execute();

  sleep(1);
  $msg = urlencode(translate("The Report Have been Requested"));
  header("Location: audits.php?status=success&msg=" . $msg);
}


ob_start();
?>
<div class="row">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="javascript:history.back()">Back</a></li>
      <li class="breadcrumb-item active" aria-current="page"><?php echo $pageTitle; ?></li>
    </ol>
  </nav>

  <div class="col-md-12">
    <div class="card">
      <div class="card-header border-bottom mb-3">
        <h5 class="card-title mb-0">Edit Report Request</h5>
      </div>
      <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
          <input type="hidden" name="login_type" value="edit_report">
          <input type="hidden" name="report_id" value="<?php echo $getid; ?>">
          <input type="hidden" name="userid" value="<?php echo $_SESSION['userid']; ?>">
          <input type="hidden" name="shortname" value="<?php echo htmlentities($shortname); ?>">
          <input type="hidden" name="regulator" value="<?php echo htmlentities($regulator); ?>">
          <input type="hidden" name="old_files" value="<?php echo htmlspecialchars(implode(',', $oldFiles)); ?>" id="old_files_input">

          <div class="mb-3">
            <label for="requestName" class="form-label">Request Name: <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="requestName" name="request_name" value="សេចក្តីព្រាងបឋមរបាយការណ៍សវនកម្ម" required>
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description: <span class="text-danger">*</span></label>
            <textarea class="form-control" id="description" name="description" placeholder="Please fill out" rows="3" required></textarea>
          </div>

          <div class="mb-3">
            <label for="fileInput" class="form-label">Upload Files:</label>
            <input type="file" class="form-control" id="fileInput" name="files[]" multiple accept=".pdf,.doc,.docx">
            <small id="fileHelp" class="form-text text-muted">You can select files one by one, and they will be added to the list.</small>
            <div id="fileList" class="mt-2">
              <?php
              // Display already uploaded files
              foreach ($oldFiles as $file) {
                if ($file) {
                  echo "
                  <div class='file-item d-flex justify-content-between align-items-center mb-1'>
                    <span><i class='bx bxs-file'></i> $file</span>
                    <button type='button' class='btn btn-danger btn-sm remove-file' data-file='$file'><i class='bx bx-x'></i></button>
                  </div>";
                }
              }
              ?>
            </div>
          </div>
      </div>
      <div class="card-footer border-top text-end">
        <button type="submit" class="btn btn-primary">Submit Request</button>
      </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.getElementById('fileInput').addEventListener('change', function(event) {
    var fileList = document.getElementById('fileList');
    var input = event.target;

    Array.from(input.files).forEach(function(file) {
      var listItem = document.createElement('div');
      listItem.classList.add('file-item', 'd-flex', 'justify-content-between', 'align-items-center', 'mb-1');

      var iconClass = '';
      var fileType = file.name.split('.').pop().toLowerCase();
      switch (fileType) {
        case 'pdf':
          iconClass = 'bx bxs-file-pdf';
          break;
        case 'doc':
        case 'docx':
          iconClass = 'bx bxs-file-word';
          break;
        default:
          iconClass = 'bx bxs-file';
          break;
      }

      listItem.innerHTML = `
        <span><i class="${iconClass}"></i> ${file.name}</span>
        <button type="button" class="btn btn-danger btn-sm remove-file"><i class='bx bx-x'></i></button>
      `;
      fileList.appendChild(listItem);

      listItem.querySelector('.remove-file').addEventListener('click', function() {
        listItem.remove();
        updateFileInput();
      });
    });

    updateFileInput();
  });

  document.querySelectorAll('.remove-file').forEach(function(button) {
    button.addEventListener('click', function() {
      var fileName = this.getAttribute('data-file');
      var oldFilesInput = document.getElementById('old_files_input');
      var oldFiles = oldFilesInput.value.split(',');

      oldFiles = oldFiles.filter(function(file) {
        return file !== fileName;
      });

      oldFilesInput.value = oldFiles.join(',');

      this.closest('.file-item').remove();
    });
  });

  function updateFileInput() {
    var dataTransfer = new DataTransfer();
    document.querySelectorAll('#fileList .file-item').forEach(function(item) {
      var fileName = item.querySelector('span').textContent.split(' ')[1];
      var fileInput = document.getElementById('fileInput');
      Array.from(fileInput.files).forEach(function(file) {
        if (file.name === fileName) {
          dataTransfer.items.add(file);
        }
      });
    });

    document.getElementById('fileInput').files = dataTransfer.files;
  }
</script>

<?php $content = ob_get_clean(); ?>
<?php include('../../includes/layout.php'); ?>
