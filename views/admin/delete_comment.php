<?php
session_start();
include('../../config/dbconn.php');

// Function to sanitize input
function sanitizeInput($input)
{
    return htmlspecialchars(trim($input));
}

$response = ['success' => false, 'message' => '', 'commentId' => ''];

if (isset($_POST['comment_id']) && isset($_POST['comment_text'])) {
  $userId = $_SESSION['userid'];
  $commentId = sanitizeInput($_POST['comment_id']);
  $commentText = sanitizeInput($_POST['comment_text']);

  // Check if the user is the author of the comment
  $checkOwnershipQuery = "SELECT user_id FROM tblcomments WHERE id = :comment_id";
  $checkStmt = $dbh->prepare($checkOwnershipQuery);
  $checkStmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
  $checkStmt->execute();
  $commentOwner = $checkStmt->fetchColumn();

  if ($commentOwner == $userId) {
    $deleteCommentQuery = "DELETE FROM tblcomments WHERE id = :comment_id";
    $deleteStmt = $dbh->prepare($deleteCommentQuery);
    $deleteStmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);

    if ($deleteStmt->execute()) {
      $response['success'] = true;
      $response['message'] = 'Comment deleted successfully';
      $response['commentId'] = $commentId;
    } else {
      $response['message'] = 'Failed to delete comment';
    }
  } else {
    $response['message'] = 'You do not have permission to delete this comment';
  }
} else {
  $response['message'] = 'Invalid request';
}

echo json_encode($response);
