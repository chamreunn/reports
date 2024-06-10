<?php
// models/UserModel.php
class UserModel
{
  private $dbh;

  public function __construct($dbh)
  {
    $this->dbh = $dbh;
  }

  public function getUserByUsername($username)
  {
    $query = "SELECT u.id, u.UserName, u.Password, u.Status, u.authenticator_enabled, u.TwoFASecret,
                       u.Honorific, u.FirstName, u.LastName, r.RoleName
                FROM tbluser u
                INNER JOIN tblrole r ON u.RoleId = r.id
                WHERE u.UserName = :username";
    $stmt = $this->dbh->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getAdminByUsername($username)
  {
    $query = "SELECT * FROM admin WHERE UserName = :username";
    $stmt = $this->dbh->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
  }
}
