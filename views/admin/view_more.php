<?php
session_start();
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
    header('Location: ../../index.php');
    exit();
}
require_once '../../models/AdminViewMoreModel.php';
?>

<div class="container my-2">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header border-bottom mb-3 d-flex justify-content-between align-items-center">
                    <h4 class="text-start mb-0">
                        <i class='bx bx-user p-2 rounded-circle bg-label-warning'></i> <?= $pageTitle ?>
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (empty($requests)) : ?>
                        <div class="text-center">
                            <i class="bx bxs-error-circle fs-1 text-muted mb-3"></i>
                            <p class="text-muted">No <?= strtolower($pageTitle) ?> found.</p>
                        </div>
                    <?php else : ?>
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Request Name</th>
                                    <?php if ($action != 'pending') : ?>
                                        <th>Admin Comment</th>
                                    <?php endif; ?>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $request) : ?>
                                    <tr>
                                        <td><?= $request['Honorific'] . " " . $request['FirstName'] . " " . $request['LastName'] ?></td>
                                        <td><?= $request['Email'] ?></td>
                                        <td><span class="badge bg-label-<?= $action == 'approved' ? 'success' : ($action == 'rejected' ? 'danger' : ($action == 'completed' ? 'primary' : 'warning')) ?>"><?= $request['status'] ?></span></td>
                                        <td><?= $request['request_name_1'] ?></td>
                                        <?php if ($action != 'pending') : ?>
                                            <td><?= $request['admin_comment'] ?></td>
                                        <?php endif; ?>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal<?= ucfirst($action) . $request['request_id'] ?>">View Detail</button>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="exampleModal<?= ucfirst($action) . $request['request_id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Request Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p><strong>Username:</strong> <?= $request['Honorific'] . " " . $request['FirstName'] . " " . $request['LastName'] ?></p>
                                                    <p><strong>Email:</strong> <?= $request['Email'] ?></p>
                                                    <p><strong>Status:</strong> <?= $request['status'] ?></p>
                                                    <p><strong>Request Name:</strong> <?= $request['request_name_1'] ?></p>
                                                    <!-- Display file attachments -->
                                                    <?php
                                                    // Fetch file attachments for this request
                                                    $attachmentsStmt = $dbh->prepare("SELECT * FROM tblrequest_attachments WHERE request_id = :request_id");
                                                    $attachmentsStmt->bindParam(":request_id", $request['request_id']);
                                                    $attachmentsStmt->execute();
                                                    $attachments = $attachmentsStmt->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <p><strong>Attachments:</strong></p>
                                                    <ul>
                                                        <?php foreach ($attachments as $attachment) : ?>
                                                            <li><a href="<?= $attachment['file_path'] ?>" target="_blank"><?= $attachment['file_path'] ?></a></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                    <?php if ($action != 'pending') : ?>
                                                        <p><strong>Admin Comment:</strong> <?= $request['admin_comment'] ?></p>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include('../../layouts/admin_layout.php');
?>
