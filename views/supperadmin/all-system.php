<?php
session_start();
error_reporting(0);
include('../../includes/dbconn.php');
if (strlen($_SESSION['alogin']) == 0) {
  header('location:../../index.php');
} else {
  if (isset($_POST['added'])) {
    $status = 1;
    $sysname = $_POST['sysname'];
    $content = $_POST['describe'];
    $link = $_POST['link'];
    date_default_timezone_set('Asia/Bangkok');
    $date = date('d-M-Y h:i A');

    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];

    // Move uploaded file to desired folder
    $upload_dir = "../../uploads/pictures/";
    $destination = $upload_dir . $file_name;
    move_uploaded_file($file_tmp, $destination);

    if ($sysname == '') {
      sleep(3);
      $error = "please fill the system name";
    } elseif ($content == '') {
      sleep(3);
      $error = "please fill the content";
    } elseif ($link == '') {
      sleep(3);
      $error = "please fill out the link of system";
    } else {

      $sql = "SELECT * FROM tblallsystems WHERE SystemName = '$sysname'";
      $query = $dbh->prepare($sql);
      $query->execute();
      $results = $query->fetchAll(PDO::FETCH_OBJ);

      if ($results) {
        sleep(3);
        $error = 'The Name already existed';
      } else {
        $sql = "INSERT INTO tblallsystems (SystemName,Status,Link,Content,Pictures,CreationDate) VALUES (:sysname,:status,:link,:content,:file_name,:date)";
        $query = $dbh->prepare($sql);
        $query->bindParam(':sysname', $sysname, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_STR);
        $query->bindParam(':link', $link, PDO::PARAM_STR);
        $query->bindParam(':content', $content, PDO::PARAM_STR);
        $query->bindParam(':file_name', $file_name, PDO::PARAM_STR);
        $query->bindParam(':date', $date, PDO::PARAM_STR);
        $query->execute();
        $lastInsertId = $dbh->lastInsertId();

        if ($lastInsertId) {
          sleep(3);
          $msg = "Record has been added Successfully";
        } else {
          sleep(3);
          $error = "ERROR";
        }
      }
    }
  }

?>
  <?php include('../../includes/alert.php'); ?>
  <!DOCTYPE html>

  <html lang="en" class="light-style layout-compact layout-navbar-fixed layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo/assets/" data-base-url="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo-1" data-framework="laravel" data-template="vertical-menu-theme-default-light">

  <head>
    <?php
    $header = 'ទំព័រដើម';
    include('../../includes/header.php');
    ?>
  </head>

  <body>
    <!-- <?php include('../../includes/loader.php') ?> -->
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <?php
        $page = 'all-systems';
        include('../../includes/admins-sidebar.php'); ?>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <?php
          $page = 'all-systems';
          include('../../includes/admins-navbar.php');
          ?>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="row mb-0 pb-0">

                <div class="col-md-12 order-1 order-lg-1 mb-4 mb-lg-0">
                  <div class="card text-center mb-4">
                    <div class="card-header border-bottom py-3 mb-4">
                      <div class="d-flex align-items-center justify-content-between">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-browser" aria-controls="navs-pills-browser" aria-selected="true"><i class="bx bx-file me-2"></i>ច្បាប់ឈប់សម្រាក</button>
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                          <i class="bx bx-plus me-2"></i>
                          បន្ថែមប្រព័ន្ធ
                        </button>
                      </div>
                      <!-- Modal -->
                      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                          <div class="modal-content">
                            <form class="row g-3 needs-validation" method="POST" enctype="multipart/form-data" novalidate>
                              <div class="modal-header">
                                <h1 class="modal-title mef2 fs-5" id="exampleModalLabel">បន្ថែមប្រព័ន្ធ</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <div class="d-flex align-items-center justify-content-center align-items-sm-center gap-4 mb-4">
                                  <img src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template-free/demo/assets/img/avatars/1.png" class="d-block rounded" style="object-fit: cover;" alt="Avatar" height="200" width="300" id="uploadedAvatar">
                                </div>
                                <div class="button-wrapper">
                                  <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block">Upload new photo</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                    <input type="file" name="file" id="upload" class="account-file-input" hidden="" accept="image/png, image/jpeg">
                                  </label>
                                </div>
                                <div class="fv-plugins-icon-container text-start mb-2">
                                  <label class="form-label" for="inputId">ឈ្មោះប្រព័ន្ធ</label>
                                  <input type="text" class="form-control" name="sysname" id="inputId" placeholder="ឈ្មោះប្រព័ន្ធ" autofocus="true" aria-label="ឈ្មោះប្រព័ន្ធ" required>
                                  <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                </div>

                                <div class="fv-plugins-icon-container text-start mb-2">
                                  <label class="form-label" for="inputId">តំណភ្ជាប់ទៅកាន់ប្រព័ន្ធ</label>
                                  <input type="link" class="form-control" name="link" id="inputId" placeholder="https://www.exampl.com" autofocus="true" aria-label="https://www.exampl.com" required>
                                  <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                </div>

                                <div class="fv-plugins-icon-container text-start mb-2">
                                  <label class="form-label" for="inputId">រៀបរាប់ពីប្រព័ន្ធ</label>
                                  <textarea type="text" class="form-control" name="describe" id="inputId" placeholder="រៀបរាប់ពីប្រព័ន្ធ" autofocus="true" aria-label="រៀបរាប់ពីប្រព័ន្ធ" required></textarea>
                                  <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="added" class="btn btn-primary">Save changes</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-content pt-0 text-start">
                      <div class="tab-pane fade active show" id="navs-pills-browser" role="tabpanel">
                        <div class="list-group">
                          <?php
                          $sql = "SELECT * FROM tblallsystems";
                          $query = $dbh->prepare($sql);
                          $query->execute();
                          $results = $query->fetchAll(PDO::FETCH_OBJ);
                          $cnt = 1;
                          if ($query->rowCount() > 0) {
                            foreach ($results as $result) {
                          ?>
                              <a href="all-systems-edit.php?allid=<?php echo htmlentities($result->id) ?>" class="list-group-item list-group-item-action d-flex align-items-center p-3">
                                <div class="badge bg-primary rounded p-1 me-3"><i class="bx bx-file bx-sm text-white"></i></div>
                                <div class="w-100">
                                  <div class="d-flex align-items-center justify-content-between">
                                    <div class="">
                                      <div class="d-flex">
                                        <h6 class="mb-0"><?php echo htmlentities($result->SystemName) ?></h6>
                                      </div>
                                      <small class="text-primary"><?php echo htmlentities($result->CreationDate) ?></small>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-3 text-end">
                                  <button type="button" class="btn btn-sm btn-icon btn-label-secondary">
                                    <i class="bx bx-chevron-right scaleX-n1-rtl"></i>
                                  </button>
                                </div>
                              </a>
                            <?php }
                          } else { ?>
                            <div class="text-center mt-5">
                              <img src="../../../../assets/img/illustrations/empty-box.png" class="avatar avatar-xl mt-4" alt="">
                              <h6 class="mt-4">មិនទាន់មានសំណើរច្បាប់ឈប់សម្រាកនៅឡើយ!</h6>
                            </div>
                          <?php } ?>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- / Content -->
          <div class="content-backdrop fade"></div>
        </div>
        <!-- Content wrapper -->
      </div>
      <!-- / Layout page -->
    </div>
    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->
    <!-- build:js assets/vendor/js/core.js -->
    <?php include('../../includes/footer.php') ?>
  </body>

  </html>
<?php } ?>
