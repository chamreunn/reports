<?php
session_start();
// Destroy all session data
$_SESSION = array();

if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(
    session_name(),
    '',
    time() - 60 * 60,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]
  );
}

unset($_SESSION['userid']);
unset($_SESSION['managerid']);
unset($_SESSION['adminid']);
session_destroy();

// Redirect to the login page after 2 seconds
echo "<style>";
echo "body {";
echo "  display: flex;";
echo "  justify-content: center;";
echo "  align-items: center;";
echo "  height: 100vh;";
echo "  background-color: white;";
echo "}";
echo "h1 {";
echo "  font-weight: bold;";
echo "}";
echo "</style>";

echo "<h1 id='logoutText'>Logging out...</h1>";

echo "<script>";
echo "setTimeout(function() {";
echo "  document.getElementById('logoutText').innerText = 'Logged out';";
echo "}, 3000);"; // Change text after 5 seconds
echo "setTimeout(function() {";
echo "  window.location.href = '../index.php';";
echo "}, 5000);"; // Redirect after 7 seconds
echo "</script>";
// You can also display a message or perform other actions before redirecting
