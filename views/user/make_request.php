<?php
session_start();
include('../../config/dbconn.php');

if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

include('../../controllers/form_process.php');
include('../../includes/translate.php');

$userId = $_SESSION['userid'];
$sql = "SELECT d.HeadOfUnit
        FROM tbldepartments AS d
        JOIN tbluser AS u ON u.Department = d.id
        WHERE u.id = :userid";

// Prepare the query
$query = $dbh->prepare($sql);
$query->bindParam(':userid', $userId, PDO::PARAM_INT);

// Execute the query
$query->execute();

// Fetch the result
$admindepartment = $query->fetch(PDO::FETCH_ASSOC);

// Initialize $headOfUnit variable
$headOfUnit = "";

// Display the HeadOfUnit
if ($admindepartment) {
  $headOfUnit = $admindepartment['HeadOfUnit'];
} else {
  echo "No Head of Unit found for this user.";
}

$pageTitle = "សំណើបង្កើតរបាយការណ៍";
$sidebar = "audits";
$shortname = isset($_GET['shortname']) ? $_GET['shortname'] : '';
$regulator = isset($_GET['rep']) ? $_GET['rep'] : '';

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
        <h5 class="card-title mb-0">Create Report Request</h5>
      </div>
      <div class="card-body">
        <form onsubmit="onsubmitForm()" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="login_type" value="request_report1">
          <input type="hidden" name="userid" value="<?php echo $_SESSION['userid'] ?>">
          <input type="hidden" name="adminid" value="<?php echo $headOfUnit ?>">
          <input type="hidden" name="shortname" value="<?php echo htmlentities($shortname); ?>">
          <input type="hidden" name="regulator" value="<?php echo htmlentities($regulator); ?>">

          <div class="mb-3">
            <label for="requestName" class="form-label">Request Name: <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="requestName" name="request_name" value="សេចក្តីព្រាងរបាយការណ៍សវនកម្ម" required>
          </div>

          <div class="mb-3"><?php echo $headOfUnit ?>
            <label for="description" class="form-label">Description: <span class="text-danger">*</span></label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
          </div>
          <label for="requestName" class="form-label">Attactments: <span class="text-danger">*</span></label>
          <div class="mb-3 custom-dropzone" id="dropzone">
            <label for="fileInput" class="form-label dropzone">Drag and drop files here or click to upload:</label>
            <input type="file" class="form-control" id="fileInput" name="files[]" multiple accept=".pdf,.doc,.docx" style="display: none;">
            <small id="fileHelp" class="form-text text-muted">You can select files one by one, and they will be added to the list.</small>
            <div id="fileList" class="file-list mt-2"></div>
          </div>
      </div>
      <div class="card-footer border-top">
        <button type="submit" class="btn btn-primary">Submit Request</button>
      </div>
      </form>
    </div>
  </div>
</div>
<style>
  .custom-dropzone {
    border: 2px dashed #ccc;
    border-radius: 5px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.3s ease;
  }

  .custom-dropzone.dragover {
    border-color: #007bff;
  }

  .custom-dropzone:hover {
    cursor: pointer;
  }

  .file-list {
    margin-top: 10px;
  }

  .file-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-align: start;
    margin-bottom: 5px;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }

  .file-item span {
    flex: 1;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    margin-right: 10px;
  }
</style>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var dropzone = document.getElementById('dropzone');
    var fileInput = document.getElementById('fileInput');
    var fileList = document.getElementById('fileList');

    dropzone.addEventListener('dragover', function(e) {
      e.preventDefault();
      dropzone.classList.add('dragover');
    });

    dropzone.addEventListener('dragleave', function() {
      dropzone.classList.remove('dragover');
    });

    dropzone.addEventListener('drop', function(e) {
      e.preventDefault();
      dropzone.classList.remove('dragover');

      Array.from(e.dataTransfer.files).forEach(function(file) {
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

    fileInput.addEventListener('change', function() {
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
  });
</script>

<?php $content = ob_get_clean(); ?>
<?php include('../../layouts/user_layout.php'); ?>
