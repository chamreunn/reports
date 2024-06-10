<?php
session_start();
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
    header('Location: ../../index.php');
    exit();
}

$pageTitle = "ទំព័រដើម";
$sidebar = "home";
ob_start(); // Start output buffering
include('../../config/dbconn.php');
include('../../controllers/form_process.php');
?>
<div class="col-md mb-4 mb-md-0 mt-3">
    <div class="d-flex align-items-center justify-content-between">
        <small class="text-light fw-medium">បង្កើតលក្ខខណ្ឌថ្មី</small>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
            បង្កើតលក្ខខណ្ឌថ្មី <i class="bx bx-plus mx-2 me-0"></i>
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
                            <input type="hidden" name="login_type" value="permission">
                            <div class="mb-3">
                                <label for="modalPermissionName" class="form-label">
                                    ឈ្មោះលក្ខខណ្ឌ
                                </label>
                                <input type="text" class="form-control" id="modalPermissionName"
                                    name="modalPermissionName" placeholder="សូមបញ្ចូលឈ្មោះមន្ត្រី" autofocus required />
                            </div>
                            <div class="mb-3">
                                <label for="engnameper" class="form-label">
                                    Name Of Permission
                                </label>
                                <input type="text" class="form-control" id="engnameper" name="engnameper"
                                    placeholder="Please Fill" required />
                            </div>
                            <div class="col-md-12 mb-3" data-select2-id="188">
                                <label for="pertype" class="form-label">ប្រភេទ</label>
                                <div class="position-relative" data-select2-id="187">
                                    <select id="pertype" name="pertype"
                                        class="select2-icons form-select select2-hidden-accessible"
                                        data-select2-id="select2Icons" tabindex="-1" aria-hidden="true">
                                        <optgroup label="Services">
                                            <option value="" data-icon="bx bxl-wordpress" selected disabled>ជ្រើសរើស
                                            </option>
                                            <option value="Manage" data-icon="bx bxl-wordpress">
                                                Manage
                                            </option>
                                            <option value="Normal" data-icon="bx bxl-codepen">
                                                Normal
                                            </option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="btn-group mb-3 g-3 flex-grid w-100" role="group"
                                aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="pericons" id="btnradio1" value="bx bx-user">
                                <label class="btn btn-outline-primary" for="btnradio1">
                                    <i class="bx bx-user"></i>
                                </label>
                                <input type="radio" class="btn-check" value="bx bx-calendar" name="pericons"
                                    id="btnradio2">
                                <label class="btn btn-outline-primary" for="btnradio2">
                                    <i class="bx bx-calendar"></i>
                                </label>
                                <input type="radio" class="btn-check" value="bx bx-time" name="pericons" id="btnradio3">
                                <label class="btn btn-outline-primary" for="btnradio3">
                                    <i class="bx bx-time"></i>
                                </label>
                                <input type="radio" class="btn-check" value="bx bx-file" name="pericons" id="btnradio4">
                                <label class="btn btn-outline-primary" for="btnradio4">
                                    <i class="bx bx-file"></i>
                                </label>
                                <input type="radio" class="btn-check" value="bx bx-calendar-event" name="pericons"
                                    id="btnradio5">
                                <label class="btn btn-outline-primary" for="btnradio5">
                                    <i class="bx bx-calendar-event"></i>
                                </label>
                                <input type="radio" class="btn-check" value="bx bx-time-five" name="pericons"
                                    id="btnradio6">
                                <label class="btn btn-outline-primary" for="btnradio6">
                                    <i class="bx bx-time-five"></i>
                                </label>
                            </div>

                            <div class="btn-group mb-3 g-3 flex-grid w-100" role="group"
                                aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" value="bx bx-qr" name="pericons" id="btnradio7">
                                <label class="btn btn-outline-primary" for="btnradio7">
                                    <i class="bx bx-qr"></i>
                                </label>
                                <input type="radio" class="btn-check" value="bx bx-edit-alt" name="pericons"
                                    id="btnradio8">
                                <label class="btn btn-outline-primary" for="btnradio8">
                                    <i class="bx bx-edit-alt"></i>
                                </label>
                                <input type="radio" class="btn-check" value="bx bx-home" name="pericons" id="btnradio9">
                                <label class="btn btn-outline-primary" for="btnradio9">
                                    <i class="bx bx-home"></i>
                                </label>
                                <input type="radio" class="btn-check" value="bx bx-notepad" name="pericons"
                                    id="btnradio10">
                                <label class="btn btn-outline-primary" for="btnradio10">
                                    <i class="bx bx-notepad"></i>
                                </label>
                                <input type="radio" class="btn-check" value="bx bx-id-card" name="pericons"
                                    id="btnradio11">
                                <label class="btn btn-outline-primary" for="btnradio11">
                                    <i class="bx bx-id-card"></i>
                                </label>
                                <input type="radio" class="btn-check" value="bx bx-landscape" name="pericons"
                                    id="btnradio12">
                                <label class="btn btn-outline-primary" for="btnradio12">
                                    <i class="bx bx-landscape"></i>
                                </label>
                            </div>

                            <div class="col-12 fv-plugins-icon-container">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="formValidationCheckbox"
                                        name="formValidationCheckbox">
                                    <label class="form-check-label" for="formValidationCheckbox">យល់ស្រប</label>
                                    <div
                                        class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-center mt-4">
                                <button class="btn btn-primary d-flex" name="submit-permission">បង្កើត</button>
                                <button type="button" class="btn btn-label-secondary mx-2"
                                    data-bs-dismiss="modal">បោះបង់</button>
                            </div>
                        </form>
                        <!--/ Add role form -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-3">
        <div class="table-responsive text-nowrap">
            <?php
                    $sql = "SELECT * FROM tblpermission";
                    $query = $dbh->prepare($sql);
                    $query->execute();
                    $results = $query->fetchAll(PDO::FETCH_OBJ);
                    $cnt = 1;
                    if ($query->rowCount() > 0) {
                      echo " <table class='table'>
                                            <thead class='thead-dark'>
                                                <tr>
                                                    <th scope='col'>ឈ្មោះលក្ខខណ្ឌ</th>
                                                    <th scope='col'>Assign To</th>
                                                    <th scope='col'>បង្កើតនៅ</th>
                                                    <th scope='col' class='text-end'>សកម្មភាព</th>
                                                </tr>
                                            </thead>
                                            <tbody class='table-border-bottom-0'>";
                      foreach ($results as $result) {
                    ?>
            <tr>
                <td>
                    <span class="fw-medium"><?php echo htmlentities($result->PermissionName) ?></span>
                </td>
                <td>
                    <span class="fw-medium"><?php echo htmlentities($result->Assign_To) ?></span>
                </td>
                <td><?php echo htmlentities($result->CreateDate) ?></td>

                <td class="d-flex align-items-center justify-content-end">
                    <div class="d-inline-block text-nowrap"><button class="btn text-primary btn-sm btn-icon"><i
                                class="bx bx-edit"></i></button><button
                            class="btn text-danger btn-sm btn-icon delete-record"><i class="bx bx-trash"></i></button>
                    </div>
                </td>
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