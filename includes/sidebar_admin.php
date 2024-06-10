<?php
if (isset($_SESSION['userid'])) {
    include('../../config/dbconn.php');

    $userId = $_SESSION['userid'];

    // Fetch admin-specific data from the database
    $sqlAdmin = "SELECT UserName, email, fullname, RoleName, status FROM admin WHERE id = :userId";
    $stmtAdmin = $dbh->prepare($sqlAdmin);
    $stmtAdmin->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmtAdmin->execute();
    $admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);
?>

<aside id="layout-menu" class="layout-menu-horizontal menu menu-horizontal container-fluid flex-grow-0 bg-menu-theme"
    data-bg-class="bg-menu-theme"
    style="touch-action: none; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);">
    <div class="container-xxl d-flex h-100">
        <ul class="menu-inner">
            <!-- Dashboards -->
            <li class="<?php if ($sidebar == 'home') { echo 'menu-item active'; } else { echo 'menu-item'; } ?>">
                <a href="dashboard.php" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-home-circle"></i>
                    <div><?php echo translate("Dashboard"); ?></div>
                </a>
            </li>

            <li class="menu-item">
                <a href="javascript:void(0)" class="menu-link">
                    <i class="menu-icon tf-icons bx bxs-dock-top"></i>
                    <div><?php echo translate("Late Documents"); ?></div>
                </a>
            </li>

            <li class="menu-item">
                <a href="javascript:void(0)" class="menu-link">
                    <i class="menu-icon tf-icons bx bxs-calendar"></i>
                    <div><?php echo translate("Leaves"); ?></div>
                </a>
            </li>

            <!-- Layouts -->
            <li class="<?php if (in_array($sidebar, ['admin_account','all-users-security','regulator','report','alluser', 'allsystem', 'role', 'permmission', 'department', 'office', 'position', 'eposition', 'leavetype', 'eleavetype', 'late', 'elate', 'ealluser'])) { echo 'menu-item active open'; } else { echo 'menu-item'; } ?>">
                <a href="javascript:void(0)"
                    class="<?php if ($sidebar == 'alluser') { echo 'menu-link menu-toggle active'; } else { echo 'menu-link menu-toggle'; } ?>">
                    <i class="menu-icon tf-icons bx bx-layout"></i>
                    <div><?php echo translate("Manage"); ?></div>
                </a>

                <ul class="menu-sub">
                    <li class="<?php if (in_array($sidebar, ['alluser', 'ealluser'])) { echo 'menu-item active'; } else { echo 'menu-item'; } ?>">
                        <a href="all-users.php"
                            class="<?php if (in_array($sidebar, ['alluser', 'ealluser'])) { echo 'menu-link active'; } else { echo 'menu-link'; } ?>">
                            <i class="menu-icon tf-icons bx bxs-user-account"></i>
                            <div><?php echo translate("Manage User Accounts"); ?></div>
                        </a>
                    </li>
                    <li class="<?php if (in_array($sidebar, ['admin_account'])) { echo 'menu-item active'; } else { echo 'menu-item'; } ?>">
                        <a href="admin-account.php"
                            class="<?php if (in_array($sidebar, ['admin_account'])) { echo 'menu-link active'; } else { echo 'menu-link'; } ?>">
                            <i class="menu-icon tf-icons bx bx-user-circle"></i>
                            <div><?php echo translate("Manage Admin Accounts"); ?></div>
                        </a>
                    </li>
                    <li class="<?php if ($sidebar == 'department') { echo 'menu-item active'; } else { echo 'menu-item'; } ?>">
                        <a href="department.php"
                            class="<?php if ($sidebar == 'department') { echo 'menu-link active'; } else { echo 'menu-link'; } ?>">
                            <i class="menu-icon tf-icons bx bx-buildings"></i>
                            <div><?php echo translate("Manage Departments"); ?></div>
                        </a>
                    </li>
                    <li class="<?php if ($sidebar == 'office') { echo 'menu-item active'; } else { echo 'menu-item'; } ?>">
                        <a href="office.php"
                            class="<?php if ($sidebar == 'office') { echo 'menu-link active'; } else { echo 'menu-link'; } ?>">
                            <i class="menu-icon tf-icons bx bx-building-house"></i>
                            <div><?php echo translate("Manage Offices"); ?></div>
                        </a>
                    </li>
                    <li class="<?php if (in_array($sidebar, ['position', 'eposition'])) { echo 'menu-item active'; } else { echo 'menu-item'; } ?>">
                        <a href="position.php"
                            class="<?php if (in_array($sidebar, ['position', 'eposition'])) { echo 'menu-link active'; } else { echo 'menu-link'; } ?>">
                            <i class="menu-icon tf-icons bx bxs-user-badge"></i>
                            <div><?php echo translate("Manage Positions"); ?></div>
                        </a>
                    </li>
                    <li class="<?php if (in_array($sidebar, ['leavetype', 'eleavetype'])) { echo 'menu-item active'; } else { echo 'menu-item';} ?>">
                        <a href="leave-type.php"
                            class="<?php if (in_array($sidebar, ['leavetype', 'eleavetype'])) { echo 'menu-link active'; } else { echo 'menu-link'; } ?>">
                            <i class="menu-icon tf-icons bx bx-calendar-edit"></i>
                            <div><?php echo translate("Manage Leave Types"); ?></div>
                        </a>
                    </li>
                    <li class="<?php if (in_array($sidebar, ['late', 'elate'])) { echo 'menu-item active'; } else { echo 'menu-item'; } ?>">
                        <a href="late.php"
                            class="<?php if (in_array($sidebar, ['late', 'elate'])) { echo 'menu-link active'; } else { echo 'menu-link'; } ?>">
                            <i class="menu-icon tf-icons bx bx-objects-horizontal-left"></i>
                            <div><?php echo translate("Manage Late Types"); ?></div>
                        </a>
                    </li>
                    <li class="<?php if ($sidebar == 'allsystem') { echo 'menu-item active'; } else { echo 'menu-item'; } ?>">
                        <a href="all-system.php"
                            class="<?php if ($sidebar == 'allsystem') { echo 'menu-link active'; } else { echo 'menu-link'; } ?>">
                            <i class="menu-icon tf-icons bx bx-grid"></i>
                            <div><?php echo translate("Manage Systems"); ?></div>
                        </a>
                    </li>
                    <li class="<?php if ($sidebar == 'regulator') { echo 'menu-item active'; } else { echo 'menu-item'; } ?>">
                        <a href="regulator.php"
                            class="<?php if ($sidebar == 'regulator') { echo 'menu-link active'; } else { echo 'menu-link'; } ?>">
                            <i class="menu-icon tf-icons bx bx-file"></i>
                            <div><?php echo translate("Manage Regulators"); ?></div>
                        </a>
                    </li>
                    <li class="<?php if ($sidebar == 'report') { echo 'menu-item active'; } else { echo 'menu-item'; } ?>">
                        <a href="reports.php"
                            class="<?php if ($sidebar == 'report') { echo 'menu-link active'; } else { echo 'menu-link'; } ?>">
                            <i class="menu-icon tf-icons bx bx-file"></i>
                            <div><?php echo translate("Manage Reports"); ?></div>
                        </a>
                    </li>
                    <li class="<?php if (in_array($sidebar, ['role', 'permmission'])) { echo 'menu-item active'; } else { echo 'menu-item'; } ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-check-shield"></i>
                            <div><?php echo translate("Role & Permission"); ?></div>
                        </a>
                        <ul class="<?php if (in_array($sidebar, ['role', 'permmission'])) { echo 'menu-sub active'; } else { echo 'menu-sub'; } ?>">
                            <li class="<?php if ($sidebar == 'role') { echo 'menu-item active'; } else { echo 'menu-item'; } ?>">
                                <a href="role.php"
                                    class="<?php if ($sidebar == 'role') { echo 'menu-link active'; } else { echo 'menu-link'; } ?>">
                                    <div><?php echo translate("Role"); ?></div>
                                </a>
                            </li>
                            <li class="<?php if ($sidebar == 'permmission') { echo 'menu-item active'; } else { echo 'menu-item'; } ?>">
                                <a href="permmission.php"
                                    class="<?php if ($sidebar == 'permmission') { echo 'menu-link active'; } else { echo 'menu-link'; } ?>">
                                    <div><?php echo translate("Permission"); ?></div>
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</aside>
<?php } ?>
