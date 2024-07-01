<?php
session_start();
include('../../config/dbconn.php');

// Redirect if user is not logged in
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

// Include translation functionality
include('../../includes/translate.php');

$pageTitle = "ឯកសារចូលអង្គភាពសវនកម្មផ្ទៃក្នុង";
$sidebar = "inoutdocument";
$userId = $_SESSION['userid'];
date_default_timezone_set('Asia/Bangkok');
$date = date('Y-m-d H:i:s');
// Handle form submission for adding new document
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    
    $data = [
        ':userid' => $userId,
        ':code' => $_POST['code'],
        ':type' => $_POST['type'],
        ':echonomic' => $_POST['echonomic'],
        ':give' => $_POST['give'],
        ':recrived' => $_POST['recrived'],
        ':file_name' => $_FILES['files']['name'],
        ':date' => $date,
    ];

    // Destination path for uploaded file
    $file_tmp = $_FILES['files']['tmp_name'];
    $destination = "../../uploads/file/in-doc/" . $data[':file_name'];

    // Upload file and insert data into database
    if (move_uploaded_file($file_tmp, $destination)) {
        // Database insertion query
        $sql = "INSERT INTO indocument (CodeId, Type, DepartmentName, NameOfgive, NameOFReceive, Typedocument, Date, user_id)
                VALUES (:code, :type, :echonomic, :give, :recrived, :file_name, :date, :userid)";
        $query = $dbh->prepare($sql);

        try {
            $query->execute($data);
            $msg = $query->rowCount() ? "Successfully submitted!" : "Error inserting data into the database.";
            // Redirect with success message
            header("Location: iniau.php?msg=" . urlencode($msg) . "&status=success");
            exit();
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
            // Redirect with error message
            header("Location: iniau.php?msg=" . urlencode($error) . "&status=error");
            exit();
        }
    } else {
        $error = "Error uploading file.";
        // Redirect with error message
        header("Location: iniau.php?msg=" . urlencode($error) . "&status=error");
        exit();
    }
}
// Handle document deletion
if (isset($_GET['delete'])) {
  $sql = "UPDATE indocument SET isdelete = 1 WHERE ID = :documentID";
  $query = $dbh->prepare($sql);
  $query->bindParam(':documentID', $_GET['delete'], PDO::PARAM_INT);
  $query->execute();

  if ($query->rowCount()) {
    $msg = "Document deleted successfully!";
    // Redirect with success message
    header("Location: iniau.php?msg=" . urlencode($msg) . "&status=success");
    exit();
  } else {
    $error = "Failed to delete document.";
    // Redirect with error message
    header("Location: iniau.php?msg=" . urlencode($error) . "&status=error");
    exit();
  }
}

// Construct base SQL query for fetching documents
$sql = "SELECT * FROM indocument 
        JOIN tbluser ON indocument.user_id = tbluser.id 
        WHERE tbluser.id = :userid 
        AND indocument.isdelete = 0";

$params = [':userid' => $userId];

// Handle search functionality
if (isset($_GET['search'])) {
  $searchKeyword = '%' . $_GET['search'] . '%';
  $sql .= " AND (CodeId LIKE :searchKeyword OR Type LIKE :searchKeyword OR DepartmentName LIKE :searchKeyword)";
  $params[':searchKeyword'] = $searchKeyword;
}

// Handle form filters and date range
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fromDate'], $_POST['toDate'])) {
  $fromDate = date('Y-m-d 00:00:00', strtotime($_POST['fromDate']));
  $toDate = date('Y-m-d 23:59:59', strtotime($_POST['toDate']));
  $sql .= " AND indocument.Date BETWEEN :fromDate AND :toDate";
  $params[':fromDate'] = $fromDate;
  $params[':toDate'] = $toDate;
}

// Finalize SQL query with ORDER BY
$sql .= " ORDER BY indocument.id DESC";

// Prepare and execute the SQL query
$query = $dbh->prepare($sql);
$query->execute($params);

// Fetch all results into $searchResults
$searchResults = $query->fetchAll(PDO::FETCH_ASSOC);

// Example usage of $searchResults
ob_start();
?>


<div class="row">
  <div class="col-md-12">
    <div class="container-xl flex-grow-1">
      <div class="d-flex align-items-center justify-content-between">
        <div class="card-header">
          <h4 class="py-3 mb-1 text-primary"><span class="text-muted fw-light ">អង្គភាពសវនកម្មផ្ទៃក្នុង/</span>ឯកសារចូល</h4>
        </div>
        <div class="dt-action-buttons pt-md-0">
          <div class="dt-buttons btn-group flex-wrap pe-3">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal"><span>បញ្ជូលឯកសារចូល</span></button>
          </div>
          <div class="row row-bordered g-0">
            <div class="modal animate__animated animate__bounceIn" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                  <div class="modal-header">
                    <h1 class="modal-title fs-5 mef2" id="exampleModalLabel">ការដាក់បញ្ជូលឯកសារចូល</h1>
                  </div>
                  <div class="modal-body">
                    <form method="POST" class="row g-2 needs-validation" name="example" enctype="multipart/form-data" novalidate>
                      <div class="row">
                        <div class="mb-3 col-md-6">
                          <label for="code" class="form-label">លេខឯកសារ</label>
                          <div class="input-group input-group-merge">
                            <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bx-book'></i></span>
                            <input class="form-control" type="text" id="code" name="code" autocomplete="on" placeholder="បំពេញលេខឯកសារ..." onBlur="checkAvailabilityCodeId()" required>
                          </div>
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="type" class="form-label">កម្មវត្តុ</label>
                          <div class="input-group input-group-merge">
                            <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bx-detail'></i></span>
                            <input class="form-control" type="text" name="type" autocomplete="off" id="type" placeholder="បំពេញកម្មវត្តុ..." required>
                          </div>
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="echonomic" class="form-label">មកពីស្ថាប័នឬក្រសួង</label>
                          <div class="input-group input-group-merge">
                            <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bxs-business'></i></span>
                            <input class="form-control" type="text" id="name" name="echonomic" placeholder="បំពេញឈ្មោះស្ថាប័នឬក្រសួង..." required>
                          </div>
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="give" class="form-label">ឈ្មោះអ្នក​ប្រគល់</label>
                          <div class="input-group input-group-merge">
                            <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bx-user'></i></span>
                            <input type="text" class="form-control" id="give" name="give" placeholder="បំពេញឈ្មោះអ្នក​ប្រគល់..." required>
                          </div>
                        </div>
                        <div class="mb-3 col-md-6">
                          <label for="recrived" class="form-label">ឈ្មោះអ្នកទទួល</label>
                          <div class="input-group input-group-merge">
                            <span id="basic-icon-default-company2" class="input-group-text"><i class='bx bx-user'></i></span>
                            <select name="recrived" id="recrived" class="form-select form-control" required>
                              <option value="">ជ្រើសរើស...</option>
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
                          <label for="document" class="form-label">ប្រភេទឯកសារចូល</label>
                          <input type="file" class="form-control" id="files" accept=".xlsx,.pdf,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" name="files" required>
                        </div>
                      </div>
                      <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary mb-0" data-bs-dismiss="modal">បដិសេធ</button>
                        <button type="submit" name="submit" class="btn btn-primary mb-0">រក្សាទុក</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-body">
          <div class="row">
            <!-- Input field for search -->
            <div class="col-md-4 mb-1">
              <input type="text" id="search" onkeyup="filterTable()" placeholder="ស្វែងរក..." class="form-control">
            </div>
            <!-- Form for date range -->
            <div class="col-md-4 mb-1">
              <form action="" method="post" class="d-flex" id="filterForm">
                <div class="form-group me-1">
                  <input type="text" id="fromDate" name="fromDate" class="form-control" placeholder="ចាប់ពីថ្ងៃខែឆ្នាំ" value="<?php echo isset($_POST['fromDate']) ? htmlspecialchars($_POST['fromDate']) : ''; ?>">
                </div>
                <div class="form-group me-1">
                  <input type="text" id="toDate" name="toDate" class="form-control" placeholder="ដល់ថ្ងៃទីខែឆ្នាំ" value="<?php echo isset($_POST['toDate']) ? htmlspecialchars($_POST['toDate']) : ''; ?>">
                </div>
                <div class="form-group me-1">
                  <button type="submit" class="btn btn-icon btn-secondary"><i class='bx bx-search'></i></button>
                </div>
                <!-- <div class="form-group me-1 fw-medium">
                  <button type="button" class="btn btn-icon btn-label-danger btn-reset" id="resetButton">
                    <span><i class='bx bx-refresh'></i></span>
                  </button>
                </div> -->
              </form>
            </div>

            <!-- Export button -->
            <?php
            // Define $fromDate and $toDate variables here
            $fromDate = isset($_POST['fromDate']) ? $_POST['fromDate'] : ''; // Example, replace with your actual value
            $toDate = isset($_POST['toDate']) ? $_POST['toDate'] : ''; // Example, replace with your actual value
            ?>
            <div class="col-md-4 mb-1 text-end">
              <form method="POST" action="export_script.php" id="filterOutdocument">
                <input type="hidden" name="documentType" value="outdocument">
                <input type="hidden" name="fromDate" value="<?php echo $fromDate; ?>">
                <input type="hidden" name="toDate" value="<?php echo $toDate; ?>">
                <button id="exportButton" type="sumbit" class="btn btn-primary">
                  <span class="text-white"><i class="bx bx-export me-1"></i>Export</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- show datatable -->
      <div class="col-12 col-lg-12 order-2 order-md-3 order-lg-2 mb-4">
        <div class="card">
          <div class="card-datatable dataTable_select text-nowrap pb-2">
            <div id="DataTables_Table_3_wrapper">
              <div class="table-responsive">
                <table class="datatables-ajax dt-select-table table dataTable no-footer dt-checkboxes-select" id="example" aria-describedby="DataTables_Table_3_info" style="width: 1416px;">
                  <thead>
                    <tr>
                      <th>ល.រ</th>
                      <th>លេខឯកសារ</th>
                      <th>កម្មវត្តុ</th>
                      <th>មកពីស្ថាប័នឬក្រសួង</th>
                      <th>ឈ្មោះអ្នក​ប្រគល់</th>
                      <th>ប្រភេទឯកសារចូល/ចំណារ</th>
                      <th>កាលបរិច្ឆេទ</th>
                      <th>សកម្មភាព</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (!empty($searchResults)) {
                      $cnt = 1;
                      foreach ($searchResults as $row) {
                    ?>
                        <tr>
                          <td class="text-sm font-weight-bold text-center mb-0"><b><?php echo htmlentities($cnt); ?></b></td>
                          <td>
                            <div class=" d-inline-block text-truncate" style="max-width:180px;"><?php echo htmlentities($row['CodeId']); ?>
                          </td>
                          <td>
                            <div class="d-inline-block text-truncate" style="max-width:180px;"><?php echo htmlentities($row['Type']); ?></div>
                          </td>
                          <td><?php echo htmlentities($row['DepartmentName']); ?></td>
                          <td><?php echo htmlentities($row['NameOfgive']); ?></td>
                          <td><a class="btn-link link-primary" href="send.php?ID=<?php echo htmlentities($row['ID']); ?>">ពិនិត្យមើលឯកសារ</a></td>
                          <td><?php echo htmlentities($row['Date']); ?></td>
                          <td>
                            <div class="d-flex align-items-center justify-content-center">
                              <a href="showiniau.php?ID=<?php echo htmlentities($row['ID']); ?>">
                                <i class='bx bx-show p-2'></i>
                              </a>
                              <a href="#" onclick="confirmDelete(<?php echo htmlentities($row['ID']); ?>)">
                                <i class='bx bx-trash p-2' style='color:#fd0606'></i>
                              </a>
                            </div>
                          </td>
                        </tr>
                    <?php
                        $cnt++;
                      }
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Modal delete -->
<div id="deleteConfirmationModal" class="modal animate__animated animate__bounceIn" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Modal Header -->
      <div class="modal-header">
        <h1 class="modal-title fs-3 text-center mef2" id="exampleModalLabel">លុបឯកសារ</h1>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        តើអ្នកយល់ព្រមលុបឯកសារដែរ​ ឬ​ ទេ?
      </div>
      <!-- Modal footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">មិនយល់ព្រម</button>
        <a id="confirmDeleteButton" href="#" class="btn btn-danger">យល់ព្រម</a>
      </div>
    </div>
  </div>
</div>

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
  }, 5000);

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

  const filterTable = () => {
    const filter = document.getElementById("search").value.toUpperCase();
    const tr = document.getElementById("example").getElementsByTagName("tr");
    Array.from(tr).forEach(row => {
      const td = row.getElementsByTagName("td");
      row.style.display = Array.from(td).some(cell =>
        cell.textContent.toUpperCase().includes(filter)) ? "" : "none";
    });
  };


  document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flatpickr for fromDate
    flatpickr("#fromDate", {
      enableTime: false, // Set to true if you want to include time selection
      dateFormat: "Y-m-d", // Format of the selected date (matching HTML date input format)
    });

    // Initialize Flatpickr for toDate
    flatpickr("#toDate", {
      enableTime: false, // Set to true if you want to include time selection
      dateFormat: "Y-m-d", // Format of the selected date (matching HTML date input format)
    });

    // Reset Button Event Listener
    document.getElementById("resetButton").addEventListener("click", function() {
      const form = document.getElementById("filterForm");
      const fromDateInput = document.getElementById("fromDate");
      const toDateInput = document.getElementById("toDate");

      if (form && fromDateInput && toDateInput) {
        console.log("Reset button clicked. Clearing date inputs and submitting form.");

        // Clear the date input fields
        document.getElementById("fromDate").value = "";
        document.getElementById("toDate").value = "";
        document.getElementById("filterForm").submit();
      } else {
        console.error("Form or date input elements not found.");
      }
    });
  });


  $(document).ready(function() {
    $('#example').DataTable({
      "searching": false,
      "paging": true,
      "info": true,
      "lengthChange": true
    });
  });
</script>