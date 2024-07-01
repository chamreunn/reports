<?php
require_once '../../config/dbconn.php'; // Your database configuration

$userId = $_SESSION['userid'];
$sql = "SELECT * FROM tblrequest WHERE user_id = :user_id AND status IN ('pending', 'approved', 'completed')";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$ongoingRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($ongoingRequests as $request) {
  // Output the request details as needed
  echo '<div class="col-md-4 mb-4">';
  echo '<div class="card">';
  echo '<div class="card-header d-flex justify-content-between border-bottom p-3 mb-2">';
  echo '<h5 class="mef2 mb-0">' . htmlentities($request['request_name_1']) . '</h5>';
  echo '<div class="badge mb-0 status-' . str_replace('_', '-', htmlentities($request['status'])) . '">' . ucfirst(htmlentities($request['status'])) . '</div>';
  echo '</div>';
  echo '<div class="card-body">';
  echo '<p><strong>Regulator:</strong> ' . htmlentities($request['Regulator']) . '</p>';
  echo '<p><strong>Shortname:</strong> ' . htmlentities($request['shortname']) . '</p>';
  echo '<p><strong>Description:</strong> ' . htmlentities($request['description_1']) . '</p>';
  if (!empty($request['link_1'])) {
    echo '<p><a target="_blank" href="' . htmlentities($request['link_1']) . '">Link Documents</a></p>';
  }
  if (!empty($request['link_2'])) {
    echo '<p><a target="_blank" href="' . htmlentities($request['link_2']) . '">Link Documents</a></p>';
  }
  if (!empty($request['link_3'])) {
    echo '<p><a target="_blank" href="' . htmlentities($request['link_3']) . '">Link Documents</a></p>';
  }
  echo '<p><strong>Created At:</strong> ' . formatDateKhmer($request['created_at']) . '</p>';
  // Fetch and display attachments
  $sqlAttachments = "SELECT id, file_path FROM tblrequest_attachments WHERE request_id = :request_id";
  $stmtAttachments = $dbh->prepare($sqlAttachments);
  $stmtAttachments->bindParam(':request_id', $request['id'], PDO::PARAM_INT);
  $stmtAttachments->execute();
  $attachments = $stmtAttachments->fetchAll(PDO::FETCH_ASSOC);
  if (!empty($attachments)) {
    echo '<p><strong>Attachments:</strong></p>';
    echo '<ul class="list-unstyled">';
    foreach ($attachments as $attachment) {
      echo '<li>';
      echo '<a href="' . htmlentities($attachment['file_path']) . '" target="_blank">' . basename(htmlentities($attachment['file_path'])) . '</a>';
      echo '<button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateFileModal' . $attachment['id'] . '"><i class="bx bxs-edit-alt me-2"></i>Update</button>';
      echo '</li>';
      // Modal for updating file
      echo '<div class="modal fade" id="updateFileModal' . $attachment['id'] . '" tabindex="-1" aria-labelledby="updateFileModalLabel' . $attachment['id'] . '" aria-hidden="true">';
      echo '<div class="modal-dialog modal-dialog-centered">';
      echo '<div class="modal-content">';
      echo '<div class="modal-header">';
      echo '<h5 class="modal-title" id="updateFileModalLabel' . $attachment['id'] . '">Update File</h5>';
      echo '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
      echo '</div>';
      echo '<form method="POST" enctype="multipart/form-data" action="../../controllers/replace_file/replace_file.php">';
      echo '<div class="modal-body">';
      echo '<input type="hidden" name="attachment_id" value="' . $attachment['id'] . '">';
      echo '<input type="hidden" name="request_id" value="' . $request['id'] . '">';
      echo '<div class="mb-3">';
      echo '<label class="form-label">Current File:</label>';
      echo '<a href="' . htmlentities($attachment['file_path']) . '" target="_blank">' . basename(htmlentities($attachment['file_path'])) . '</a>';
      echo '</div>';
      echo '<div class="mb-3">';
      echo '<label for="fileInput' . $attachment['id'] . '" class="form-label">Choose New File:</label>';
      echo '<input type="file" class="form-control" id="fileInput' . $attachment['id'] . '" name="new_file" accept=".pdf,.doc,.docx" required>';
      echo '</div>';
      echo '</div>';
      echo '<div class="modal-footer">';
      echo '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
      echo '<button type="submit" class="btn btn-primary">Save changes</button>';
      echo '</div>';
      echo '</form>';
      echo '</div>';
      echo '</div>';
      echo '</div>';
    }
    echo '</ul>';
  }
  echo '</div>';
  // Add report links and actions
  echo '<div class="card-footer border-top p-3">';
  // Logic for different steps and statuses
  echo '</div>';
  echo '</div>';
  echo '</div>';
}
