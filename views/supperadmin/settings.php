<?php
session_start();
include('../../config/dbconn.php');

// Redirect to index page if the user is not authenticated
if (!isset($_SESSION['userid'])) {
  header('Location: ../../index.php');
  exit();
}

include('../../includes/translate.php');
include('../../controllers/form_process.php');
// Fetch default language from the database
$default_language = "kh"; // Default to English if not found in the database

try {
    // Retrieve existing data if available
    $sql = "SELECT * FROM tblsystemsettings";
    $result = $dbh->query($sql);

    if ($result->rowCount() > 0) {
        // Fetch data and pre-fill the form fields
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $system_name = $row["system_name"];
        // Assuming icon and cover paths are stored in the database
        $icon_path = $row["icon_path"];
        $cover_path = $row["cover_path"];
        $default_language = $row["default_language"];
    } else {
        // If no data available, set default values
        $system_name = "";
        $icon_path = "../../assets/img/avatars/no-image.jpg";
        $cover_path = "../../assets/img/pages/profile-banner.png";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
$pageTitle = translate("System Settings");
$sidebar = "settings";
ob_start(); // Start output buffering
?>
<h5 class="mb-3"><?php echo translate("System Settings"); ?></h5>
<form id="formAuthentication" onsubmit="submitForm()" class="mb-3" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="login_type" value="setting-system">
    <!-- System Name -->
    <div class="row mt-0">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header mb-3 border-bottom">
                    <h6 class="mb-0"><i class='bx bxs-business me-2 mx-0 bg-label-primary rounded-circle p-2 mb-0'></i><?php echo translate("System Name"); ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="col-12 fv-plugins-icon-container">
                        <label class="form-label" for="systemname"><?php echo translate("Full Name"); ?></label>
                        <input type="text" id="systemname" class="form-control" placeholder="John Doe" name="systemname"
                            value="<?php echo htmlspecialchars($system_name); ?>">
                        <div
                            class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Logo System -->
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header mb-3 border-bottom">
                    <h6 class="mb-0"><i class='bx bxs-business mx-0 me-2 bg-label-primary rounded-circle p-2 mb-0'></i><?php echo translate("Logo System"); ?></h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img src="<?php echo htmlspecialchars($icon_path); ?>" alt="user-avatar"
                            class="d-block rounded-circle" height="100" width="100" id="uploadedAvatar"
                            style="object-fit: cover;">
                        <div class="button-wrapper">
                            <label for="uploadIcon" class="btn btn-outline-primary me-2 mb-4" tabindex="0">
                                <span class="d-none d-sm-block"><?php echo translate("Upload new icon"); ?></span>
                                <i class="bx bx-upload d-block d-sm-none"></i>
                                <input type="file" name="iconfile" id="uploadIcon" class="account-file-input" hidden=""
                                    accept="image/png, image/jpeg">
                            </label>
                            <button type="button" class="btn btn-label-secondary account-image-reset mb-4">
                                <i class="bx bx-reset d-block d-sm-none"></i>
                                <span class="d-none d-sm-block"><?php echo translate("Reset"); ?></span>
                            </button>
                            <p class="text-muted mb-0"><?php echo translate("Allowed JPG, GIF, or PNG. Max size of 800K"); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Cover Picture -->
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center mb-3 border-bottom">
                    <h6 class="mb-0"><i
                            class='bx bxs-business mx-0 me-2 bg-label-primary rounded-circle p-2 mb-0'></i><?php echo translate("Cover System"); ?>
                    </h6>
                    <label for="uploadCover" class="btn btn-outline-primary" tabindex="0">
                        <span class="d-none d-sm-block"><i class="bx bx-photo-album me-2"></i><?php echo translate("Upload Cover"); ?></span>
                        <i class="bx bx-upload d-block d-sm-none"></i>
                        <input type="file" id="uploadCover" name="coverfile" class="account-file-input" hidden
                            accept="image/png, image/jpeg" onchange="displaySelectedCover(this)">
                    </label>
                </div>
                <div class="card-body border-bottom">
                    <div class="user-profile-header-banner text-center">
                        <label for="uploadCover" class="upload-cover-label">
                            <img src="<?php echo htmlspecialchars($cover_path); ?>" alt="Banner image" class="rounded"
                                id="uploadedCover" style="width: 100%;height: 40vh; object-fit: cover;">
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <!-- Default Language -->
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header mb-3 border-bottom">
                    <h6 class="mb-0"><i class='bx bx-globe me-2 mx-0 bg-label-primary rounded-circle p-2 mb-0'></i><?php echo translate("Default Language"); ?>
                    </h6>
                </div>
                <div class="card-body">
                    <div class="col-12 fv-plugins-icon-container">
                        <label class="form-label" for="defaultLanguage"><?php echo translate("Language"); ?></label>
                        <select id="defaultLanguage" class="form-select select2" name="defaultLanguage">
                            <option value="en" <?php echo ($default_language == 'en') ? 'selected' : ''; ?>><?php echo translate("English"); ?></option>
                            <option value="kh" <?php echo ($default_language == 'kh') ? 'selected' : ''; ?>><?php echo translate("ភាសាខ្មែរ"); ?></option>
                            <!-- Add more language options as needed -->
                        </select>
                        <div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Submit Button -->
    <div class="row">
        <div class="col-12 text-end">
            <button class="btn btn-primary mx-2"><?php echo translate("Save Settings"); ?></button>
        </div>
    </div>
</form>
<script>
    // Function to remove success message parameters from URL without reloading the page
    function removeSuccessMessage() {
        var url = window.location.href;
        // Check if URL contains success message parameters
        if (url.includes("status=success") && url.includes("msg=")) {
            // Remove status and msg parameters from URL
            var newUrl = url.split("?")[0];
            window.history.replaceState({}, document.title, newUrl);
        }
    }

    // Call the function when the page loads
    window.onload = function () {
        removeSuccessMessage();
    };
</script>
<?php $content = ob_get_clean(); ?>
<?php include('../../includes/layout.php'); ?>
