<?php
session_start();
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

$pageTitle = "បង្កើតនិយ័តករ";
$sidebar = "regulator";
ob_start(); // Start output buffering
include('../../config/dbconn.php');
include('../../controllers/form_process.php');
?>
<div class="col-md mb-4 mb-md-0 mt-3">
  <div class="d-flex align-items-center justify-content-between">
    <small class="text-light fw-medium">បង្កើតនិយ័តករ</small>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
      បង្កើតនិយ័តករថ្មី <i class="bx bx-plus mx-2 me-0"></i>
    </button>

    <!-- Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-md modal-simple modal-dialog-centered modal-add-new-role">
        <div class="modal-content p-3 p-md-5">
          <div class="modal-body">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="text-center mb-4">
              <h3 class="role-title mef2">បង្កើតលក្ខខណ្ឌថ្មី</h3>
            </div>
            <!-- Add role form -->
            <form id="formAuthentication" class="mb-3" method="POST">
              <input type="hidden" name="login_type" value="regulator_name">
              <div class="mb-3">
                <label for="regulatorname" class="form-label">
                  ឈ្មោះនិយ័តករ
                </label>
                <input type="text" class="form-control" id="regulatorname" name="regulatorname" placeholder="ឈ្មោះនិយ័តករ" autofocus required />
              </div>

              <div class="mb-3">
                <label for="shortname" class="form-label">
                  ឈ្មោះកាត់និយ័តករ
                </label>
                <input type="text" class="form-control" id="shortname" name="shortname" placeholder="ឈ្មោះនិយ័តករ" required />
              </div>

              <div class="col-12 fv-plugins-icon-container">
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="formValidationCheckbox" name="formValidationCheckbox">
                  <label class="form-check-label" for="formValidationCheckbox">យល់ស្រប</label>
                  <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                  </div>
                </div>
              </div>
              <div class="d-flex align-items-center justify-content-center mt-4">
                <button class="btn btn-primary d-flex" name="submit-permission">បង្កើត</button>
                <button type="button" class="btn btn-label-secondary mx-2" data-bs-dismiss="modal">បោះបង់</button>
              </div>
            </form>
            <!--/ Add role form -->
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card mt-3">
    <div class="card-datatable table-responsive">
      <?php
      $sql = "SELECT * FROM tblregulator";
      $query = $dbh->prepare($sql);
      $query->execute();
      $results = $query->fetchAll(PDO::FETCH_OBJ);
      $cnt = 1;
      if ($query->rowCount() > 0) {
        echo " <table id='notificationsTable' class='dt-responsive table border-top table-striped dataTable no-footer dtr-inline collapsed' aria-describedby='notificationsTable_info'>
                                            <thead class='thead-dark'>
                                                <tr>
                                                    <th scope='col'>ឈ្មោះនិយ័តករ</th>
                                                    <th scope='col'>ឈ្មោះកាត់</th>
                                                    <th scope='col'>បង្កើតនៅ</th>
                                                    <th scope='col' class='text-end'>សកម្មភាព</th>
                                                </tr>
                                            </thead>
                                            <tbody class='table-border-bottom-0'>";
        foreach ($results as $result) {
      ?>
          <tr>
            <td>
              <span class="fw-medium"><?php echo htmlentities($result->RegulatorName) ?></span>
            </td>
            <td>
              <span class="fw-medium"><?php echo htmlentities($result->ShortName) ?></span>
            </td>
            <td>
              <span class="fw-medium"><?php echo htmlentities($result->created_at) ?></span>
            </td>
            <td><?php echo htmlentities($result->updated_at) ?></td>
          </tr>
        <?php }
      } else { ?>
        <div class="text-center">
          <img src="../../assets/img/illustrations/empty-box.png" class="avatar avatar-xl mt-4" alt="">
          <h6 class="mt-4">មិនទាន់មាន Permission នៅឡើយ!</h6>
        </div>
      <?php } ?>
      </tbody>
      </table>
    </div>
  </div>
</div>
<?php $content = ob_get_clean(); ?>

<?php include('../../includes/layout.php'); ?>
