<?php
session_start();
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

$pageTitle = "គណនីអេដមីន | ";
$sidebar = "admin_account";
ob_start(); // Start output buffering
include('../../config/dbconn.php');
include('../../controllers/form_process.php');
?>
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="content-left">
                        <span>នាយកដ្ឋានកិច្ចការទូទៅ</span>
                        <div class=" d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2">0</h4>
                        </div>
                        <p class="mb-0">សរុប</p>
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
                        <span data-i18n="នាយកដ្ឋានសវនកម្មទី១">នាយកដ្ឋានសវនកម្មទី១</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2">0</h4>
                        </div>
                        <p class="mb-0">សរុប</p>
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
                        <span data-i18n="នាយកដ្ឋានសវនកម្មទី២">នាយកដ្ឋានសវនកម្មទី២</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2">0</h4>
                        </div>
                        <p class="mb-0">សរុប</p>
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
                        <span>អង្គភាព</span>
                        <div class="d-flex align-items-end mt-2">
                            <h4 class="mb-0 me-2">0</h4>
                        </div>
                        <p class="mb-0">សរុប</p>
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
<!-- Users List Table -->

<div class="card">
    <div class="card-header border-bottom">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="card-title">Search Filter</h5>
            <!-- Button trigger modal -->
            <button class="btn btn-secondary add-new btn-primary" tabindex="0" aria-controls="DataTables_Table_0"
                type="button" data-bs-target="#editUser" data-bs-toggle="modal">
                <span><i class=" bx bx-plus me-0 me-sm-1"></i>
                    <span class="d-none d-sm-inline-block">បង្កើតគណនីថ្មី</span>
                </span>
            </button>
        </div>

        <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0">
            <div class="col-md-4 user_role">
                <select id="UserRole" class="select2 form-select text-capitalize">
                    <option value=""> Select Role </option>
                    <option value="Admin">Admin</option>
                    <option value="Author">Author</option>
                    <option value="Editor">Editor</option>
                    <option value="Maintainer">Maintainer</option>
                    <option value="Subscriber">Subscriber</option>
                </select>
            </div>
            <div class="col-md-4 user_plan">
                <select id="UserPlan" class="select2 form-select text-capitalize">
                    <option value=""> Select Plan </option>
                    <option value="Basic">Basic</option>
                    <option value="Company">Company</option>
                    <option value="Enterprise">Enterprise</option>
                    <option value="Team">Team</option>
                </select>
            </div>
            <div class="col-md-4 user_status">
                <select id="FilterTransaction" class="select2 form-select text-capitalize">
                    <option value=""> Select Status </option>
                    <option value="Pending" class="text-capitalize">Pending</option>
                    <option value="Active" class="text-capitalize">Active</option>
                    <option value="Inactive" class="text-capitalize">Inactive</option>
                </select>
            </div>
        </div>
    </div>


    <?php
$sql = "SELECT * FROM tblrole JOIN admin ON tblrole.id = admin.RoleId";
$query = $dbh->prepare($sql);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);
$cnt = 1;
if ($query->rowCount() > 0) {
?>
    <div class="card-datatable table-responsive">
    <table id="notificationsTable" class="dt-responsive table border-top table-striped dataTable no-footer dtr-inline collapsed" aria-describedby="notificationsTable_info">
        <thead class='thead-dark'>
                <tr>
                    <th scope='col'>ID</th>
                    <th scope='col'>ឈ្មោះនាយកដ្ឋាន</th>
                    <th scope='col'>Role</th>
                    <th scope='col'>បង្កើតនៅ</th>
                    <th scope='col'>កែប្រែនៅ</th>
                    <th scope='col'>ស្ថានភាព</th>
                    <th scope='col' class='text-end'>សកម្មភាព</th>
                </tr>
            </thead>
            <tbody class='table-border-bottom-0'>
                <?php foreach ($results as $result) { ?>
                <tr>
                    <td><?php echo $cnt ?></td>
                    <td class="sorting_1">
                        <div class="d-flex justify-content-start align-items-center user-name">
                            <?php if (!empty($result->Profile)) : ?>
                            <div class="avatar-wrapper">
                                <div class="avatar avatar-sm me-3">
                                    <img src="<?php echo htmlentities($result->Profile); ?>" alt="Profile Picture"
                                        class="rounded-circle" style="object-fit: cover;" />
                                </div>
                            </div>
                            <?php else : ?>
                            <div class="avatar-wrapper">
                                <div class="avatar avatar-sm me-3">
                                    <span
                                        class="avatar-initial rounded-circle bg-label-success"><?php echo substr($result->UserName, 0, 2); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="d-flex flex-column">
                                <a href="all-users-detail.php?uid=<?php echo htmlentities($result->id) ?>"
                                    class="text-body text-truncate">
                                    <span
                                        class="fw-medium"><?php echo $result->fullname?></span>
                                </a>
                                <small class="text-muted"><?php echo $result->email ?></small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span
                            class="fw-medium head-of-department badge <?php echo $result->Colors ?>"><?php echo $result->RoleName ?></span>
                    </td>
                    <td><?php echo htmlentities($result->CreationDate) ?></td>
                    <td><?php echo htmlentities($result->UpdateAt) ?></td>
                    <td>
                        <span
                            class="fw-medium dep-head-of-department badge <?php echo ($result->Status == 1) ? 'bg-label-success' : ''; ?>"><?php echo ($result->Status == 1) ? 'ACTIVE' : 'INACTIVE'; ?></span>
                    </td>
                    <td class="text-end">
                        <div class="d-inline-block d-flex align-items-center justify-content-end text-nowrap">
                            <button class="btn btn-sm btn-icon">
                                <i class="bx bx-edit text-primary"></i>
                            </button>
                        </div>
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
} else {
?>
    <div class="text-center">
        <img src="../../assets/img/illustrations/empty-box.png" class="avatar avatar-xl mt-4" alt="">
        <h6 class="mt-4">មិនទាន់មាននាយកដ្ឋាននៅឡើយ !</h6>
    </div>
    <?php
}
?>


</div>
<!--/ Content wrapper -->
<!-- Edit User Modal -->
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
                        <img src="../../assets/img/avatars/no-image.jpg" alt="user-avatar" class="d-block rounded"
                            height="150" width="150" id="uploadedAvatar" style="object-fit: cover;" />
                        <div class="button-wrapper">
                            <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block"><i class="bx bx-photo-album"></i>ប្តូររូបភាព</span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" id="upload" name="profile" class="account-file-input" hidden
                                    accept="image/png, image/jpeg" />
                            </label>
                        </div>
                    </div>

                    <div class="col-12 col-md-2">
                        <label for="honorific" class="form-label">គោរមងារ
                            <span class="text-danger fw-bolder">*</span>
                        </label>
                        <select id="honorific" class="select2 form-select form-select-lg select2-hidden-accessible"
                            data-allow-clear="false" tabindex="-1" aria-hidden="true" name="honorific">
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
                        <input type="text" id="firstname" name="firstname" class="form-control" placeholder="គោត្តនាម"
                            autofocus required>
                        <div
                            class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
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
                            <input type="phonenumber" id="contact" name="contact" class="form-control phone-number-mask"
                                placeholder="098 765 4321" required>
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
                        <input type="text" id="username" name="username" class="form-control"
                            placeholder="ឈ្មោះមន្ត្រី">
                        <div
                            class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="email">Email
                            <span class="text-danger fw-bolder">*</span>
                        </label>
                        <input type="text" id="email" name="email" class="form-control" required
                            placeholder="example@gmail.com">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="password">ពាក្យសម្ងាត់
                            <span class="text-danger fw-bolder">*</span>
                        </label>
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            aria-describedby="password" required>
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
                        <input type="text" id="formValidationDob" name="dob" class="form-control phone-number-mask"
                            placeholder="ថ្ងៃខែឆ្នាំកំណើត" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label" for="department">នាយកដ្ឋាន
                            <span class="text-danger fw-bolder">*</span>
                        </label>
                        <select id="department" name="department" class="select2 orm-select"
                            aria-label="Default select example">
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
                        <select id="office" name="office" class="select2 orm-select" aria-label="Default select example"
                            required>
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
                        <select id="role" name="role" class="select2 orm-select" aria-label="Default select example"
                            required>
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
                        <select id="permissionid" name="permissionid[]" class="select2 form-select"
                            aria-label="Default select example" multiple required>
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
                            <textarea name="address" class="form-control" id="address" rows="4"
                                placeholder="អាសយដ្ឋានបច្ចុប្បន្ន"></textarea>
                        </div>
                    </div>

                    <div class="col-12 text-center">
                        <button type="submit" id="submitForm" class="btn btn-primary me-sm-3 me-1">រក្សាទុក</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                            aria-label="Close">មិនទាន់</button>
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

<?php include('../../includes/layout.php'); ?>
