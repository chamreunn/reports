<?php
session_start();
include('../../config/dbconn.php');

// Function to sanitize input
function sanitizeInput($input)
{
    return htmlspecialchars(trim($input));
}

$response = ['success' => false, 'message' => '', 'commentHtml' => ''];

if (isset($_POST['new_comment']) && isset($_POST['comment_id'])) {
  $userId = $_SESSION['userid'];
  $commentId = sanitizeInput($_POST['comment_id']);
  $newComment = sanitizeInput($_POST['new_comment']);

  if (!empty($newComment)) {
    // Check if the user is the author of the comment
    $checkOwnershipQuery = "SELECT user_id FROM tblcomments WHERE id = :comment_id";
    $checkStmt = $dbh->prepare($checkOwnershipQuery);
    $checkStmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
    $checkStmt->execute();
    $commentOwner = $checkStmt->fetchColumn();

    if ($commentOwner == $userId) {
      $updateCommentQuery = "UPDATE tblcomments SET comment = :new_comment WHERE id = :comment_id";
      $updateStmt = $dbh->prepare($updateCommentQuery);
      $updateStmt->bindParam(':new_comment', $newComment, PDO::PARAM_STR);
      $updateStmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);

      if ($updateStmt->execute()) {
        $fetchCommentQuery = "SELECT c.*, u.FirstName, u.LastName, u.Profile
                                      FROM tblcomments c
                                      LEFT JOIN tbluser u ON c.user_id = u.id
                                      WHERE c.id = :comment_id";
        $fetchCommentStmt = $dbh->prepare($fetchCommentQuery);
        $fetchCommentStmt->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
        $fetchCommentStmt->execute();
        $comment = $fetchCommentStmt->fetch(PDO::FETCH_ASSOC);

        if ($comment) {
          $profilePic = ($comment['Profile']) ? $comment['Profile'] : 'default-profile.jpg';
          $utcDate = new DateTime($comment['created_at'], new DateTimeZone('UTC'));
          $utcDate->setTimezone(new DateTimeZone('Asia/Bangkok'));
          $localDate = $utcDate->format('M j, Y h:i A');

          $commentHtml = "
                    <div class='mb-3 comment-container comment-left' id='comment-{$comment['id']}'>
                        <div class='d-flex align-items-center mb-2'>
                            <img src='{$profilePic}' alt='User Avatar' class='avatar me-2 rounded-circle' style='object-fit: cover; width: 50px; height: 50px;'>
                            <p><strong>{$comment['FirstName']} {$comment['LastName']}</strong></p>
                        </div>
                        <div class='flex-grow-1 border-bottom'>
                            <div class='d-flex align-items-center'>
                                <p class='mb-0'>Comment: {$comment['comment']}</p>
                                <a href='#editForm{$comment['id']}' class='btn btn-sm btn-link ms-2' role='button' data-bs-toggle='collapse' aria-expanded='false' aria-controls='editForm{$comment['id']}'><i class='bx bxs-edit-alt' data-bs-toggle='tooltip' data-bs-placement='top' title='Edit Comment'></i></a>
                                <div class='dropdown'>
                                    <button class='btn btn-sm btn-link mx-2 dropdown-toggle' type='button' id='dropdownDelete{$comment['id']}' data-bs-toggle='dropdown' aria-expanded='false'>
                                        <i class='bx bx-trash text-danger' data-bs-toggle='tooltip' data-bs-placement='top' title='Delete Comment'></i>
                                    </button>
                                    <ul class='dropdown-menu dropdown-menu-end' aria-labelledby='dropdownDelete{$comment['id']}'>
                                        <li>
                                            <form class='p-4' method='post' onsubmit='return deleteComment(event, \"{$comment['id']}\", \"{$comment['comment']}\")'>
                                                <input type='hidden' name='comment_id' value='{$comment['id']}'>
                                                <input type='hidden' name='comment_text' value='" . htmlspecialchars($comment['comment']) . "'>
                                                <button type='submit' name='delete_comment' class='btn btn-sm btn-danger'>Delete</button>
                                                <button type='button' class='btn btn-sm btn-outline-secondary' data-bs-toggle='dropdown' aria-expanded='false'>Cancel</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <p class='text-muted'>Posted on {$localDate}</p>

                            <div class='collapse mt-3 mb-3' id='editForm{$comment['id']}'>
                                <form method='post' onsubmit='return updateComment(event, \"{$comment['id']}\")'>
                                    <input type='hidden' name='comment_id' value='{$comment['id']}'>
                                    <textarea name='new_comment' class='form-control mb-2' rows='2' placeholder='Edit your comment'>{$comment['comment']}</textarea>
                                    <button type='submit' name='update_comment' class='btn btn-sm btn-primary'>Save</button>
                                </form>
                            </div>
                        </div>
                    </div>";

          $response['success'] = true;
          $response['message'] = 'Comment updated successfully';
          $response['commentHtml'] = $commentHtml;
        } else {
          $response['message'] = 'Failed to fetch updated comment';
        }
      } else {
        $response['message'] = 'Failed to update comment';
      }
    } else {
      $response['message'] = 'You do not have permission to update this comment';
    }
  } else {
    $response['message'] = 'Comment cannot be empty';
  }
} else {
  $response['message'] = 'Invalid request';
}

echo json_encode($response);
