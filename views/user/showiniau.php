<?php
session_start();
include('../../config/dbconn.php');

// Check if user is logged in
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

// Initialize variables
$id = isset($_GET['ID']) ? intval($_GET['ID']) : null;

if (isset($_POST['delete'])) {
  $delete = "UPDATE indocument SET isdelete = 1 WHERE ID = :id";
  $query = $dbh->prepare($delete);
  $query->bindParam(':id', $id);
  $query->execute();

  if ($query->rowCount() > 0) {
      header("Location: iniau.php?msg=Successfully+Deleted&status=success");
      exit();
  } else {
      header("Location: iniau.php?msg=Error:+Something+went+wrong&status=failed");
      exit();
  }
}

if (isset($_POST['edit'])) {
  $id = $_POST['id'];

  // Correctly format the date
  date_default_timezone_set('Asia/Bangkok');
  $date = date('Y-m-d H:i:s');

  // Initialize variables for file names
  $uploadedFile = $_POST['current_file'];
  $uploadedFile1 = $_POST['current_file1'];

  // Handle the first file upload
  if (isset($_FILES['files']) && $_FILES['files']['error'] == UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['files']['tmp_name'];
    $fileName = $_FILES['files']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Sanitize file name
    $newFileName = preg_replace("/[^a-zA-Z0-9_\-\.]/", "", pathinfo($fileName, PATHINFO_FILENAME)) . '.' . $fileExtension;

    // Directory where you want to save the uploaded file
    $uploadFileDir = '../../uploads/file/in-doc/';
    $dest_path = $uploadFileDir . $newFileName;

    if (move_uploaded_file($fileTmpPath, $dest_path)) {
      $uploadedFile = $newFileName;
    } else {
      echo 'There was an error moving the uploaded file.';
    }
  }

  // Handle the second file upload
  if (isset($_FILES['files1']) && $_FILES['files1']['error'] == UPLOAD_ERR_OK) {
    $fileTmpPath1 = $_FILES['files1']['tmp_name'];
    $fileName1 = $_FILES['files1']['name'];
    $fileExtension1 = strtolower(pathinfo($fileName1, PATHINFO_EXTENSION));

    // Sanitize file name
    $newFileName1 = preg_replace("/[^a-zA-Z0-9_\-\.]/", "", pathinfo($fileName1, PATHINFO_FILENAME)) . '.' . $fileExtension1;

    // Directory where you want to save the uploaded file
    $uploadFileDir1 = '../../uploads/file/note-doc/';
    $dest_path1 = $uploadFileDir1 . $newFileName1;

    if (move_uploaded_file($fileTmpPath1, $dest_path1)) {
      $uploadedFile1 = $newFileName1;
    } else {
      echo 'There was an error moving the uploaded file.';
    }
  }

  $edit = "UPDATE indocument SET
              CodeId = ?,
              Type = ?,
              NameOfgive = ?,
              DepartmentName = ?,
              NameOFReceive = ?,
              DepartmentReceive = ?,                
              NameRecipient = ?,
              Typedocument = ?,
              document = ?,
              `update` = ?
          WHERE ID = ?";

  $query = $dbh->prepare($edit);

  // Bind parameters using positional placeholders
  $query->execute([
    $_POST['code'],
    $_POST['type'],
    $_POST['give'],
    $_POST['echonomic'],
    $_POST['recrived'],
    $_POST['department'],
    $_POST['burden'],
    $uploadedFile,  // Use the file name (new or existing) for the first file
    $uploadedFile1, // Use the file name (new or existing) for the second file
    $date,  // Use the formatted date
    $id
  ]);

  if ($query->rowCount() > 0) {
    header("Location: showiniau.php?ID=" . urlencode($id) . "&msg=Successfully+Edited&status=success");
    exit();
  } else {
    header("Location: showiniau.php?ID=" . urlencode($id) . "&msg=Error+Edited&status=failed");
    exit();
  }
}

include('../../includes/translate.php');
$pageTitle = "ឯកសារចូលអង្គភាពសវនកម្មផ្ទៃក្នុង";
$sidebar = "inoutdocument";
ob_start();
?>


<div class="row">
  <div class="col-md-12">
    <div class="container-xxl flex-grow-1">
      <div class="card mb-2">
        <h5 class="card-header text-primary">ពិនិត្យមើលឯកសារចូល/ឯកសារចំណារ</h5>
        <hr class="my-0">
        <div class="card-body">
          <form id="formAccountSettings" method="post">
            <div class="row">
              <?php
              $sql = "SELECT * FROM indocument WHERE ID = :id";
              $query = $dbh->prepare($sql);
              $query->bindParam(':id', $id, PDO::PARAM_INT);
              $query->execute();
              $results = $query->fetchAll(PDO::FETCH_ASSOC); // Changed variable name to $results
              foreach ($results as $row) { // Loop through each result                
              ?>

                <div class="mb-3 col-md-6">
                  <label for="code" class="form-label">លេខឯកសារ</label>
                  <input class="form-control" type="text" id="code" name="code" value="<?php echo htmlentities($row['CodeId']) ?>" disabled>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="type" class="form-label">កម្មវត្តុ</label>
                  <input class="form-control " type="text" id="type" name="type" value="<?php echo htmlentities($row['Type']) ?>" disabled>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="echonomic" class="form-label">មកពីស្ថាប័នឬក្រសួង</label>
                  <input class="form-control" type="text" id="echonomic" name="echonomic" value="<?php echo htmlentities($row['DepartmentName']) ?>" disabled>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="give" class="form-label">ឈ្មោះអ្នក​ប្រគល់</label>
                  <input class="form-control" type="text" id="give" name="give" value="<?php echo htmlentities($row['NameOfgive']) ?>" disabled>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="recrived" class="form-label">ឈ្មោះអ្នកទទួល</label>
                  <input class="form-control" type="text" id="recrived" name="recrived" value="<?php echo htmlentities($row['NameOFReceive']) ?>" disabled>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="files" class="form-label">ប្រភេទឯកសារចូល</label>
                  <div class="input-group ">
                    <div class="input-group-append">
                      <?php
                      $sql2 = "SELECT Typedocument FROM indocument WHERE ID = ?";
                      $stmt2 = $dbh->prepare($sql2);
                      $stmt2->execute([$id]);
                      while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) { ?>
                        <div class="d-flex  justify-content-between  p-2 rounded-3">
                          <a href="../../uploads/file/in-doc/<?php echo $row2['Typedocument']; ?>" target="blank_" class="btn-sm btn-link h6 mb-0 text-primary ">
                            <?php echo $row2['Typedocument']; ?>
                          </a>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="files" class="form-label">ប្រភេទឯកសារចំណារ</label>
                  <div class="input-group ">
                    <div class="input-group-append">
                      <?php
                      $sql2 = "SELECT document FROM indocument WHERE ID = ?";
                      $stmt2 = $dbh->prepare($sql2);
                      $stmt2->execute([$id]);
                      while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) { ?>
                        <div class="d-flex  justify-content-between  p-2 rounded-3">
                          <a href="../../uploads/file/note-doc/<?php echo $row2['document']; ?>" target="blank_" class="btn-sm btn-link h6 mb-0 text-primary ">
                            <?php echo $row2['document']; ?>
                          </a>
                        </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="department" class="form-label">នាយកដ្ឋានទទួលបន្ទុក</label>
                  <input class="form-control" type="text" id="department" name="department" value="<?php echo htmlentities($row['DepartmentReceive']) ?>" disabled>
                </div>
                <div class="mb-3 col-md-6">
                  <label for="burden" class="form-label">ឈ្មោះអ្នកទទួលបន្ទុក</label>
                  <input class="form-control" type="text" id="burden" name="burden" value="<?php echo htmlentities($row['NameRecipient']) ?>" disabled>
                </div>
              <?php
              }
              ?>
            </div>
            <div class="mt-2">
              <!-- Button trigger modal -->
              <div class="col-md-12 text-end">
                <!-- Buttons for editing and deleting -->
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal">កែសម្រួល</button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">លុប</button>
                <a href="iniau.php" class="btn btn-secondary">ត្រឡប់ក្រោយ</a>
              </div>

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
            $sql = "SELECT * FROM indocument WHERE ID = :id";
            $query = $dbh->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->execute();
            $results = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach ($results as $row) {
            ?>
              <input type="hidden" name="id" value="<?php echo htmlentities($row['ID']); ?>"> <!-- Hidden input for ID -->
              <input type="hidden" name="recrived" value="<?php echo htmlentities($row['NameOFReceive']); ?>"> <!-- Hidden input for ID -->
              <input type="hidden" name="burden" value="<?php echo htmlentities($row['NameRecipient']); ?>"> <!-- Hidden input for ID -->
              <input type="hidden" name="department" value="<?php echo htmlentities($row['DepartmentReceive']); ?>"> <!-- Hidden input for ID -->
              <input type="hidden" name="current_file" value="<?php echo htmlentities($row['Typedocument']); ?>"> <!-- Hidden input for current file -->
              <input type="hidden" name="current_file1" value="<?php echo htmlentities($row['document']); ?>"> <!-- Hidden input for current file -->
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
                <label for="echonomic" class="form-label">មកពីស្ថាប័នឬក្រសួង</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bxs-business'></i></span>
                  <input class="form-control" type="text" id="echonomic" name="echonomic" value="<?php echo htmlentities($row['DepartmentName']); ?>">
                </div>
              </div>
              <div class="mb-3 col-md-6">
                <label for="give" class="form-label">ឈ្មោះអ្នក​ប្រគល់</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bx-user'></i></span>
                  <input class="form-control" type="text" id="give" name="give" value="<?php echo htmlentities($row['NameOfgive']); ?>">
                </div>
              </div>
              <div class="mb-3 col-md-6">
                <label for="recrived" class="form-label">ឈ្មោះអ្នកទទួល</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bx-user'></i></span>
                  <select name="recrived" id="recrived" class="form-select form-control">
                    <option value="<?php echo htmlentities($row['NameOFReceive']); ?>"><?php echo htmlentities($row['NameOFReceive']); ?></option>
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
                <label for="files" class="form-label">ប្រភេទឯកសារចូល</label>
                <div class="input-group">
                  <input type="file" class="form-control" id="files" name="files">
                  <input type="text" class="form-control" value="<?php echo htmlentities($row['Typedocument']); ?>" readonly>
                </div>
              </div>
              <div class="mb-3 col-md-6">
                <label for="files" class="form-label">ប្រភេទឯកសារចំណារ</label>
                <div class="input-group">
                  <input type="file" class="form-control" id="files1" name="files1">
                  <input type="text" class="form-control" value="<?php echo htmlentities($row['document']); ?>" readonly>
                </div>
              </div>
              <div class="mb-3 col-md-6">
                <label for="department" class="form-label">នាយកដ្ឋានទទួលបន្ទុក</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bxs-business'></i></span>
                  <select class="custom-select form-control form-select rounded-2" name="department">
                    <option value="<?php echo htmlentities($row['DepartmentReceive']); ?>"><?php echo htmlentities($row['DepartmentReceive']); ?></option>
                    <option value="អង្គភាពសវនកម្មផ្ទៃក្នុង">អង្គភាពសវនកម្មផ្ទៃក្នុង</option>
                    <option value="នាយកដ្ឋានកិច្ចការទូទៅ">នាយកដ្ឋានកិច្ចការទូទៅ</option>
                    <option value="នាយកដ្ឋានសវនកម្មទី១">នាយកដ្ឋានសវនកម្មទី១</option>
                    <option value="នាយកដ្ឋានសវនកម្មទី២">នាយកដ្ឋានសវនកម្មទី២</option>
                  </select>
                </div>
              </div>
              <div class="mb-3 col-md-6">
                <label for="burden" class="form-label">ឈ្មោះអ្នកទទួលបន្ទុក</label>
                <div class="input-group input-group-merge">
                  <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bx-user'></i></span>
                  <select name="burden" id="burden" class="form-select form-control">
                    <option value="<?php echo htmlentities($row['NameRecipient']); ?>"><?php echo htmlentities($row['NameRecipient']); ?></option>
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