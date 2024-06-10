<?php
// Database connection parameters
$host = 'localhost'; // Hostname
$dbname = 'report_system'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password

try {
  // Create a PDO instance
  $dbh = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

  // Set PDO error mode to exception
  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Optionally, set character encoding
  $dbh->exec("SET NAMES 'utf8'");
} catch (PDOException $e) {
  // Handle connection errors
  echo "Connection failed: " . $e->getMessage();
}
