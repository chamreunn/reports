<?php
// admin/functions.php

function markNotificationsAsRead($userId) {
  global $dbh;

  $sql = "UPDATE notifications SET is_read=TRUE WHERE user_id=:user_id";
  $query = $dbh->prepare($sql);
  $query->bindParam(':user_id', $userId);
  $query->execute();
}

function getNotifications($userId) {
  global $dbh;

  $sql = "SELECT * FROM notifications WHERE user_id=:user_id AND is_read=FALSE";
  $query = $dbh->prepare($sql);
  $query->bindParam(':user_id', $userId);
  $query->execute();

  return $query->fetchAll(PDO::FETCH_ASSOC);
}
