<?php
session_start();
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
    header('Location: ../../index.php');
    exit();
}

$pageTitle = "តួនាទី | គ្រប់គ្រង";
$sidebar = "role";
ob_start(); // Start output buffering
include('../../config/dbconn.php');
include('../../controllers/form_process.php');
?>
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Role cards -->
    <div class="row g-4">
        <?php
    $sql = "SELECT * FROM tblrole";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    $cnt = 1;
    if ($query->rowCount() > 0) {
      foreach ($results as $result) {               ?>
        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="fw-normal">Total users</h6>
                        <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                class="avatar avatar-sm pull-up" aria-label="Vinnie Mostowy"
                                data-bs-original-title="Vinnie Mostowy">
                                <img class="rounded-circle"
                                    src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo/assets/img/avatars/5.png"
                                    alt="Avatar">
                            </li>
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                class="avatar avatar-sm pull-up" aria-label="Allen Rieske"
                                data-bs-original-title="Allen Rieske">
                                <img class="rounded-circle"
                                    src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo/assets/img/avatars/12.png"
                                    alt="Avatar">
                            </li>
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                class="avatar avatar-sm pull-up" aria-label="Julee Rossignol"
                                data-bs-original-title="Julee Rossignol">
                                <img class="rounded-circle"
                                    src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo/assets/img/avatars/6.png"
                                    alt="Avatar">
                            </li>
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                class="avatar avatar-sm pull-up" aria-label="Kaith D'souza"
                                data-bs-original-title="Kaith D'souza">
                                <img class="rounded-circle"
                                    src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo/assets/img/avatars/15.png"
                                    alt="Avatar">
                            </li>
                            <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                                class="avatar avatar-sm pull-up" aria-label="John Doe"
                                data-bs-original-title="John Doe">
                                <img class="rounded-circle"
                                    src="https://demos.themeselection.com/sneat-bootstrap-html-laravel-admin-template/demo/assets/img/avatars/1.png"
                                    alt="Avatar">
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div class="role-heading">
                            <h4 class="mb-1"><?php echo htmlentities($result->RoleName) ?></h4>
                            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addRoleModal"
                                class="role-edit-modal">
                                <small>Edit Role</small>
                            </a>
                        </div>
                        <a href="javascript:void(0);" class="text-muted">
                            <i class="bx bx-copy"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php }
    } ?>

        <div class="col-xl-4 col-lg-6 col-md-6">
            <div class="card h-100">
                <div class="row h-100">
                    <div class="col-sm-5">
                        <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-3">
                            <img src="../../assets/img/illustrations/sitting-girl-with-laptop-light.png"
                                class="img-fluid" alt="Image" width="120"
                                data-app-light-img="illustrations/sitting-girl-with-laptop-light.png"
                                data-app-dark-img="illustrations/sitting-girl-with-laptop-dark.png">
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="card-body text-sm-end text-center ps-sm-0">
                            <button data-bs-target="#addRoleModal" data-bs-toggle="modal"
                                class="btn btn-primary mb-3 text-nowrap add-new-role">Add New
                                Role</button>
                            <p class="mb-0">Add role, if it does not exist</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md mb-4 mb-md-0 mt-3">
        <small class="text-light fw-medium">Popout Accordion</small>
        <div class="accordion accordion-popout mt-3" id="accordionPopout">
            <?php
        $sql = "SELECT * FROM tblrole";
        $query = $dbh->prepare($sql);
        $query->execute();
        $roles = $query->fetchAll(PDO::FETCH_OBJ);
        if ($query->rowCount() > 0) {
            foreach ($roles as $role) {
        ?>
            <div class="card accordion-item">
                <h2 class="accordion-header" id="headingPopout<?php echo htmlentities($role->id); ?>">
                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse"
                        data-bs-target="#collapsePopout<?php echo htmlentities($role->id); ?>" aria-expanded="false"
                        aria-controls="collapsePopout<?php echo htmlentities($role->id); ?>">
                        <?php echo htmlentities($role->RoleName); ?>
                    </button>
                </h2>
                <div id="collapsePopout<?php echo htmlentities($role->id); ?>" class="accordion-collapse collapse"
                    aria-labelledby="headingPopout<?php echo htmlentities($role->id); ?>"
                    data-bs-parent="#accordionPopout">
                    <div class="accordion-body">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Permission Name</th>
                                    <th>User</th>
                                    <th>Creation Date</th>
                                    <th>Update Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                <?php
                                    $sql_permission = "SELECT p.*, u.UserName FROM tblpermission p INNER JOIN tbluser u ON p.RoleId = :roleId AND u.PermissionId = p.id";
                                    $query_permission = $dbh->prepare($sql_permission);
                                    $query_permission->bindParam(':roleId', $role->id, PDO::PARAM_INT);
                                    $query_permission->execute();
                                    $permissions = $query_permission->fetchAll(PDO::FETCH_OBJ);
                                    if ($query_permission->rowCount() > 0) {
                                        foreach ($permissions as $permission) {
                                    ?>
                                <tr>
                                    <td>
                                        <span
                                            class="fw-medium"><?php echo htmlentities($permission->PermissionName); ?></span>
                                    </td>
                                    <td><?php echo htmlentities($permission->UserName); ?></td>
                                    <td><?php echo htmlentities($permission->CreationDate); ?></td>
                                    <td><?php echo htmlentities($permission->UpdateAt); ?></td>
                                    <td class="d-flex align-items-center justify-content-end">
                                        <div class="d-inline-block text-nowrap">
                                            <button class="btn btn-sm btn-icon"><i class="bx bx-edit"></i></button>
                                            <button class="btn btn-sm btn-icon delete-record"><i
                                                    class="bx bx-trash"></i></button>
                                            <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"><i
                                                    class="bx bx-dots-vertical-rounded me-2"></i></button>
                                            <div class="dropdown-menu dropdown-menu-end m-0">
                                                <a href="#" class="dropdown-item">View</a>
                                                <a href="javascript:;" class="dropdown-item">Suspend</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                        }
                                    } else {
                                    ?>
                                <tr>
                                    <td colspan="5" class="text-center">No permissions found for this role.</td>
                                </tr>
                                <?php
                                    }
                                    ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            }
        } else {
        ?>
            <div class="card">
                <div class="card-body">
                    <p class="card-text">No roles found.</p>
                </div>
            </div>
            <?php
        }
        ?>
        </div>
    </div>

</div>
</div>
</div>

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-dialog-centered modal-add-new-role">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="role-title">Add New Role</h3>
                    <p>Set role permissions</p>
                </div>
                <!-- Add role form -->
                <form id="formAuthentication" class="mb-3" method="POST">
                    <input type="hidden" name="login_type" value="role">
                    <div class="col-12 mb-3 fv-plugins-icon-container">
                        <label class="form-label" for="modalRoleName">Role Name</label>
                        <input type="text" id="modalRoleName" name="rname" class="form-control"
                            placeholder="Enter a role name" tabindex="-1" autofocus required>
                        <div
                            class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                        </div>
                    </div>
                    <div class="col-md-12 mb-4">
                        <label for="selectpickerBasic" class="form-label">ជ្រើសរើសពណ៌</label>
                        <div class="dropdown bootstrap-select w-100">
                            <div class="btn-group w-100" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="colors" id="color-primary"
                                    value="bg-label-primary" checked="">
                                <label class="btn btn-outline-primary" for="color-primary">Primary</label>
                                <input type="radio" class="btn-check" name="colors" id="color-secondary"
                                    value="bg-label-secondary">
                                <label class="btn btn-outline-secondary" for="color-secondary">Secondary</label>
                                <input type="radio" class="btn-check" name="colors" id="color-success"
                                    value="bg-label-success">
                                <label class="btn btn-outline-success" for="color-success">Success</label>
                                <input type="radio" class="btn-check" name="colors" id="color-danger"
                                    value="bg-label-danger">
                                <label class="btn btn-outline-danger" for="color-danger">Danger</label>
                                <input type="radio" class="btn-check" name="colors" id="color-warning"
                                    value="bg-label-warning">
                                <label class="btn btn-outline-warning" for="color-warning">Warning</label>
                                <input type="radio" class="btn-check" name="colors" id="color-info"
                                    value="bg-label-info">
                                <label class="btn btn-outline-info" for="color-info">Info</label>
                                <input type="radio" class="btn-check" name="colors" id="color-dark"
                                    value="bg-label-dark">
                                <label class="btn btn-outline-dark" for="color-dark">Dark</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <h4>Role Permissions</h4>
                        <!-- Permission table -->
                        <div class="table-responsive">
                            <table class="table table-flush-spacing">
                                <tbody>
                                    <tr>
                                        <td class="text-nowrap fw-medium">Supper Admin Access
                                            <i class="bx bx-info-circle bx-xs" data-bs-toggle="tooltip"
                                                data-bs-placement="top" aria-label="Allows a full access to the system"
                                                data-bs-original-title="Allows a full access to the system">
                                            </i>
                                        </td>
                                        <td class="justify-content-end">
                                            <label class="switch switch-primary">
                                                <input type="checkbox" name="switch-admin" id="supperAdminToggle"
                                                    class="switch-input">
                                                <span class="switch-toggle-slider">
                                                    <span class="switch-on">
                                                        <i class="bx bx-check"></i>
                                                    </span>
                                                    <span class="switch-off">
                                                        <i class="bx bx-x"></i>
                                                    </span>
                                                </span>
                                            </label>
                                        </td>
                                    </tr>
                                    <?php
                  $sql = "SELECT * FROM tblpermission";
                  $query = $dbh->prepare($sql);
                  $query->execute();
                  $results = $query->fetchAll(PDO::FETCH_OBJ);
                  $cnt = 1;
                  if ($query->rowCount() > 0) {
                    foreach ($results as $result) {
                  ?>
                                    <tr>
                                        <td class="text-nowrap fw-medium">
                                            <input type="hidden" name="rid"
                                                value="<?php echo htmlentities($result->id) ?>">
                                            <?php echo htmlentities($result->PermissionName) ?>
                                        </td>
                                        <td class="justify-content-end">
                                            <label class="switch switch-primary">
                                                <input type="checkbox" name="pid[]"
                                                    value="<?php echo htmlentities($result->id) ?>"
                                                    class="switch-input permission-toggle">
                                                <span class="switch-toggle-slider">
                                                    <span class="switch-on">
                                                        <i class="bx bx-check"></i>
                                                    </span>
                                                    <span class="switch-off">
                                                        <i class="bx bx-x"></i>
                                                    </span>
                                                </span>
                                            </label>
                                        </td>
                                    </tr>
                                    <?php }
                  } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Permission table -->
                    </div>
                    <div class="col-12 text-center">
                        <button class="btn btn-primary me-sm-3 me-1">Submit</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
                            aria-label="Close">Cancel</button>
                    </div>
                    <input type="hidden">
                </form>
                <!--/ Add role form -->
            </div>
        </div>
    </div>
</div>
<!--/ Add Role Modal -->
<?php $content = ob_get_clean(); ?>

<?php include('../../includes/layout.php'); ?>