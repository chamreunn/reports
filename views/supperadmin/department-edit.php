<?php
session_start();
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

$pageTitle = "ទំព័រដើម";
$sidebar = "department";
ob_start(); // Start output buffering
include ('../../controllers/form_process.php');
if (isset($_POST['delete'])) {
  $delete = "DELETE FROM tbldepartments WHERE id =:getid";
  $query = $dbh->prepare($delete);
  $query->bindParam(':getid', $getid);
  $query->execute();
  if ($query) {
    sleep(2);
    $msg = "successfull";
    echo "
            <script>
      setTimeout(function() {
          window.location.href = 'department.php';
      }, 3000); // Redirect after 5 seconds (5000 milliseconds)
    </script>";
  } else {
    sleep(2);
    $error = "something wrong";
  }
}

?>
<div class="row">
  <div class="col-md mb-4 mb-md-0">
    <div class="d-flex align-items-center justify-content-between p-0 mb-0">
      <nav aria-label="breadcrumb mb-0">
        <ol class="breadcrumb breadcrumb-style1">
          <li class="breadcrumb-item">
            <a href="department.php"><i class="bx bx-buildings"></i>គ្រប់គ្រងនាយកដ្ឋាន</a>
          </li>
          <li class="breadcrumb-item active text-primary"><i class="bx bx-edit"></i>កែប្រែនាយកដ្ឋាន</li>
        </ol>
      </nav>
    </div>
    <div class="card">
      <div class="card-body">
        <div class="card-header mb-0 p-0">
          <h4 class="mef2 text-center">កែប្រែនាយកដ្ឋាន</h4>
        </div>
        <?php
        $getid = intval($_GET['depid']);

        // SQL query to fetch department details along with honorific, first name, and last name of various roles
        $sql = "SELECT d.*,
               uh.Honorific AS HeadHonorific, uh.FirstName AS HeadFirstName, uh.LastName AS HeadLastName,
               GROUP_CONCAT(ud.Honorific) AS DepHeadHonorific, GROUP_CONCAT(ud.FirstName) AS DepHeadFirstName, GROUP_CONCAT(ud.LastName) AS DepHeadLastName,
               hu.Honorific AS UnitHeadHonorific, hu.FirstName AS UnitHeadFirstName, hu.LastName AS UnitHeadLastName,
               GROUP_CONCAT(udhu.Honorific) AS DepUnitHeadHonorific, GROUP_CONCAT(udhu.FirstName) AS DepUnitHeadFirstName, GROUP_CONCAT(udhu.LastName) AS DepUnitHeadLastName
        FROM tbldepartments AS d
        LEFT JOIN tbluser AS uh ON d.HeadOfDepartment = uh.id
        LEFT JOIN tbluser AS ud ON FIND_IN_SET(ud.id, d.DepHeadOfDepartment)
        LEFT JOIN tbluser AS hu ON d.HeadOfUnit = hu.id
        LEFT JOIN tbluser AS udhu ON FIND_IN_SET(udhu.id, d.DepHeadOfUnit)
        WHERE d.id = :getid
        GROUP BY d.id";

        $query = $dbh->prepare($sql);
        $query->bindParam(':getid', $getid, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if ($result) {
          $id = $result->id;
          $departmentname = $result->DepartmentName;
          $createdate = $result->CreationDate;
          $updateat = $result->UpdateAt;

          // Concatenate honorific, first name, and last name for HeadOfDepartment
          $headFullName = ($result->HeadHonorific ?? '') . " " . ($result->HeadFirstName ?? '') . " " . ($result->HeadLastName ?? '');

          // Extracting honorific, first name, and last name for DepHeadOfDepartment
          $depHeadHonors = explode(",", $result->DepHeadHonorific ?? '');
          $depHeadFirstNames = explode(",", $result->DepHeadFirstName ?? '');
          $depHeadLastNames = explode(",", $result->DepHeadLastName ?? '');

          // Combine information for DepHeadOfDepartment into an array of full names
          $depHeadFullNames = array();
          foreach ($depHeadHonors as $key => $depHeadHonor) {
            $depHeadFullNames[] = ($depHeadHonor ?? '') . " " . ($depHeadFirstNames[$key] ?? '') . " " . ($depHeadLastNames[$key] ?? '');
          }

          // Concatenate full names of DepHeadOfDepartment with comma separator
          $depHeadFullName = implode(", ", $depHeadFullNames);

          // Concatenate honorific, first name, and last name for HeadOfUnit
          $unitHeadFullName = ($result->UnitHeadHonorific ?? '') . " " . ($result->UnitHeadFirstName ?? '') . " " . ($result->UnitHeadLastName ?? '');

          // Extracting honorific, first name, and last name for DepHeadOfUnit
          $depUnitHeadHonors = explode(",", $result->DepUnitHeadHonorific ?? '');
          $depUnitHeadFirstNames = explode(",", $result->DepUnitHeadFirstName ?? '');
          $depUnitHeadLastNames = explode(",", $result->DepUnitHeadLastName ?? '');

          // Combine information for DepHeadOfUnit into an array of full names
          $depUnitHeadFullNames = array();
          foreach ($depUnitHeadHonors as $key => $depUnitHeadHonor) {
            $depUnitHeadFullNames[] = ($depUnitHeadHonor ?? '') . " " . ($depUnitHeadFirstNames[$key] ?? '') . " " . ($depUnitHeadLastNames[$key] ?? '');
          }

          // Concatenate full names of DepHeadOfUnit with comma separator
          $depUnitHeadFullName = implode(", ", $depUnitHeadFullNames);
        }
        ?>
        <form id="formAuthentication"  class="mb-3" method="POST">
          <input type="hidden" name="login_type" value="edepartment">
          <input type="hidden" name="edepid" value="<?php echo $getid ?>">
          <div class="mb-3">
            <label for="edepname" class="form-label">ឈ្មោះនាយកដ្ឋាន</label>
            <span class="text-danger fw-bold">*</span>
            <input type="text" class="form-control" id="edepname" name="edepname" value="<?php echo $departmentname ?>" autofocus required />
          </div>
          <div class="col-md-12 mb-4">
            <label for="headofunit" class="form-label">ប្រធានអង្គភាព</label>
            <span class="text-danger fw-bold">*</span>
            <div class="position-relative">
              <select id="headofunit" name="eheadofunit" class="select2 form-select form-select-lg select2-hidden-accessible" data-allow-clear="true" data-select2-id="headofunit" tabindex="-1" aria-hidden="true">
                <option data-tokens="ketchup mustard"><?php echo $unitHeadFullName ?></option>
                <?php
                $sql = "SELECT * FROM tbluser";
                $query = $dbh->prepare($sql);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                $cnt = 1;
                if ($query->rowCount() > 0) {
                  foreach ($results as $result) {
                    $fullname = $result->Honorific . " " . $result->FirstName . " " . $result->LastName;
                ?>
                    <option data-tokens="ketchup mustard" value="<?php echo htmlentities($result->id) ?>">
                      <?php echo $fullname ?>
                    </option>
                  <?php }
                } else { ?>
                  <option data-tokens="ketchup mustard">មិនទាន់មានឈ្មោះ</option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-md-12 mb-4">
            <label for="depheadofunit" class="form-label">អនុប្រធានអង្គភាព</label>
            <span class="text-danger fw-bold">*</span>
            <div class="position-relative">
              <select id="depheadofunit" name="edepheadofunit[]" class="select2 form-select" data-allow-clear="true" data-select2-id="depheadofunit" tabindex="-1" aria-hidden="true" multiple>
                <option selected data-tokens="ketchup mustard"><?php echo $depUnitHeadFullName ?>
                </option>
                <?php
                $sql = "SELECT * FROM tbluser";
                $query = $dbh->prepare($sql);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                $cnt = 1;
                if ($query->rowCount() > 0) {
                  foreach ($results as $result) {
                    $fullname = $result->Honorific . " " . $result->FirstName . " " . $result->LastName;
                ?>
                    <option value="<?php echo htmlentities($result->id) ?>">
                      <?php echo $fullname ?>
                    </option>
                  <?php }
                } else { ?>
                  <option data-tokens="ketchup mustard">មិនទាន់មានឈ្មោះ</option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-md-12 mb-4">
            <label for="select2Basic" class="form-label">ប្រធាន</label>
            <span class="text-danger fw-bold">*</span>
            <div class="position-relative">
              <select id="select2Basic" name="eheaddep" class="select2 form-select form-select-lg select2-hidden-accessible" data-allow-clear="true" data-select2-id="select2Basic" tabindex="-1" aria-hidden="true">
                <option data-tokens="ketchup mustard"><?php echo $headFullName ?></option>
                <?php
                $sql = "SELECT * FROM tbluser";
                $query = $dbh->prepare($sql);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                $cnt = 1;
                if ($query->rowCount() > 0) {
                  foreach ($results as $result) {
                    $fullname = $result->Honorific . " " . $result->FirstName . " " . $result->LastName;
                ?>
                    <option data-tokens="ketchup mustard" value="<?php echo htmlentities($result->id) ?>">
                      <?php echo $fullname ?>
                    </option>
                  <?php }
                } else { ?>
                  <option data-tokens="ketchup mustard">មិនទាន់មានឈ្មោះ</option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="col-md-12 mb-4">
            <label for="select2Basic1" class="form-label">អនុប្រធាន</label>
            <span class="text-danger fw-bold">*</span>
            <div class="position-relative">
              <select id="select2Basic1" name="edeheaddep" class="select2 form-select" data-allow-clear="true" data-select2-id="select2Basic1" tabindex="-1" aria-hidden="true">
                <option selected data-tokens="ketchup mustard"><?php echo $depHeadFullName ?></option>
                <?php
                $sql = "SELECT * FROM tbluser";
                $query = $dbh->prepare($sql);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                $cnt = 1;
                if ($query->rowCount() > 0) {
                  foreach ($results as $result) {
                    $fullname = $result->Honorific . " " . $result->FirstName . " " . $result->LastName;
                ?>
                    <option value="<?php echo htmlentities($result->id) ?>">
                      <?php echo $fullname ?>
                    </option>
                  <?php }
                } else { ?>
                  <option data-tokens="ketchup mustard">មិនទាន់មានឈ្មោះ</option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label for="updateat" class="form-label">ថ្ងៃខែឆ្នាំបង្កើត</label>
            <input type="text" id="updateat" class="form-control" value="<?php echo $createdate ?>" disabled />
          </div>
          <?php if ($updateat == '') { ?>
          <?php } else { ?>
            <div class="mb-3">
              <label class="form-label">ថ្ងៃខែឆ្នាំកែប្រែ</label>
              <input type="text" class="form-control" value="<?php echo $updateat ?>" disabled />
            </div>
          <?php } ?>
          <div class="col-12 fv-plugins-icon-container">
            <div class="form-check">
              <input type="checkbox" class="form-check-input" id="formValidationCheckbox" name="formValidationCheckbox">
              <label class="form-check-label" for="formValidationCheckbox">យល់ស្រប</label>
              <span class="text-danger fw-bold">*</span>
              <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
              </div>
            </div>
          </div>
          <div class="d-flex align-items-center justify-content-end mt-4">
            <button type="submit" name="save-edit" class="btn btn-primary d-flex">
              <i class="bx bx-save me-1"></i> រក្សាទុក
            </button>
            <button type="button" class="btn btn-danger mx-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
              <i class="bx bx-trash"></i> លុប
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>

</div>
<?php $content = ob_get_clean(); ?>

<?php include('../../includes/layout.php'); ?>
