<?php
session_start();
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
    header('Location: ../../index.php');
    exit();
}

$pageTitle = "ទំព័រដើម";
$sidebar = "office";
ob_start(); // Start output buffering
include('../../config/dbconn.php');
include('../../controllers/form_process.php');
?>
<div class="row">
    <!-- Role cards -->
    <div class="col-md mb-4 mb-md-0">
        <div class="d-flex align-items-center justify-content-between p-0 mb-0">
            <nav aria-label="breadcrumb mb-0">
                <ol class="breadcrumb breadcrumb-style1">
                    <li class="breadcrumb-item">
                        <a href="office.php"><i class="bx bx-buildings"></i>គ្រប់គ្រង</a>
                    </li>
                    <li class="breadcrumb-item active text-primary"><i
                            class="bx bx-add-to-queue"></i>បង្កើតនាយកដ្ឋានថ្មី</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center justify-content-between">
            <small class="text-light fw-medium mb-0">នាយកដ្ឋាន</small>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                បង្កើតនាយកដ្ឋានថ្មី <i class="bx bx-plus mx-2 me-0"></i>
            </button>
        </div>

        <div class="card mt-3">
            <div class="table-responsive text-nowrap">
                <?php
        $sql = "SELECT d.*,
                       CONCAT(hd.FirstName, ' ', hd.LastName) AS HeadFullName,
                       GROUP_CONCAT(CONCAT(dhd.FirstName, ' ', dhd.LastName) SEPARATOR ', ') AS DepHeadFullNames,
                       CONCAT(hu.FirstName, ' ', hu.LastName) AS HeadUnitFullName,
                       GROUP_CONCAT(CONCAT(dhu.FirstName, ' ', dhu.LastName) SEPARATOR ', ') AS DepHeadUnitFullNames
                FROM tbldepartments AS d
                LEFT JOIN tbluser AS hd ON hd.id = d.HeadOfDepartment
                LEFT JOIN tbluser AS dhd ON FIND_IN_SET(dhd.id, d.DepHeadOfDepartment)
                LEFT JOIN tbluser AS hu ON hu.id = d.HeadOfUnit
                LEFT JOIN tbluser AS dhu ON FIND_IN_SET(dhu.id, d.DepHeadOfUnit)
                GROUP BY d.id";
        $query = $dbh->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        $cnt = 1;
        if ($query->rowCount() > 0) { ?>
                <table class='table'>
                    <thead class='thead-dark'>
                        <tr>
                            <th scope='col'>ID</th>
                            <th scope='col'>ឈ្មោះនាយកដ្ឋាន</th>
                            <th scope='col'>ប្រធានអង្គភាព</th>
                            <th scope='col'>អនុប្រធានអង្គភាព</th>
                            <th scope='col'>ប្រធាន</th>
                            <th scope='col'>អនុប្រធាន</th>
                            <th scope='col'>បង្កើតនៅ</th>
                            <th scope='col'>កែប្រែនៅ</th>
                            <th scope='col' class='text-end'>សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody class='table-border-bottom-0'>
                        <?php foreach ($results as $result) { ?>
                        <tr>
                            <td class="department-id" name="departmentId"><?php echo htmlentities($result->id) ?></td>
                            <td><span class="fw-medium"><?php echo htmlentities($result->DepartmentName) ?></span></td>
                            <td><span class="fw-medium"><?php echo htmlentities($result->HeadUnitFullName) ?></span>
                            </td>
                            <td><span class="fw-medium"><?php echo htmlentities($result->DepHeadUnitFullNames) ?></span>
                            </td>
                            <td><span class="fw-medium"><?php echo htmlentities($result->HeadFullName) ?></span></td>
                            <td><span class="fw-medium"><?php echo htmlentities($result->DepHeadFullNames) ?></span>
                            </td>
                            <td><?php echo htmlentities($result->CreationDate) ?></td>
                            <td><?php echo htmlentities($result->UpdateAt) ?></td>
                            <td class="d-flex align-items-center justify-content-end">
                                <div class="d-inline-block text-nowrap">
                                    <a href="department-edit.php?depid=<?php echo htmlentities($result->id) ?>"
                                        class="btn text-primary btn-sm btn-icon btn-edit">
                                        <i class="bx bx-edit"></i> កែប្រែ
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php }} else { ?>
                        <div class="text-center">
                            <img src="../../assets/img/illustrations/empty-box.png" class="avatar avatar-xl mt-4"
                                alt="">
                            <h6 class="mt-4">មិនទាន់មាននាយកដ្ឋាននៅឡើយ !</h6>
                        </div>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<!-- Modal -->
<!-- create department-modal  -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-simple modal-dialog-centered modal-add-new-role">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
                <div class="text-center mb-4">
                    <h3 class="role-title mef2">បង្កើតនាយកដ្ឋានថ្មី</h3>
                </div>
                <!-- Add role form -->
                <form id="formAuthentication" class="mb-3" method="POST">
                    <input type="hidden" name="login_type" value="departments">
                    <div class="mb-3">
                        <label for="department" class="form-label">ឈ្មោះនាយកដ្ឋាន</label>
                        <span class="text-danger fw-bold">*</span>
                        <input type="text" class="form-control" id="department" name="department"
                            placeholder="សូមបញ្ចូលឈ្មោះនាយកដ្ឋាន" autofocus required />
                    </div>
                    <div class="col-md-12 mb-4" data-select2-id="66">
                        <label for="headofunit" class="form-label">ប្រធានអង្គភាព</label>
                        <div class="position-relative" data-select2-id="65">
                            <select id="headofunit" name="headofunit"
                                class="select2 form-select form-select-lg select2-hidden-accessible"
                                data-allow-clear="true" data-select2-id="headofunit" tabindex="-1" aria-hidden="true">
                                <option data-tokens="ketchup mustard"></option>
                                <?php
                $sql = "SELECT * FROM tbluser";
                $query = $dbh->prepare($sql);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                if ($query->rowCount() > 0) {
                    foreach ($results as $result) {
                        $fullname = $result->Honorific . " " . $result->FirstName . " " . $result->LastName;
                ?>
                                <option value="<?php echo $result->id ?>" data-tokens="ketchup mustard">
                                    <?php echo $fullname; ?></option>
                                <?php
                    }
                } else {
                ?>
                                <option data-tokens="ketchup mustard">មិនទាន់មានឈ្មោះ</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 mb-4" data-select2-id="66">
                        <label for="depheadofunit" class="form-label">អនុប្រធានអង្គភាព</label>
                        <div class="position-relative" data-select2-id="65">
                            <select id="depheadofunit" name="depheadofunit[]"
                                class="select2 form-select form-select-lg select2-hidden-accessible"
                                data-allow-clear="true" multiple data-select2-id="depheadofunit" tabindex="-1"
                                aria-hidden="true">
                                <option data-tokens="ketchup mustard"></option>
                                <?php
                if ($query->rowCount() > 0) {
                    foreach ($results as $result) {
                        $fullname = $result->Honorific . " " . $result->FirstName . " " . $result->LastName;
                ?>
                                <option value="<?php echo $result->id ?>" data-tokens="ketchup mustard">
                                    <?php echo $fullname; ?></option>
                                <?php
                    }
                } else {
                ?>
                                <option data-tokens="ketchup mustard">មិនទាន់មានឈ្មោះ</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 mb-4" data-select2-id="66">
                        <label for="select2Basic" class="form-label">ប្រធាននាយកដ្ឋាន</label>
                        <div class="position-relative" data-select2-id="65">
                            <select id="select2Basic" name="headdep"
                                class="select2 form-select form-select-lg select2-hidden-accessible"
                                data-allow-clear="true" data-select2-id="select2Basic" tabindex="-1" aria-hidden="true">
                                <option data-tokens="ketchup mustard"></option>
                                <?php
                $sql = "SELECT * FROM tbluser";
                $query = $dbh->prepare($sql);
                $query->execute();
                $results = $query->fetchAll(PDO::FETCH_OBJ);
                if ($query->rowCount() > 0) {
                    foreach ($results as $result) {
                        $fullname = $result->Honorific . " " . $result->FirstName . " " . $result->LastName;
                ?>
                                <option value="<?php echo $result->id ?>" data-tokens="ketchup mustard">
                                    <?php echo $fullname; ?></option>
                                <?php
                    }
                } else {
                ?>
                                <option data-tokens="ketchup mustard">មិនទាន់មានឈ្មោះ</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 mb-4" data-select2-id="66">
                        <label for="select2Basic1" class="form-label">អនុប្រធាននាយកដ្ឋាន</label>
                        <div class="position-relative" data-select2-id="65">
                            <select id="select2Basic1" name="deheaddep"
                                class="select2 form-select form-select-lg select2-hidden-accessible"
                                data-allow-clear="true" data-select2-id="select2Basic1" tabindex="-1"
                                aria-hidden="true">
                                <option data-tokens="ketchup mustard"></option>
                                <?php
                if ($query->rowCount() > 0) {
                    foreach ($results as $result) {
                        $fullname = $result->Honorific . " " . $result->FirstName . " " . $result->LastName;
                ?>
                                <option value="<?php echo $result->id ?>" data-tokens="ketchup mustard">
                                    <?php echo $fullname; ?></option>
                                <?php
                    }
                } else {
                ?>
                                <option data-tokens="ketchup mustard">មិនទាន់មានឈ្មោះ</option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 fv-plugins-icon-container">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="formValidationCheckbox"
                                name="formValidationCheckbox">
                            <label class="form-check-label" for="formValidationCheckbox">យល់ស្រប</label>
                            <span class="text-danger fw-bold">*</span>
                            <div
                                class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-center mt-4">
                        <button class="btn btn-primary d-flex" name="create-depart">បង្កើត</button>
                        <button type="button" class="btn btn-label-secondary mx-2"
                            data-bs-dismiss="modal">បោះបង់</button>
                    </div>
                </form>
                <!--/ Add role form -->

            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>


<?php include('../../includes/layout.php'); ?>