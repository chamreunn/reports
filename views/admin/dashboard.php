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
ob_start(); // Start output buffering
?>
<div class="card mb-3">
  <div class="card-body">
    <h2 class="text-center">Pending Requests</h2>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Request ID</th>
          <th>User Information</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Fetch pending requests with user information
        $stmt = $dbh->prepare("SELECT r.id AS request_id, u.Honorific, u.FirstName, u.LastName, u.Email, u.Profile, r.status, r.admin_comment
                             FROM tblrequest r
                             INNER JOIN tbluser u ON r.user_id = u.id
                             WHERE r.status = 'pending'");
        $stmt->execute();
        $pendingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($pendingRequests as $request) {
          echo "<tr>";
          echo "<td>{$request['request_id']}</td>";
          echo "<td>{$request['Honorific']} {$request['FirstName']} {$request['LastName']} <br> Email: {$request['Email']}</td>";
          echo "<td><span class='badge bg-secondary'>{$request['status']}</span></td>";
          echo "<td class='d-flex'>";
          if ($request['status'] === 'rejected') {
            // Display a button to show the modal for adding comments
            echo "<button type='button' class='btn btn-sm btn-danger' data-bs-toggle='modal' data-bs-target='#rejectCommentModal' data-request-id='{$request['request_id']}'>Add Comment</button>";
          } else {
            // Display buttons to approve or reject requests
            echo "<form action='../../controllers/update_status.php' method='POST' class='me-2'>";
            echo "<input type='hidden' name='request_id' value='{$request['request_id']}'>";
            echo "<input type='hidden' name='status' value='approved'>";
            echo "<button type='submit' class='btn btn-sm btn-success'>Approve</button>";
            echo "</form>";

            echo "<form action='../../controllers/update_status.php' method='POST'>";
            echo "<input type='hidden' name='request_id' value='{$request['request_id']}'>";
            echo "<input type='hidden' name='status' value='rejected'>";
            echo "<button type='submit' class='btn btn-sm btn-danger'>Reject</button>";
            echo "</form>";
          }
          echo "</td>";
          echo "</tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal for adding comments to rejected requests -->
<div class="modal fade" id="rejectCommentModal" tabindex="-1" aria-labelledby="rejectCommentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rejectCommentModalLabel">Add Comment for Rejection</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="../../controllers/update_status.php" method="POST">
          <input type="hidden" name="status" value="rejected">
          <input type="hidden" id="rejectRequestId" name="request_id">
          <div class="mb-3">
            <label for="rejectComment" class="form-label">Comment:</label>
            <textarea class="form-control" id="rejectComment" name="comment" rows="3" required></textarea>
          </div>
          <button type="submit" class="btn btn-danger">Reject</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <h2 class="text-center">Approved Requests</h2>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Request ID</th>
          <th>User Information</th>
          <th>Status</th>
          <th>Admin Comment</th>
          <th>Approved At</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Fetch approved requests with user information
        $stmt = $dbh->prepare("SELECT r.id AS request_id, u.Honorific, u.FirstName, u.LastName, u.Email, u.Profile, r.status, r.admin_comment, r.approved_at
                             FROM tblrequest r
                             INNER JOIN tbluser u ON r.user_id = u.id
                             WHERE r.status = 'approved'");
        $stmt->execute();
        $approvedRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($approvedRequests as $request) {
          echo "<tr>";
          echo "<td>{$request['request_id']}</td>";
          echo "<td>{$request['Honorific']} {$request['FirstName']} {$request['LastName']} <br> Email: {$request['Email']}</td>";
          echo "<td><span class='badge bg-success'>{$request['status']}</span></td>";
          echo "<td>{$request['admin_comment']}</td>";
          echo "<td>{$request['approved_at']}</td>";
          echo "</tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<div class="card mb-3">
  <div class="card-body">
    <h2 class="text-center">Rejected Requests</h2>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Request ID</th>
          <th>User Information</th>
          <th>Status</th>
          <th>Admin Comment</th>
          <th>Rejected At</th>
        </tr>
      </thead>
      <tbody>
        <?php
        // Fetch rejected requests with user information
        $stmt = $dbh->prepare("SELECT r.id AS request_id, u.Honorific, u.FirstName, u.LastName, u.Email, u.Profile, r.status, r.admin_comment, r.rejected_at
                             FROM tblrequest r
                             INNER JOIN tbluser u ON r.user_id = u.id
                             WHERE r.status = 'rejected'");
        $stmt->execute();
        $rejectedRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rejectedRequests as $request) {
          echo "<tr>";
          echo "<td>{$request['request_id']}</td>";
          echo "<td>{$request['Honorific']} {$request['FirstName']} {$request['LastName']} <br> Email: {$request['Email']}</td>";
          echo "<td><span class='badge bg-danger'>{$request['status']}</span></td>";
          echo "<td>{$request['admin_comment']}</td>";
          echo "<td>{$request['rejected_at']}</td>";
          echo "</tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<?php $content = ob_get_clean(); ?>
<?php include('../../layouts/admin_layout.php'); ?>
