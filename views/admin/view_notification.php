<?php
session_start();
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

$pageTitle = "Admin Dashboard";
$sidebar = "home";

// Check for status messages
$getnotification = $_GET['id'] ?? null;


// Update notification to "read"
if ($getnotification) {
  try {
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = :notification_id";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':notification_id', $getnotification);
    $stmt->execute();
  } catch (PDOException $e) {
    // Handle error
    echo "Error updating notification: " . $e->getMessage();
  }
}
if ($getnotification) {
  try {
    // Fetch request details based on notification ID
    $requestSql = "SELECT r.*, u.UserName, u.Profile
                     FROM tblrequest r
                     JOIN tbluser u ON r.user_id = u.id
                     WHERE r.id = (
                         SELECT request_id FROM notifications WHERE id = :notification_id
                     )";
    $requestStmt = $dbh->prepare($requestSql);
    $requestStmt->bindParam(':notification_id', $getnotification);
    $requestStmt->execute();
    $requestDetails = $requestStmt->fetch(PDO::FETCH_ASSOC);

    ob_start(); // Start output buffering
    include('../../config/dbconn.php');
    include('../../includes/login_check.php');
?>
    <div class="row">
      <!-- Request details -->
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h2>Request Details</h2>
            <div class="user-profile">
              <img src="<?php echo $requestDetails['Profile']; ?>" alt="User Profile" width="100">
              <p class="user-name"><?php echo $requestDetails['UserName']; ?></p>
            </div>
            <div class="request-info">
              <p class="request-name"><?php echo $requestDetails['request_name_1']; ?></p>
              <p class="request-description"><?php echo $requestDetails['description_1']; ?></p>
              <p class="attachments-title">Attachments:</p>
              <ul class="attachments-list">
                <?php
                // Fetch and display attachments
                $attachmentsSql = "SELECT * FROM tblrequest_attachments WHERE request_id = :request_id";
                $attachmentsStmt = $dbh->prepare($attachmentsSql);
                $attachmentsStmt->bindParam(':request_id', $requestDetails['id']);
                $attachmentsStmt->execute();
                $attachments = $attachmentsStmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($attachments as $attachment) {
                  echo '<li><a href="' . $attachment['file_path'] . '" target="_blank">' . basename($attachment['file_path']) . '</a></li>';
                }
                ?>
              </ul>
            </div>
            <!-- Approved and Rejected buttons -->
            <div class="action-buttons">
              <form action="process_request.php" method="post">
                <input type="hidden" name="request_id" value="<?php echo $requestDetails['id']; ?>">
                <input type="hidden" name="adminid" value="<?php echo $_SESSION['userid']; ?>">
                <button type="submit" name="action" value="approve_request" class="btn btn-success">Approve</button>
                <button type="submit" name="action" value="reject_request" class="btn btn-danger">Reject</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php
  } catch (PDOException $e) {
    // Handle database error
    echo "Error fetching request details: " . $e->getMessage();
  }
} else {
  // Notification ID not provided
  echo "Notification ID not found.";
}
?>
<?php $content = ob_get_clean(); ?>
<?php include('../../includes/layout.php'); ?>
