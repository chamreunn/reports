<?php
session_start();
include('../../config/dbconn.php');
include('../../includes/translate.php');
// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
    header('Location: ../../index.php');
    exit();
}

$pageTitle = "ទំព័រដើម";
$sidebar = "home";
ob_start(); // Start output buffering
include('../../config/dbconn.php');
?>
<div class="row">
    <!-- single card  -->
    <div class="col-9 col-sm-12">
        <div class="card mb-4">
            <div class="card-widget-separator-wrapper">
                <div class="card-body card-widget-separator">
                    <div class="row gy-4 gy-sm-1">
                        <div class="col-sm-6 col-lg-3">
                            <div
                                class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                                <div>
                                    <h3 class="mb-1" data-i18n="10">10</h3>
                                    <p class="mb-0" data-i18n="Leave Taken"><?php echo translate('Leave Taken')?></p>
                                </div>
                                <span class="badge bg-label-warning rounded p-2 me-sm-4"
                                    data-i18n="<i class='bx bx-calendar-check bx-sm'></i>"></span>
                            </div>
                            <hr class="d-none d-sm-block d-lg-none me-4">
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div
                                class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                                <div>
                                    <h3 class="mb-1" data-i18n="5">5</h3>
                                    <p class="mb-0" data-i18n="Leave Approved"><?php echo translate('Leave Approved')?></p>
                                </div>
                                <span class="badge bg-label-success rounded p-2 me-lg-4"
                                    data-i18n="<i class='bx bx-check-double bx-sm'></i>"></span>
                            </div>
                            <hr class="d-none d-sm-block d-lg-none">
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div
                                class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                                <div>
                                    <h3 class="mb-1" data-i18n="2">2</h3>
                                    <p class="mb-0" data-i18n="Leave Rejected"><?php echo translate('Leave Rejected')?></p>
                                </div>
                                <span class="badge bg-label-danger rounded p-2 me-sm-4"
                                    data-i18n="<i class='bx bx-x-circle bx-sm'></i>"></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h3 class="mb-1" data-i18n="3">3</h3>
                                    <p class="mb-0" data-i18n="Leave This Week"><?php echo translate('Leave This Week')?></p>
                                </div>
                                <span class="badge bg-label-primary rounded p-2"
                                    data-i18n="<i class='bx bx-calendar-event bx-sm'></i>">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); ?>

<?php include('../../layouts/superadmin_layout.php'); ?>
