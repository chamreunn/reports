<?php
session_start();
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

require_once('../../controllers/form_process.php');
require_once('../../includes/translate.php');

$pageTitle = "ព័ត៌មានគណនី";
$sidebar = "alluser";
ob_start(); // Start output buffering

// Your PHP code for fetching data from the database
$sql = "SELECT * FROM tblrole JOIN tbluser ON tblrole.id = tbluser.RoleId";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
$cnt = 1;

?>
<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span><?php echo translate("General Affair Department"); ?></span>
            <div class=" d-flex align-items-end mt-2">
              <h4 class="mb-0 me-2">0</h4>
            </div>
            <p class="mb-0"><?php echo translate("Total"); ?></p>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-primary">
              <i class="bx bx-user bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span><?php echo translate("Audit 1 Department"); ?></span>
            <div class="d-flex align-items-end mt-2">
              <h4 class="mb-0 me-2">0</h4>
            </div>
            <p class="mb-0"><?php echo translate("Total"); ?></p>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-danger">
              <i class="bx bx-user-check bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span><?php echo translate("Audit 2 Department"); ?></span>
            <div class="d-flex align-items-end mt-2">
              <h4 class="mb-0 me-2">0</h4>
            </div>
            <p class="mb-0"><?php echo translate("Total"); ?></p>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-success">
              <i class="bx bx-group bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
          <div class="content-left">
            <span><?php echo translate("Internal Audit Unit"); ?></span>
            <div class="d-flex align-items-end mt-2">
              <h4 class="mb-0 me-2">0</h4>
            </div>
            <p class="mb-0"><?php echo translate("Total"); ?></p>
          </div>
          <div class="avatar">
            <span class="avatar-initial rounded bg-label-warning">
              <i class="bx bx-user-voice bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
// Retrieve unique role names from the database for filtering
$uniqueRoles = array_unique(array_column($results, 'RoleName'));

// Retrieve unique plan names from the database for filtering
$uniquePlans = array_unique(array_column($results, 'UserPlan'));

// Retrieve unique status values from the database for filtering
$uniqueStatuses = array_unique(array_column($results, 'UserStatus'));

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['login_type']) && $_POST['login_type'] == 'filter') {
    // Retrieve filter values from the form
    $selectedName = $_POST['user_name'];
    $selectedRole = $_POST['user_role'];

    // Generate SQL query based on the selected filters
    $sql = "SELECT * FROM tblrole JOIN tbluser ON tblrole.id = tbluser.RoleId WHERE 1=1";
    if (!empty($selectedName)) {
      $sql .= " AND CONCAT(Honorific, ' ', FirstName, ' ', LastName) LIKE '%$selectedName%'";
    }
    if (!empty($selectedRole)) {
      $sql .= " AND RoleName = '$selectedRole'";
    }

    // Execute the SQL query
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
  }
}
?>
<div class="card">
  <div class="card-header border-bottom">
    <div class="d-flex align-items-center justify-content-between mb-2">
      <h5 class="card-title">តម្រងស្វែងរក</h5>
      <!-- Button trigger modal -->
      <button class="btn btn-secondary add-new btn-primary" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-target="#editUser" data-bs-toggle="modal">
        <span><i class=" bx bx-plus me-0 me-sm-1"></i>
          <span class="d-none d-sm-inline-block">បង្កើតគណនីថ្មី</span>
        </span>
      </button>
    </div>

    <form id="filterForm" method="POST">
      <input type="hidden" name="login_type" value="filter">
      <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0">
        <div class="col-md-5 position-relative">
          <div class="input-group input-group-merge">
            <span class="input-group-text" id="basic-addon-search31"><i class="bx bx-search"></i></span>
            <input type="text" name="user_name" class="form-control" placeholder="ស្វែងរក..." value="<?php echo isset($_POST['user_name']) ? $_POST['user_name'] : ''; ?>" aria-label="ស្វែងរក..." aria-describedby="basic-addon-search31">
          </div>
        </div>
        <div class="col-md-5 user_role">
          <select id="UserRole" name="user_role" class="select2 form-select text-capitalize">
            <option value=""> ជ្រើសរើសតួនាទី </option>
            <?php foreach ($uniqueRoles as $role) : ?>
              <option value="<?php echo $role; ?>" <?php echo (isset($_POST['user_role']) && $_POST['user_role'] == $role) ? 'selected' : ''; ?>><?php echo $role; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-xl-2 col-lg-2 col-md-12 col-sm-12 text-end mt-2">
          <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter me-2"></i>អនុវត្តតម្រង</button>
        </div>
      </div>
    </form>
  </div>

  <?php if (!empty($results)) : ?>
    <div class="card-datatable table-responsive">
      <table id="notificationsTable" class="dt-responsive table border-top table-striped dataTable no-footer dtr-inline collapsed" aria-describedby="notificationsTable_info">
        <thead class='thead-dark'>
          <tr>
            <th scope='col'>ID</th>
            <th scope='col'>ឈ្មោះនាយកដ្ឋាន</th>
            <th scope='col'>តួនាទី</th>
            <th scope='col'>តួនាទី</th>
            <th scope='col'>បង្កើតនៅ</th>
            <th scope='col'>កែប្រែនៅ</th>
            <th scope='col'>ស្ថានភាព</th>
            <th scope='col' class='text-end action'>សកម្មភាព</th>
          </tr>
        </thead>
        <tbody class='table-border-bottom-0'>
          <?php foreach ($results as $result) : ?>
            <tr>
              <td><?php echo $result->id; ?></td>
              <td class="sorting_1">
                <div class="d-flex justify-content-start align-items-center user-name">
                  <div class="avatar-wrapper">
                    <div class="avatar avatar-sm me-3">
                      <?php if (!empty($result->Profile)) : ?>
                        <img src="<?php echo htmlentities($result->Profile); ?>" alt="រូបភាពអក្សរ" class="rounded-circle" style="object-fit: cover;" />
                      <?php else : ?>
                        <span class="avatar-initial rounded-circle bg-label-success"><?php echo substr($result->UserName, 0, 2); ?></span>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="d-flex flex-column">
                    <a href="all-users-detail.php?uid=<?php echo htmlentities($result->id) ?>" class="text-body text-truncate">
                      <span class="fw-medium"><?php echo $result->Honorific . " " . $result->FirstName . " " . $result->LastName ?></span>
                    </a>
                    <small class="text-muted"><?php echo $result->Email ?></small>
                  </div>
                </div>
              </td>
              <td>
                <span class="fw-medium head-of-department badge <?php echo $result->Colors ?>"><?php echo $result->RoleName ?></span>
              </td>
              <td><?php echo $result->Position; ?></td>
              <td><?php echo $result->CreationDate; ?></td>
              <td><?php echo $result->UpdateAt; ?></td>
              <td><?php echo $result->Status; ?></td>
              <td class='text-end'>
                <div>
                  <button class="btn p-0" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-html="true" title="លម្អិត">
                    <a href="all-users-detail.php?uid=<?php echo htmlentities($result->id) ?>">
                      <i class="bx bx-show-alt"></i>
                    </a>
                  </button>
                  <button class="btn p-0" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-html="true" title="កែប្រែ">
                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#editUser">
                      <i class="bx bx-edit-alt"></i>
                    </a>
                  </button>
                  <button class="btn p-0" data-bs-toggle="tooltip" data-bs-offset="0,8" data-bs-placement="top" data-bs-html="true" title="លុប">
                    <a href="javascript:;" class="text-danger" data-bs-toggle="modal" onclick="openDeleteModal(<?php echo $result->id; ?>)" data-bs-target="#deleteModal" >
                      <i class="bx bx-trash-alt"></i>
                    </a>
                  </button>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else : ?>
    <div class="text-center">
      <img src="../../assets/img/illustrations/empty-box.png" class="avatar avatar-xl mt-4" alt="">
      <h6 class="mt-4">រកមិនឃើញកំណត់ត្រាត្រូវគ្នាទេ!</h6>
    </div>
  <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal animate__animated animate__bounceIn" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete this record?
      </div>
      <div class="modal-footer">
        <form id="deleteForm" method="POST" action="../../controllers/delete_record.php">
          <input type="hidden" name="deleteid" id="deleteid">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Your existing code for the table -->
<script>
  function openDeleteModal(id) {
    document.getElementById('deleteid').value = id;
    $('#deleteModal').modal('show');
  }

  // Auto dismiss toast alerts
  $(document).ready(function() {
    setTimeout(function() {
      $('.toast').toast('hide');
    }, 5000); // Adjust timing as needed

    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href.split("?")[0]);
    }
  });
</script>
<!-- add User Modal -->
<div class="modal fade" id="editUser" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-simple modal-edit-user">
    <div class="modal-content p-3 p-md-5">
      <div class="modal-body">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        <div class="text-center mb-4">
          <h3 class="mef2">បង្កើតគណនីថ្មី</h3>
        </div>
        <form id="formAuthentication" class="row g-3 mb-3" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="login_type" value="adduser">
          <div class="d-flex flex-column align-items-center align-items-sm-center gap-4">
            <img src="../../assets/img/avatars/no-image.jpg" alt="user-avatar" class="d-block rounded" height="150" width="150" id="uploadedAvatar" style="object-fit: cover;" />
            <div class="button-wrapper">
              <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                <span class="d-none d-sm-block"><i class="bx bx-photo-album"></i>ប្តូររូបភាព</span>
                <i class="bx bx-upload d-block d-sm-none"></i>
                <input type="file" id="upload" name="profile" class="account-file-input" hidden accept="image/png, image/jpeg" />
              </label>
            </div>
          </div>

          <div class="col-12 col-md-2">
            <label for="honorific" class="form-label">គោរមងារ
              <span class="text-danger fw-bolder">*</span>
            </label>
            <select id="honorific" class="select2 form-select form-select-lg select2-hidden-accessible" data-allow-clear="false" tabindex="-1" aria-hidden="true" name="honorific">
              <option select="">ជ្រើសរើស</option>
              <option value="ឯកឧត្តម" data-select2-id="2">ឯកឧត្តម</option>
              <option value="លោកជំទាវ" data-select2-id="3">លោកជំទាវ</option>
              <option value="លោក" data-select2-id="4">លោក</option>
              <option value="លោកស្រី" data-select2-id="2">លោកស្រី</option>
              <option value="អ្នកនាង" data-select2-id="3">អ្នកនាង</option>
              <option value="កញ្ញា" data-select2-id="4">កញ្ញា</option>
            </select>
          </div>

          <div class="col-12 col-md-5 fv-plugins-icon-container">
            <label class="form-label" for="firstname">គោត្តនាម
              <span class="text-danger fw-bolder">*</span>
            </label>
            <input type="text" id="firstname" name="firstname" class="form-control" placeholder="គោត្តនាម" autofocus required>
            <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
            </div>
          </div>

          <div class="col-12 col-md-5 fv-plugins-icon-container">
            <label class="form-label" for="lastname">នាម
              <span class="text-danger fw-bolder">*</span>
            </label>
            <input type="text" id="lastname" name="lastname" class="form-control" placeholder="នាម">
          </div>

          <div class="col-md-12 col-lg-6">
            <label class="form-label">ភេទ
              <span class="text-danger fw-bolder">*</span>
            </label>
            <div class="d-flex">
              <div class="btn-group w-100" role="group" aria-label="Basic radio toggle button group">
                <input type="radio" value="ស្រី" class="btn-check" name="gender" id="gender1">
                <label class="btn btn-outline-primary" for="gender1">ស្រី
                </label>
                <input type="radio" value="ប្រុស" class="btn-check" name="gender" id="gender2">
                <label class="btn btn-outline-primary" for="gender2">ប្រុស
                </label>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="contact">លេខទូរស័ព្ទ
              <span class="text-danger fw-bolder">*</span>
            </label>
            <div class="input-group input-group-merge">
              <span class="input-group-text">+855</span>
              <input type="phonenumber" id="contact" name="contact" class="form-control phone-number-mask" placeholder="098 765 4321" required>
            </div>
          </div>


          <div class="col-12 col-md-12 fv-plugins-icon-container">
            <label class="form-label" for="username">ឈ្មោះមន្ត្រី
              <span class="text-danger fw-bolder">*</span>
            </label>
            <div class="alert alert-warning alert-dismissible" role="alert">
              <h6 class="text-warning fw-bolder">ចំណាំៈ</h6>
              <p class="mb-0">ឈ្មោះមន្ត្រីប្រើសម្រាប់ធ្វើការ Login
                ចូលប្រើប្រាស់ប្រព័ន្ធ។
              </p>
            </div>
            <input type="text" id="username" name="username" class="form-control" placeholder="ឈ្មោះមន្ត្រី">
            <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
            </div>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="email">Email
              <span class="text-danger fw-bolder">*</span>
            </label>
            <input type="text" id="email" name="email" class="form-control" required placeholder="example@gmail.com">
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="password">ពាក្យសម្ងាត់
              <span class="text-danger fw-bolder">*</span>
            </label>
            <input type="password" id="password" name="password" class="form-control" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="status">ស្ថានភាពគណនី
              <span class="text-danger fw-bolder">*</span>
            </label>
            <select id="status" name="status" class="select2 orm-select" aria-label="ជ្រើសរើស" required>
              <option selected="">ជ្រើសរើស</option>
              <option value="1">សកម្ម</option>
              <option value="0">អសកម្ម</option>
            </select>
          </div>


          <div class="col-12 col-md-6">
            <label class="form-label" for="dob">ថ្ងៃខែឆ្នាំកំណើត
              <span class="text-danger fw-bolder">*</span></label>
            <input type="text" id="formValidationDob" name="dob" class="form-control phone-number-mask" placeholder="ថ្ងៃខែឆ្នាំកំណើត" required>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="department">នាយកដ្ឋាន
              <span class="text-danger fw-bolder">*</span>
            </label>
            <select id="department" name="department" class="select2 orm-select" aria-label="Default select example">
              <option selected="">ជ្រើសរើស</option>
              <?php
              $sql = "SELECT * FROM tbldepartments";
              $query = $dbh->prepare($sql);
              $query->execute();
              $results = $query->fetchAll(PDO::FETCH_OBJ);
              $cnt = 1;
              if ($query->rowCount() > 0) {
                foreach ($results as $result) { ?>
                  <option value="<?php echo htmlentities($result->id) ?>">
                    <?php echo htmlentities($result->DepartmentName) ?>
                  </option>
              <?php }
              } ?>
            </select>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="office">ការិយាល័យ
              <span class="text-danger fw-bolder">*</span>
            </label>
            <select id="office" name="office" class="select2 orm-select" aria-label="Default select example" required>
              <option selected="">ជ្រើសរើស</option>
              <?php
              $sql = "SELECT * FROM tbloffices";
              $query = $dbh->prepare($sql);
              $query->execute();
              $results = $query->fetchAll(PDO::FETCH_OBJ);
              $cnt = 1;
              if ($query->rowCount() > 0) {
                foreach ($results as $result) { ?>
                  <option value="<?php echo htmlentities($result->id) ?>">
                    <?php echo htmlentities($result->OfficeName) ?>
                  </option>
              <?php }
              } ?>
            </select>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="role">Role
              <span class="text-danger fw-bolder">*</span>
            </label>
            <select id="role" name="role" class="select2 orm-select" aria-label="Default select example" required>
              <option selected="">Select</option>
              <?php
              $sql = "SELECT * FROM tblrole";
              $query = $dbh->prepare($sql);
              $query->execute();
              $results = $query->fetchAll(PDO::FETCH_OBJ);
              if ($query->rowCount() > 0) {
                foreach ($results as $result) { ?>
                  <option value="<?php echo htmlentities($result->id) ?>">
                    <?php echo htmlentities($result->RoleName) ?>
                  </option>
              <?php }
              } ?>
            </select>
          </div>

          <div class="col-12 col-md-6">
            <label class="form-label" for="permissionid">Permission
              <span class="text-danger fw-bolder">*</span>
            </label>
            <select id="permissionid" name="permissionid[]" class="select2 form-select" aria-label="Default select example" multiple required>
              <?php
              $sql = "SELECT * FROM tblpermission";
              $query = $dbh->prepare($sql);
              $query->execute();
              $results = $query->fetchAll(PDO::FETCH_OBJ);
              if ($query->rowCount() > 0) {
                foreach ($results as $result) {
                  echo '<option value="' . htmlentities($result->id) . '">' . htmlentities($result->PermissionName) . '</option>';
                }
              }
              ?>
            </select>
          </div>


          <div class="col-12">
            <label class="form-label" for="address">អាសយដ្ឋានបច្ចុប្បន្ន</label>
            <div class="position-relative">
              <textarea name="address" class="form-control" id="address" rows="4" placeholder="អាសយដ្ឋានបច្ចុប្បន្ន"></textarea>
            </div>
          </div>

          <div class="col-12 text-center">
            <button type="submit" id="submitForm" class="btn btn-primary me-sm-3 me-1">រក្សាទុក</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">មិនទាន់</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
  document.getElementById('honorific').addEventListener('change', function() {
    var selectedHonorific = this.value;
    var genderRadio = document.getElementsByName('gender');

    if (selectedHonorific === 'ឯកឧត្តម' || selectedHonorific === 'លោក') {
      genderRadio[1].checked = true; // Set gender to ប្រុស
    } else {
      genderRadio[0].checked = true; // Set gender to ស្រី
    }
  });
</script>
<?php $content = ob_get_clean(); ?>

<?php include('../../layouts/superadmin_layout.php'); ?>
