<?php
include('../../config/dbconn.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the form is submitted
    if (isset($_POST['request_id']) && isset($_POST['action'])) {
        $requestId = $_POST['request_id'];
        $action = $_POST['action'];
        $adminId = $_POST['adminid'];

        try {
            // Update request status based on action (approve or reject)
            if ($action === 'approve_request') {
                $status = 'approved';
            } elseif ($action === 'reject_request') {
                $status = 'rejected';
            }
            // Update request status in the database
            $updateSql = "UPDATE tblrequest SET status = :status,approved_by = :admin_id WHERE id = :request_id";
            $updateStmt = $dbh->prepare($updateSql);
            $updateStmt->bindParam(':status', $status);
            $updateStmt->bindParam(':admin_id', $adminId);
            $updateStmt->bindParam(':request_id', $requestId);
            $updateStmt->execute();

            // Get user ID associated with the request
            $userIdSql = "SELECT user_id FROM tblrequest WHERE id = :request_id";
            $userIdStmt = $dbh->prepare($userIdSql);
            $userIdStmt->bindParam(':request_id', $requestId);
            $userIdStmt->execute();
            $userId = $userIdStmt->fetchColumn();

            // Send notification to the user
            $notificationMessage = "Your request has been " . ucfirst($status);
            $notificationSql = "INSERT INTO notifications (user_id, message, request_id) VALUES (:user_id, :message, :request_id)";
            $notificationStmt = $dbh->prepare($notificationSql);
            $notificationStmt->bindParam(':user_id', $userId);
            $notificationStmt->bindParam(':message', $notificationMessage);
            $notificationStmt->bindParam(':request_id', $requestId);
            $notificationStmt->execute();

            // Redirect back to the view_notification.php page with a success message
            header("Location: view_notification.php?id=$notification_id&status=success");
            exit();
        } catch (PDOException $e) {
            // Handle database error
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
