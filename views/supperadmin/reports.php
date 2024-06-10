<?php
session_start();
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

$pageTitle = "ទំព័រដើម";
$sidebar = "report";
ob_start(); // Start output buffering
include('../../config/dbconn.php');
include('../../controllers/form_process.php');
?>
<div class="card mb-3">
  <div class="d-flex align-items-center justify-content-between">
    <h5 class="card-header mef2">របាយការណ៍សវនកម្ម</h5>
    <div class="card-header"><a href="create-reports.php" class="btn btn-primary">Create New Report</a></div>
  </div>
  <?php
  $sql = "SELECT * FROM admin JOIN form_data ON form_data.admin_id = admin.id";
  $query = $dbh->prepare($sql);
  $query->execute();
  $results = $query->fetchAll(PDO::FETCH_OBJ);
  $cnt = 1;
  if ($query->rowCount() > 0) { ?>
    <div class="card-datatable table-responsive">
      <table id="notificationsTable" class="dt-responsive table border-top table-striped dataTable no-footer dtr-inline collapsed" aria-describedby="notificationsTable_info">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>ចំណងជើង</th>
            <th></th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody class="table-border-bottom-0">
          <?php foreach ($results as $result) { ?>
            <tr>
              <td><?php echo $cnt ?></td>
              <td><?php echo htmlentities($result->headline) ?></td>
              <td></td>
              <td>
                <a href="edit-reports.php?erid=<?php echo htmlentities($result->id) ?>">
                  <i class="bx bx-edit"></i>
                </a>
              </td>
            </tr>
          <?php
            $cnt++;
          }
          ?>
        </tbody>
      </table>
    </div>
  <?php
  } else { ?>
    <div class="text-center">
      <img src="../../assets/img/illustrations/empty-box.png" class="avatar avatar-xl mt-4" alt="">
      <h6 class="mt-4">មិនទាន់មាននាយកដ្ឋាននៅឡើយ !</h6>
    </div>
  <?php
  } ?>
</div>
<?php $content = ob_get_clean(); ?>

<?php include('../../includes/layout.php'); ?>
