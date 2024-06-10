<?php
session_start(); // Start session at the beginning of the script
require __DIR__ . '/../config/dbconn.php'; // Include database connection
require __DIR__ . '/../models/UserModel.php'; // Include UserModel
require __DIR__ . '/../models/SystemSettingsModel.php'; // Include SystemSettingsModel

class AuthController
{
    private $userModel;
    private $systemSettingsModel;

    public function __construct($dbh)
    {
        $this->userModel = new UserModel($dbh); // Initialize UserModel
        $this->systemSettingsModel = new SystemSettingsModel($dbh); // Initialize SystemSettingsModel
    }

    public function login()
    {
        $settings = $this->systemSettingsModel->getSystemSettings(); // Get system settings

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $loginType = $_POST['login_type'];
            if ($loginType == 'login') {
                if (!empty($_POST["username"]) && !empty($_POST["password"])) {
                    $username = $_POST["username"];
                    $password = $_POST["password"];
                    $user = $this->userModel->getUserByUsername($username); // Get user by username
                    $admin = $this->userModel->getAdminByUsername($username); // Get admin by username

                    $account = $user ?: $admin;
                    $accountType = $user ? 'user' : ($admin ? 'supperadmin' : null);

                    if ($account) {
                        if ($account['Status'] == 'locked') {
                            sleep(1);
                            header('Location: /test/Leaves/views/account-locked.php'); // Redirect to account locked page
                            exit;
                        } elseif (password_verify($password, $account['Password'])) {
                            if (isset($account['authenticator_enabled']) && $account['authenticator_enabled'] == 1) {
                                $_SESSION['temp_userid'] = $account['id'];
                                $_SESSION['temp_username'] = $account['UserName'];
                                $_SESSION['temp_role'] = $account['RoleName'];
                                $_SESSION['temp_usertype'] = $accountType;
                                $_SESSION['temp_secret'] = $account['TwoFASecret'];
                                sleep(1);
                                header('Location: /test/Leaves/views/2fa.php'); // Redirect to 2FA verification page
                                exit;
                            } else {
                                $_SESSION['userid'] = $account['id'];
                                $_SESSION['role'] = $account['RoleName'];
                                $_SESSION['username'] = $account['Honorific'] . " " . $account['FirstName'] . " " . $account['LastName'];

                                $roleToDashboard = [
                                    'ប្រធានអង្គភាព' => '../views/admin/dashboard.php',
                                    'អនុប្រធានអង្គភាព' => '../views/admin/dashboard.php',
                                    'ប្រធាននាយកដ្ឋាន' => '../views/manager/dashboard.php',
                                    'អនុប្រធាននាយកដ្ឋាន' => '../views/manager/dashboard.php',
                                    'ប្រធានការិយាល័យ' => '../views/office_manager/dashboard.php',
                                    'អនុប្រធានការិយាល័យ' => '../views/office_manager/dashboard.php',
                                    'supperadmin' => '../views/supperadmin/dashboard.php'
                                ];

                                $role = $_SESSION['role'];
                                if (isset($roleToDashboard[$role])) {
                                    header('Location: ' . $roleToDashboard[$role]); // Redirect to appropriate dashboard
                                } else {
                                    header('Location: /test/Leaves/views/user/dashboard.php'); // Redirect to default user dashboard
                                }
                                exit;
                            }
                        } else {
                            $_SESSION['error'] = 'ឈ្មោះមន្ត្រី ឬ ពាក្យសម្ងាត់ មិនត្រឹមត្រូវ'; // Store error message in session
                            header('Location: /test/Leaves/views/auth/login.php'); // Redirect to login page
                            exit;
                        }
                    } else {
                        $_SESSION['error'] = 'ឈ្មោះមន្ត្រី ឬ ពាក្យសម្ងាត់ មិនត្រឹមត្រូវ'; // Store error message in session
                        header('Location: /test/Leaves/views/auth/login.php'); // Redirect to login page
                        exit;
                    }
                } else {
                    $_SESSION['error'] = 'សូមបញ្ចូលឈ្មោះមន្ត្រី និង ពាក្យសម្ងាត់'; // Store error message in session
                    header('Location: /test/Leaves/views/auth/login.php'); // Redirect to login page
                    exit;
                }
            } else {
                $_SESSION['error'] = "ប្រភេទការចូលមិនត្រឹមត្រូវ"; // Store error message in session
                header('Location: /test/Leaves/views/auth/login.php'); // Redirect to login page
                exit;
            }
        } else {
            include __DIR__ . '/../views/auth/login.php'; // Include login page
        }
    }
}

// Initialize database connection and controller
$authController = new AuthController($dbh);
$authController->login();
?>