<?php

class SystemSettingsModel
{
  private $dbh;

  public function __construct($dbh)
  {
    $this->dbh = $dbh;
  }

  public function getSystemSettings()
  {
    try {
      $sql = "SELECT * FROM tblsystemsettings";
      $stmt = $this->dbh->query($sql);

      if ($stmt->rowCount() > 0) {
        return $stmt->fetch(PDO::FETCH_ASSOC);
      } else {
        return [
          'system_name' => 'Default System Name',
          'icon_path' => 'assets/img/avatars/no-image.jpg',
          'cover_path' => 'assets/img/pages/profile-banner.png'
        ];
      }
    } catch (PDOException $e) {
      echo "Connection failed: " . $e->getMessage();
      return false;
    }
  }
}
