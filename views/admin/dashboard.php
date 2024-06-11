<?php
session_start();
include('../../config/dbconn.php');
require_once '../../includes/translate.php';

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}
require_once '../../models/AdminDashboardModel.php';
?>

<div class="container my-2">
  <div class="row mb-3">
    <div class="col-12">
      <div class="card mb-2">
        <div class="card-widget-separator-wrapper">
          <div class="card-body card-widget-separator">
            <div class="row gy-4 gy-sm-1">
              <!-- Pending Requests -->
              <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                  <div>
                    <h3 class="mb-1"><?= $counts['pending_count']; ?></h3>
                    <p class="mb-0"><?= translate('Pending Requests') ?></p>
                  </div>
                  <span class="badge bg-label-warning rounded p-2 me-sm-4">
                    <i class="bx bx-time-five bx-sm"></i>
                  </span>
                </div>
                <hr class="d-none d-sm-block d-lg-none me-4">
              </div>
              <!-- Approved Requests -->
              <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                  <div>
                    <h3 class="mb-1"><?= $counts['approved_count']; ?></h3>
                    <p class="mb-0"><?= translate('Approved Requests') ?></p>
                  </div>
                  <span class="badge bg-label-success rounded p-2 me-lg-4">
                    <i class="bx bx-check-circle bx-sm"></i>
                  </span>
                </div>
                <hr class="d-none d-sm-block d-lg-none">
              </div>
              <!-- Rejected Requests -->
              <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                  <div>
                    <h3 class="mb-1"><?= $counts['rejected_count']; ?></h3>
                    <p class="mb-0"><?= translate('Rejected Requests') ?></p>
                  </div>
                  <span class="badge bg-label-danger rounded p-2 me-sm-4">
                    <i class="bx bx-x-circle bx-sm"></i>
                  </span>
                </div>
              </div>
              <!-- Completed Requests -->
              <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-start pb-3 pb-sm-0 card-widget-4">
                  <div>
                    <h3 class="mb-1"><?= $counts['completed_count']; ?></h3>
                    <p class="mb-0"><?= translate('Completed Requests') ?></p>
                  </div>
                  <span class="badge bg-label-primary rounded p-2">
                    <i class="bx bx-check-double bx-sm"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- User Activity Logs -->
  <div class="card mb-4">
    <div class="card-header border-bottom mb-3 d-flex justify-content-between align-items-center">
      <h4 class="text-start mb-0">
        <i class='bx bx-user p-2 rounded-circle bg-label-warning'></i> <?= translate('Recent Pending Requests') ?>
      </h4>
      <div class="d-none d-sm-block mb-0">
        <a href="view_more.php?action=pending" class="btn btn-sm btn-label-secondary rounded-4">
          <?= translate('View More') ?> <i class="bx bx-chevron-right scaleX-n1-rtl"></i>
        </a>
      </div>
      <div class="d-block d-sm-none mb-0">
        <a href="view_more.php?action=pending" class="btn btn-sm btn-icon btn-label-secondary">
          <i class="bx bx-chevron-right scaleX-n1-rtl"></i>
        </a>
      </div>
    </div>

    <div class="card-body">
      <?php if (empty($pendingRequests)) : ?>
        <div class="text-center">
          <i class="bx bxs-error-circle fs-1 text-muted mb-3 mt-4"></i>
          <p class="text-muted"><?= translate('Oops! No pending requests found.') ?></p>
        </div>
      <?php else : ?>
        <ul class="list-group">
          <?php foreach ($pendingRequests as $request) : ?>
            <li class="list-group-item">
              <div class="d-flex align-items-center">
                <img src="<?= $request['Profile'] ?>" alt="<?= translate('Profile Picture') ?>" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                <div>
                  <div class="fw-bolder"><?= translate('Username: ') ?><?= $request['Honorific'] . " " . $request['FirstName'] . " " . $request['LastName'] ?><br></div>
                  <div><?= translate('Status: ') ?><span class="badge bg-secondary"><?= $request['status'] ?></span></div>
                </div>
                <button class="btn btn-sm btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#exampleModal<?= $request['request_id'] ?>"><?= translate('View Detail') ?></button>

                <div class="modal fade" id="exampleModal<?= $request['request_id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><?= translate('Request Details') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= translate('Close') ?>"></button>
                      </div>
                      <div class="modal-body">
                        <p><strong><?= translate('Username:') ?></strong> <?= $request['Honorific'] . " " . $request['FirstName'] . " " . $request['LastName'] ?></p>
                        <p><strong><?= translate('Email:') ?></strong> <?= $request['Email'] ?></p>
                        <p><strong><?= translate('Status:') ?></strong> <?= $request['status'] ?></p>
                        <p><strong><?= translate('Request Name:') ?></strong> <?= $request['request_name_1'] ?></p>
                        <!-- Display file attachments -->
                        <?php
                        // Fetch file attachments for this request
                        $attachmentsStmt = $dbh->prepare("SELECT * FROM tblrequest_attachments WHERE request_id = :request_id");
                        $attachmentsStmt->bindParam(":request_id", $request['request_id']);
                        $attachmentsStmt->execute();
                        $attachments = $attachmentsStmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <p><strong><?= translate('Attachments:') ?></strong></p>
                        <ul>
                          <?php foreach ($attachments as $attachment) : ?>
                            <li><a href="<?= $attachment['file_path'] ?>" target="_blank"><?= $attachment['file_path'] ?></a></li>
                          <?php endforeach; ?>
                        </ul>
                        <!-- Admin comments and action -->
                        <form action="../../controllers/update_request.php" method="POST">
                          <div class="mb-3">
                            <label for="adminComment" class="form-label"><?= translate('Admin Comment:') ?></label>
                            <textarea class="form-control" id="adminComment" name="admin_comment" rows="3" required></textarea>
                          </div>
                          <input type="hidden" name="request_id" value="<?= $request['request_id'] ?>">
                          <button type="submit" class="btn btn-success" name="approve_request"><?= translate('Approve') ?></button>
                          <button type="submit" class="btn btn-danger" name="reject_request"><?= translate('Reject') ?></button>
                        </form>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= translate('Close') ?></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

  <!-- Approved Requests -->
  <div class="card mb-4">
    <div class="card-header border-bottom mb-3 d-flex justify-content-between align-items-center">
      <h4 class="text-start mb-0">
        <i class='bx bx-check-circle p-2 rounded-circle bg-label-success'></i> <?= translate('Recent Approved Requests') ?>
      </h4>
      <div class="d-none d-sm-block mb-0">
        <a href="view_more.php?action=approved" class="btn btn-sm btn-label-secondary rounded-4">
          <?= translate('View More') ?> <i class="bx bx-chevron-right scaleX-n1-rtl"></i>
        </a>
      </div>
      <div class="d-block d-sm-none mb-0">
        <a href="view_more.php?action=approved" class="btn btn-sm btn-icon btn-label-secondary">
          <i class="bx bx-chevron-right scaleX-n1-rtl"></i>
        </a>
      </div>
    </div>

    <div class="card-body">
      <?php if (empty($approvedRequests)) : ?>
        <div class="text-center">
          <i class="bx bxs-error-circle fs-1 text-muted mb-3 mt-4"></i>
          <p class="text-muted"><?= translate('Oops! No approved requests found.') ?></p>
        </div>
      <?php else : ?>
        <ul class="list-group">
          <?php foreach ($approvedRequests as $request) : ?>
            <li class="list-group-item">
              <div class="d-flex align-items-center">
                <img src="<?= $request['Profile'] ?>" alt="<?= translate('Profile Picture') ?>" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                <div>
                  <div class="fw-bolder"><?= translate('Username: ') ?><?= $request['Honorific'] . " " . $request['FirstName'] . " " . $request['LastName'] ?><br></div>
                  <div><?= translate('Status: ') ?><span class="badge bg-success"><?= $request['status'] ?></span></div>
                </div>
                <button class="btn btn-sm btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#exampleModal<?= $request['request_id'] ?>"><?= translate('View Detail') ?></button>

                <div class="modal fade" id="exampleModal<?= $request['request_id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><?= translate('Request Details') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= translate('Close') ?>"></button>
                      </div>
                      <div class="modal-body">
                        <p><strong><?= translate('Username:') ?></strong> <?= $request['Honorific'] . " " . $request['FirstName'] . " " . $request['LastName'] ?></p>
                        <p><strong><?= translate('Email:') ?></strong> <?= $request['Email'] ?></p>
                        <p><strong><?= translate('Status:') ?></strong> <?= $request['status'] ?></p>
                        <p><strong><?= translate('Request Name:') ?></strong> <?= $request['request_name_1'] ?></p>
                        <!-- Display file attachments -->
                        <?php
                        // Fetch file attachments for this request
                        $attachmentsStmt = $dbh->prepare("SELECT * FROM tblrequest_attachments WHERE request_id = :request_id");
                        $attachmentsStmt->bindParam(":request_id", $request['request_id']);
                        $attachmentsStmt->execute();
                        $attachments = $attachmentsStmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <p><strong><?= translate('Attachments:') ?></strong></p>
                        <ul>
                          <?php foreach ($attachments as $attachment) : ?>
                            <li><a href="<?= $attachment['file_path'] ?>" target="_blank"><?= $attachment['file_path'] ?></a></li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= translate('Close') ?></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>

  <!-- Rejected Requests -->
  <div class="card mb-4">
    <div class="card-header border-bottom mb-3 d-flex justify-content-between align-items-center">
      <h4 class="text-start mb-0">
        <i class='bx bx-x-circle p-2 rounded-circle bg-label-danger'></i> <?= translate('Recent Rejected Requests') ?>
      </h4>
      <div class="d-none d-sm-block mb-0">
        <a href="view_more.php?action=rejected" class="btn btn-sm btn-label-secondary rounded-4">
          <?= translate('View More') ?> <i class="bx bx-chevron-right scaleX-n1-rtl"></i>
        </a>
      </div>
      <div class="d-block d-sm-none mb-0">
        <a href="view_more.php?action=rejected" class="btn btn-sm btn-icon btn-label-secondary">
          <i class="bx bx-chevron-right scaleX-n1-rtl"></i>
        </a>
      </div>
    </div>

    <div class="card-body">
      <?php if (empty($rejectedRequests)) : ?>
        <div class="text-center">
          <i class="bx bxs-error-circle fs-1 text-muted mb-3 mt-4"></i>
          <p class="text-muted"><?= translate('Oops! No rejected requests found.') ?></p>
        </div>
      <?php else : ?>
        <ul class="list-group">
          <?php foreach ($rejectedRequests as $request) : ?>
            <li class="list-group-item">
              <div class="d-flex align-items-center">
                <img src="<?= $request['Profile'] ?>" alt="<?= translate('Profile Picture') ?>" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                <div>
                  <div class="fw-bolder"><?= translate('Username: ') ?><?= $request['Honorific'] . " " . $request['FirstName'] . " " . $request['LastName'] ?><br></div>
                  <div><?= translate('Status: ') ?><span class="badge bg-danger"><?= $request['status'] ?></span></div>
                </div>
                <button class="btn btn-sm btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#exampleModal<?= $request['request_id'] ?>"><?= translate('View Detail') ?></button>

                <div class="modal fade" id="exampleModal<?= $request['request_id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><?= translate('Request Details') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= translate('Close') ?>"></button>
                      </div>
                      <div class="modal-body">
                        <p><strong><?= translate('Username:') ?></strong> <?= $request['Honorific'] . " " . $request['FirstName'] . " " . $request['LastName'] ?></p>
                        <p><strong><?= translate('Email:') ?></strong> <?= $request['Email'] ?></p>
                        <p><strong><?= translate('Status:') ?></strong> <?= $request['status'] ?></p>
                        <p><strong><?= translate('Request Name:') ?></strong> <?= $request['request_name_1'] ?></p>
                        <!-- Display file attachments -->
                        <?php
                        // Fetch file attachments for this request
                        $attachmentsStmt = $dbh->prepare("SELECT * FROM tblrequest_attachments WHERE request_id = :request_id");
                        $attachmentsStmt->bindParam(":request_id", $request['request_id']);
                        $attachmentsStmt->execute();
                        $attachments = $attachmentsStmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <p><strong><?= translate('Attachments:') ?></strong></p>
                        <ul>
                          <?php foreach ($attachments as $attachment) : ?>
                            <li><a href="<?= $attachment['file_path'] ?>" target="_blank"><?= $attachment['file_path'] ?></a></li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= translate('Close') ?></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
  <!-- Completed Requests -->
  <div class="card mb-4">
    <div class="card-header border-bottom mb-3 d-flex justify-content-between align-items-center">
      <h4 class="text-start mb-0">
        <i class='bx bx-check-double p-2 rounded-circle bg-label-primary'></i> <?= translate('Recent Completed Requests') ?>
      </h4>
      <div class="d-none d-sm-block mb-0">
        <a href="view_more.php?action=completed" class="btn btn-sm btn-label-secondary rounded-4">
          <?= translate('View More') ?> <i class="bx bx-chevron-right scaleX-n1-rtl"></i>
        </a>
      </div>
      <div class="d-block d-sm-none mb-0">
        <a href="view_more.php?action=completed" class="btn btn-sm btn-icon btn-label-secondary">
          <i class="bx bx-chevron-right scaleX-n1-rtl"></i>
        </a>
      </div>
    </div>

    <div class="card-body">
      <?php if (empty($completedRequests)) : ?>
        <div class="text-center">
          <i class="bx bxs-error-circle fs-1 text-muted mb-3 mt-4"></i>
          <p class="text-muted"><?= translate('Oops! No completed requests found.') ?></p>
        </div>
      <?php else : ?>
        <ul class="list-group">
          <?php foreach ($completedRequests as $request) : ?>
            <li class="list-group-item">
              <div class="d-flex align-items-center">
                <img src="<?= $request['Profile'] ?>" alt="<?= translate('Profile Picture') ?>" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                <div>
                  <div class="fw-bolder"><?= translate('Username: ') ?><?= $request['Honorific'] . " " . $request['FirstName'] . " " . $request['LastName'] ?><br></div>
                  <div><?= translate('Status: ') ?><span class="badge bg-primary"><?= $request['status'] ?></span></div>
                </div>
                <button class="btn btn-sm btn-primary ms-auto" data-bs-toggle="modal" data-bs-target="#exampleModal<?= $request['request_id'] ?>"><?= translate('View Detail') ?></button>

                <div class="modal fade" id="exampleModal<?= $request['request_id'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><?= translate('Request Details') ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= translate('Close') ?>"></button>
                      </div>
                      <div class="modal-body">
                        <p><strong><?= translate('Username:') ?></strong> <?= $request['Honorific'] . " " . $request['FirstName'] . " " . $request['LastName'] ?></p>
                        <p><strong><?= translate('Email:') ?></strong> <?= $request['Email'] ?></p>
                        <p><strong><?= translate('Status:') ?></strong> <?= $request['status'] ?></p>
                        <p><strong><?= translate('Request Name:') ?></strong> <?= $request['request_name_1'] ?></p>
                        <!-- Display file attachments -->
                        <?php
                        // Fetch file attachments for this request
                        $attachmentsStmt = $dbh->prepare("SELECT * FROM tblrequest_attachments WHERE request_id = :request_id");
                        $attachmentsStmt->bindParam(":request_id", $request['request_id']);
                        $attachmentsStmt->execute();
                        $attachments = $attachmentsStmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>
                        <p><strong><?= translate('Attachments:') ?></strong></p>
                        <ul>
                          <?php foreach ($attachments as $attachment) : ?>
                            <li><a href="<?= $attachment['file_path'] ?>" target="_blank"><?= $attachment['file_path'] ?></a></li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= translate('Close') ?></button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
include('../../layouts/admin_layout.php');
?>
