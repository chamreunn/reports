<?php
include('../config/dbconn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['deleteid'])) {
    $deleteId = intval($_POST['deleteid']);

    try {
      $sql = "DELETE FROM tbluser WHERE id = :id";
      $query = $dbh->prepare($sql);
      $query->bindParam(':id', $deleteId, PDO::PARAM_INT);
      $query->execute();

      if ($query->rowCount() > 0) {
        // Record deleted successfully
        sleep(1);
        $msg = "លុបគណនីបានជោគជ័យ។";
        header("Location: ../pages/supperadmin/all-users.php?status=success&msg=" . urlencode($msg));
        exit();
      } else {
        // No record found with the given ID
        sleep(1);
        $error = "មិនមានគណនីនេះទេ";
        header("Location: ../pages/supperadmin/all-users.php?status=error&msg=" . urlencode($error));
        exit();
      }
    } catch (PDOException $e) {
      // Handle error
      sleep(1);
      $error = "មានបញ្ហាលើបច្ចេកទេស";
      header("Location: ../pages/supperadmin/all-users.php?status=error&msg=" . urlencode($error));
      exit();
    }
  } else {
    // ID parameter not set
    sleep(1);
    $error = "រកមិនឃើញគណនី";
    header("Location: ../pages/supperadmin/all-users.php?status=error&msg=" . urlencode($error));
    exit();
  }
}
