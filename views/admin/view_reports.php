<?php
session_start();
include('../../config/dbconn.php');
require_once '../../includes/translate.php';

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
    header('Location: ../../index.php');
    exit();
}

$pageTitle = "View Reports";
$sidebar = "view_more.php";
ob_start(); // Start output buffering

// Function to sanitize input
function sanitizeInput($input)
{
    return htmlspecialchars(trim($input));
}

// Fetch reports from tblreport_step3 with user and request details
$reportsQuery = "SELECT rep.*, r.*, u.*, c.comment AS admin_comment
                 FROM tblreport_step3 rep
                 LEFT JOIN tblrequest r ON rep.request_id = r.id
                 LEFT JOIN tbluser u ON r.user_id = u.id
                 LEFT JOIN tblcomments c ON rep.id = c.report_id
                 ORDER BY rep.id DESC";
$reportsStmt = $dbh->prepare($reportsQuery);
$reportsStmt->execute();
$reports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!-- Smart Breadcrumb -->
<nav aria-label="breadcrumb mb-2">
  <ol class="breadcrumb">
    <li class="breadcrumb-item">
      <a href="dashboard.php" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= translate('Go back to Dashboard') ?>">
        <span class="d-flex align-items-center mb-0">
          <i class='bx bxs-chevron-left-square mb-0'></i>
          <?= translate('Dashboard') ?>
        </span>
      </a>
    </li>
    <li class="breadcrumb-item text-uppercase text-primary active" aria-current="page"><?= $pageTitle ?></li>
  </ol>
</nav>

<div class="row mb-3">
    <div class="col-12">
        <div class="card-body">
            <?php if (empty($reports)) : ?>
                <div class="text-center">
                    <i class="bx bxs-error-circle fs-1 text-muted mb-3"></i>
                    <p class="text-muted">No reports found.</p>
                </div>
            <?php else : ?>
                <?php foreach ($reports as $report) : ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">User Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Name:</strong> <?= $report['Honorific'] . " " . $report['FirstName'] . " " . $report['LastName'] ?></p>
                                    <p><strong>Email:</strong> <?= $report['Email'] ?></p>
                                    <img src="<?= $report['Profile'] ?>" alt="User Avatar" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Request Details</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Status:</strong> <?= $report['status'] ?></p>
                                    <p><strong>Request Name:</strong> <?= $report['request_name_1'] ?></p>
                                    <p><strong>Created At:</strong> <?= $report['created_at'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Report Details</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-justify"><strong>Report ID:</strong> <?= $report['id'] ?></p>
                                    <?php
                                    // Use newline as the delimiter to split the headlines and data
                                    $headlines = explode("\n", trim($report['headline']));
                                    $data = explode("\n", trim($report['data']));

                                    foreach ($headlines as $index => $headline) {
                                        $headline = htmlspecialchars_decode($headline);
                                        $dataLine = htmlspecialchars_decode($data[$index]);
                                        echo "<p class='text-justify'><strong class='mef2'>{$headline}:</strong> {$dataLine}</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title"><i class="bx bx-comment p-2 bg-label-info rounded-circle me-2"></i><?= translate('Comments') ?></h5>
                                </div>
                                <div class="card-body">
                                    <!-- Display messages -->
                                    <div id="messages"></div>

                                    <div id="comments-section-<?= $report['id'] ?>">
                                        <?php
                                        $commentsQuery = "SELECT c.*, u.FirstName, u.LastName, u.Profile
                                                          FROM tblcomments c
                                                          LEFT JOIN tbluser u ON c.user_id = u.id
                                                          WHERE c.report_id = :report_id
                                                          ORDER BY c.created_at DESC";
                                        $commentsStmt = $dbh->prepare($commentsQuery);
                                        $commentsStmt->bindParam(':report_id', $report['id'], PDO::PARAM_INT);
                                        $commentsStmt->execute();
                                        $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

                                        if (empty($comments)) { ?>
                                            <div class="d-flex align-items-center flex-column justify-content-center text-muted mb-4">
                                                <div class="text-muted fw-bold"><?= translate('No Comment(s) Yet') ?></div>
                                            </div>
                                        <?php } else {
                                            foreach ($comments as $comment) {
                                                $commentClass = ($_SESSION['userid'] == $comment['user_id']) ? 'comment-left' : 'comment-right';
                                                $profilePic = ($comment['Profile']) ? $comment['Profile'] : 'default-profile.jpg';
                                                $utcDate = new DateTime($comment['created_at'], new DateTimeZone('UTC'));
                                                $utcDate->setTimezone(new DateTimeZone('Asia/Bangkok'));
                                                $localDate = $utcDate->format('M j, Y h:i A');
                                        ?>
                                                <div class="mb-3 comment-container <?= $commentClass ?>" id="comment-<?= $comment['id'] ?>">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <img src='<?= $profilePic ?>' alt='User Avatar' class='avatar me-2 rounded-circle' style='object-fit: cover; width: 50px; height: 50px;'>
                                                        <p><strong><?php echo $comment['FirstName'] . " " . $comment['LastName'] ?></strong></p>
                                                    </div>
                                                    <div class="flex-grow-1 border-bottom">
                                                        <div class="d-flex align-items-center">
                                                            <p class="mb-0">Comment: <?php echo $comment['comment'] ?></p>
                                                            <?php if ($_SESSION['userid'] == $comment['user_id']) { ?>
                                                                <a href="#editForm<?php echo $comment['id'] ?>" class='btn btn-sm btn-link ms-2' role='button' data-bs-toggle='collapse' aria-expanded='false' aria-controls="editForm<?php echo $comment['id'] ?>"><i class='bx bxs-edit-alt' data-bs-toggle='tooltip' data-bs-placement='top' title='<?= translate('Edit Comment') ?>'></i></a>
                                                                <div class="dropdown">
                                                                    <button class='btn btn-sm btn-link mx-2 dropdown-toggle' type='button' id='dropdownDelete<?php echo $comment['id'] ?>' data-bs-toggle='dropdown' aria-expanded='false'>
                                                                        <i class='bx bx-trash text-danger' data-bs-toggle='tooltip' data-bs-placement='top' title='<?= translate('Delete Comment') ?>'></i>
                                                                    </button>
                                                                    <ul class='dropdown-menu dropdown-menu-right' aria-labelledby='dropdownDelete<?php echo $comment['id'] ?>'>
                                                                        <li>
                                                                            <form class="p-4" method="post" onsubmit="return deleteComment(event, '<?php echo $comment['id']; ?>', '<?php echo addslashes($comment['comment']) ?>')">
                                                                              <p>Are you Sure You Want to Delete this Comment?</p>
                                                                                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                                                                <input type="hidden" name="comment_text" value="<?php echo htmlspecialchars($comment['comment']); ?>">
                                                                                <button type="submit" name="delete_comment" class="btn btn-sm btn-danger">Delete</button>
                                                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown" aria-expanded="false">Cancel</button>
                                                                            </form>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                        <p class="text-muted">Posted on <?= $localDate ?></p>

                                                        <div class="collapse mt-3 mb-3" id="editForm<?php echo $comment['id'] ?>">
                                                            <form method='post' onsubmit="return updateComment(event, '<?php echo $comment['id'] ?>')">
                                                                <input type='hidden' name='comment_id' value='<?php echo $comment['id'] ?>'>
                                                                <textarea name='new_comment' class='form-control mb-2' rows='2' placeholder='Edit your comment'><?php echo $comment['comment'] ?></textarea>
                                                                <button type='submit' name='update_comment' class='btn btn-sm btn-primary'>Save</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </div>

                                    <form method="post" onsubmit="return postComment(event, '<?= $report['id'] ?>')">
                                        <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                                        <textarea name="user_comment" class="form-control mb-2" rows="3" placeholder="<?= translate('Add your comment') ?>"></textarea>
                                        <div class="d-flex justify-content-end mt-2 mb-0">
                                            <button type="submit" class="btn btn-primary mb-0"><?= translate('Post Comment') ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function postComment(event, reportId) {
        event.preventDefault();
        let form = event.target;
        let formData = $(form).serialize();

        $.ajax({
            type: "POST",
            url: "post_comment.php",
            data: formData,
            success: function(response) {
                let result = JSON.parse(response);
                if (result.success) {
                    $('#comments-section-' + reportId).append(result.commentHtml);
                    form.reset();
                    $('#messages').html('<div class="alert alert-success mt-3">' + result.message + '</div>');
                } else {
                    $('#messages').html('<div class="alert alert-danger mt-3">' + result.message + '</div>');
                }
            },
            error: function() {
                $('#messages').html('<div class="alert alert-danger mt-3">An error occurred while posting the comment.</div>');
            }
        });
        return false;
    }

    function updateComment(event, commentId) {
        event.preventDefault();
        let form = event.target;
        let formData = $(form).serialize();

        $.ajax({
            type: "POST",
            url: "update_comment.php",
            data: formData,
            success: function(response) {
                let result = JSON.parse(response);
                if (result.success) {
                    $('#comment-' + commentId).replaceWith(result.commentHtml);
                    $('#messages').html('<div class="alert alert-success mt-3">' + result.message + '</div>');
                } else {
                    $('#messages').html('<div class="alert alert-danger mt-3">' + result.message + '</div>');
                }
            },
            error: function() {
                $('#messages').html('<div class="alert alert-danger mt-3">An error occurred while updating the comment.</div>');
            }
        });
        return false;
    }

    function deleteComment(event, commentId, commentText) {
        event.preventDefault();
        let form = event.target;
        let formData = $(form).serialize();

        $.ajax({
            type: "POST",
            url: "delete_comment.php",
            data: formData,
            success: function(response) {
                let result = JSON.parse(response);
                if (result.success) {
                    $('#comment-' + commentId).remove();
                    $('#messages').html('<div class="alert alert-success mt-3">' + result.message + '</div>');
                } else {
                    $('#messages').html('<div class="alert alert-danger mt-3">' + result.message + '</div>');
                }
            },
            error: function() {
                $('#messages').html('<div class="alert alert-danger mt-3">An error occurred while deleting the comment.</div>');
            }
        });
        return false;
    }
</script>

<?php
$content = ob_get_clean();
include('../../layouts/admin_layout.php');
?>
