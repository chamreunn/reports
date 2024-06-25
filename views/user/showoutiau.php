<?php
session_start();
include('../../config/dbconn.php');
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

$id = isset($_GET['ID']) ? intval($_GET['ID']) : null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $id = $_POST['id'];
  date_default_timezone_set('Asia/Bangkok');
  $update = date('Y-m-d H:i:s');
  $uploadedFile = $_POST['current_file'];

  if (isset($_FILES['files']) && $_FILES['files']['error'] == UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['files']['tmp_name'];
    $fileName = $_FILES['files']['name'];
    $newFileName = basename($fileName);
    $dest_path = '../../uploads/file/out-doc/' . $newFileName;
    if (move_uploaded_file($fileTmpPath, $dest_path)) {
      $uploadedFile = $newFileName;
    } else {
      $error = 'Error moving the uploaded file.';
    }
  }

  if (isset($_POST['delete'])) {
    $delete = "UPDATE outdocument SET isdelete = 1 WHERE ID = :id";
    $query = $dbh->prepare($delete);
    $query->bindParam(':id', $id);
    $query->execute();

    if ($query->rowCount() > 0) {
      header("Location: outiau.php?msg=Successfully+Deleted&status=success");
      exit();
    } else {
      header("Location: outiau.php?msg=Error:+Something+went+wrong&status=failed");
      exit();
    }
  } elseif (isset($_POST['edit'])) {
    $query = $dbh->prepare("UPDATE outdocument SET
  CodeId = ?, Type = ?, FromDepartment = ?, NameOfgive = ?,
  OutDepartment = ?, NameOFReceive = ?, Typedocument = ?, `update` = ?
  WHERE ID = ?");
    $query->execute([
      $_POST['code'], $_POST['type'], $_POST['fromdepartment'], $_POST['nameofgive'],
      $_POST['outdepartment'], $_POST['nameofreceive'], $uploadedFile, $update, $id
    ]);
    if ($query->rowCount() > 0) {
      header("Location: showoutiau.php?ID=" . urlencode($id) . "&msg=Successfully+Edited&status=success");
      exit();
    } else {
      header("Location: showoutiau.php?ID=" . urlencode($id) . "&msg=Error+Edited&status=failed");
      exit();
    }
  }
}

include('../../includes/translate.php');
$pageTitle = "ឯកសារចេញអង្គភាពសវនកម្មផ្ទៃក្នុង";
$sidebar = "inoutdocument";
ob_start();
?>

<div class="row">
  <div class="col-md-12">
    <div class="container-xxl flex-grow-1">
      <div class="card mb-2">
        <h5 class="card-header text-primary">ពិនិត្យមើលឯកសារចេញ</h5>
        <hr class="my-0">
        <div class="card-body">
          <form id="formAccountSettings" method="post">
            <div class="row">
              <?php
              $sql = "SELECT * FROM outdocument WHERE ID = :id";
              $query = $dbh->prepare($sql);
              $query->bindParam(':id', $id, PDO::PARAM_INT);
              $query->execute();
              $results = $query->fetchAll(PDO::FETCH_ASSOC);
              foreach ($results as $row) {
              ?>
                <div class="mb-3 col-md-6">
                  <label for="code" class="form-label">លេខឯកសារ</label>
                  <input class="form-control" type="text" id="code" name="code" value="<?php echo htmlentities($row['CodeId']); ?>" disabled>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="type" class="form-label">កម្មវត្តុ</label>
                  <input class="form-control" type="text" id="type" name="type" value="<?php echo htmlentities($row['Type']); ?>" disabled>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="outdepartment" class="form-label">ចេញទៅស្ថាប័នឬក្រសួង</label>
                  <input class="form-control" type="text" id="outdepartment" name="outdepartment" value="<?php echo htmlentities($row['OutDepartment']); ?>" disabled>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="nameofreceive" class="form-label">ឈ្មោះអ្នកទទួល</label>
                  <input class="form-control" type="text" id="nameofreceive" name="nameofreceive" value="<?php echo htmlentities($row['NameOFReceive']); ?>" disabled>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="nameofgive" class="form-label">ឈ្មោះអ្នក​ប្រគល់</label>
                  <input class="form-control" type="text" id="nameofgive" name="nameofgive" value="<?php echo htmlentities($row['NameOfgive']); ?>" disabled>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="fromdepartment" class="form-label">មកពីនាយកដ្ឋាន</label>
                  <input class="form-control" type="text" id="fromdepartment" name="fromdepartment" value="<?php echo htmlentities($row['FromDepartment']); ?>" disabled>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="files" class="form-label">ប្រភេទឯកសារចេញ</label>
                  <div class="input-group">
                    <div class="input-group-append">
                      <?php
                      $sql2 = "SELECT Typedocument FROM outdocument WHERE ID = ?";
                      $stmt2 = $dbh->prepare($sql2);
                      $stmt2->execute([$id]);
                      while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) { ?>
                        <div class="d-flex justify-content-between p-2 rounded-3">
                          <a href="../../uploads/file/out-doc/<?php echo $row2['Typedocument']; ?>" target="blank_" class="btn-sm btn-link h6 mb-0 text-primary">
                            <?php echo $row2['Typedocument']; ?>
                          </a>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              <?php
              }
              ?>
            </div>
            <div class="col-md-12 text-end">
              <!-- Buttons for editing and deleting -->
              <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">កែសម្រួល</button>
              <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">លុប</button>
              <a href="outiau.php" class="btn btn-secondary">ត្រឡប់ក្រោយ</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal delete -->
<div class="modal animate__animated animate__bounceIn" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-3 text-center mef2" id="exampleModalLabel">លុបឯកសារ</h1>
      </div>
      <div class="modal-body">
        តើអ្នកយល់ព្រមលុបឯកសារដែរ​ ឬ​ ទេ?
      </div>
      <form id="deleteForm" method="post">
        <input type="hidden" name="id" value="<?php echo htmlentities($row['ID']); ?>">
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">មិនយល់ព្រម</button>
          <button type="submit" name="delete" class="btn btn-danger">យល់ព្រម</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Edit -->
<div class="modal animate__animated animate__bounceIn" id="editModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content ">
      <div class="modal-header">
        <h5 class="modal-title mef2" id="exampleModalLabel4">ក្រែប្រែឯកសារ</h5>
      </div>
      <div class="modal-body">
        <form id="formAccountSettings" method="post" enctype="multipart/form-data">
          <div class="row">
            <?php
            $sql = "SELECT * FROM outdocument WHERE ID = :id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as $row) {
            ?>
              <input type="hidden" name="id" value="<?php echo htmlentities($row['ID']); ?>"> <!-- Hidden input for ID -->
              <input type="hidden" name="nameofgive" value="<?php echo htmlentities($row['NameOfgive']); ?>"> <!-- Hidden input for ID -->
              <input type="hidden" name="fromdepartment" value="<?php echo htmlentities($row['FromDepartment']); ?>">
              <input type="hidden" name="current_file" value="<?php echo htmlentities($row['Typedocument']); ?>"> <!-- Hidden input for current file -->
              <div class="mb-3 col-md-6">
                <label for="code" class="form-label">លេខឯកសារ</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bx-book'></i></span>
                  <input class="form-control" type="text" id="code" name="code" value="<?php echo htmlentities($row['CodeId']); ?>">
                </div>
              </div>
              <div class="mb-3 col-md-6">
                <label for="type" class="form-label">កម្មវត្តុ</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bx-detail'></i></span>
                  <input class="form-control" type="text" id="type" name="type" value="<?php echo htmlentities($row['Type']); ?>">
                </div>
              </div>
              <div class="mb-3 col-md-6">
                <label for="outdepartment" class="form-label">ចេញទៅស្ថាប័នឬក្រសួង</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bxs-business'></i></span>
                  <input class="form-control" type="text" id="outdepartment" name="outdepartment" value="<?php echo htmlentities($row['OutDepartment']); ?>">
                </div>
              </div>
              <div class="mb-3 col-md-6">
                <label for="nameofreceive" class="form-label">ឈ្មោះអ្នកទទួល</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bx-user'></i></span>
                  <input class="form-control" type="text" id="nameofreceive" name="nameofreceive" value="<?php echo htmlentities($row['NameOFReceive']); ?>">
                </div>
              </div>
              <div class="mb-3 col-md-6">
                <label for="nameofgive" class="form-label">ឈ្មោះអ្នក​ប្រគល់</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bx-user'></i></span>
                  <select name="nameofgive" id="nameofgive" class="form-select form-control">
                    <option value="<?php echo htmlentities($row['NameOfgive']); ?>"><?php echo htmlentities($row['NameOfgive']); ?></option>
                    <?php
                    $sql = "SELECT * FROM tbluser";
                    $query = $dbh->prepare($sql);
                    $query->execute();
                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                    if ($query->rowCount() > 0) {
                      foreach ($results as $result) {
                    ?>
                        <option value="<?php echo htmlentities($result->UserName); ?>"><?php echo htmlentities($result->UserName); ?></option>
                    <?php }
                    } ?>
                  </select>
                </div>
              </div>
              <div class="mb-3 col-md-6">
                <label for="fromdepartment" class="form-label">មកពីនាយកដ្ឋាន</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bxs-business'></i></span>
                  <select class="custom-select form-control form-select rounded-2" name="fromdepartment" required>
                    <option value="<?php echo htmlentities($row['FromDepartment']); ?>"><?php echo htmlentities($row['FromDepartment']); ?></option>
                    <option value="អង្គភាពសវនកម្មផ្ទៃក្នុង">អង្គភាពសវនកម្មផ្ទៃក្នុង</option>
                    <option value="នាយកដ្ឋានកិច្ចការទូទៅ">នាយកដ្ឋានកិច្ចការទូទៅ</option>
                    <option value="នាយកដ្ឋានសវនកម្មទី១">នាយកដ្ឋានសវនកម្មទី១</option>
                    <option value="នាយកដ្ឋានសវនកម្មទី២">នាយកដ្ឋានសវនកម្មទី២</option>
                  </select>
                </div>
              </div>
              <div class="mb-3 col-md-6">
                <label for="files" class="form-label">ប្រភេទឯកសារចេញ</label>
                <div class="input-group">
                  <input type="file" class="form-control" id="files" name="files">
                  <input type="text" class="form-control" value="<?php echo htmlentities($row['Typedocument']); ?>" readonly>
                </div>
              </div>
            <?php
            }
            ?>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">បោះបង់</button>
            <button type="submit" name="edit" class="btn btn-primary">យល់ព្រម</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- HTML code for displaying completed and rejected requests -->

<?php
// Get the content from output buffer
$content = ob_get_clean();

// Include layout or template file
include('../../layouts/user_layout.php');
?>
<script>
  setTimeout(() => {
    $('.toast').fadeTo("slow", 0.1, () => {
      $('.toast').alert('close');
    });
  }, 1000);

  (() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();

  const confirmDelete = (documentID) => {
    document.getElementById("confirmDeleteButton").href = "?delete=" + documentID;
    $('#deleteConfirmationModal').modal('show');
  };
</script>