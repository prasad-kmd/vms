<?php
// This is a simplified check. In a real-world scenario, you might have more complex logic.
$is_active = function($page_name) {
    // Returns 'active' if the current page matches, otherwise returns an empty string.
    return basename($_SERVER['PHP_SELF']) == $page_name ? 'active' : '';
};

$is_admin = isset($_SESSION["role"]) && $_SESSION["role"] === 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Vendor Management System'; ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Custom CSS -->
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-light border-right" id="sidebar-wrapper">
            <div class="sidebar-heading">
                <i class="fas fa-dolly-flatbed"></i> VMS
            </div>
            <div class="list-group list-group-flush">
                <a href="index.php" class="list-group-item list-group-item-action <?php echo $is_active('index.php'); ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="vendors.php" class="list-group-item list-group-item-action <?php echo $is_active('vendors.php'); ?>">
                    <i class="fas fa-users"></i> Vendors
                </a>
                <a href="products.php" class="list-group-item list-group-item-action <?php echo $is_active('products.php'); ?>">
                    <i class="fas fa-box"></i> Products
                </a>
                <a href="purchase_orders.php" class="list-group-item list-group-item-action <?php echo $is_active('purchase_orders.php'); ?>">
                    <i class="fas fa-file-invoice-dollar"></i> Purchase Orders
                </a>
                <?php if ($is_admin): ?>
                    <a href="users.php" class="list-group-item list-group-item-action <?php echo $is_active('users.php'); ?>">
                        <i class="fas fa-user-cog"></i> User Management
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
                <button class="btn btn-primary" id="menu-toggle"><i class="fas fa-bars"></i></button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION["username"]); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="logout.php" class="btn btn-outline-danger ml-2">
                                <i class="fas fa-sign-out-alt"></i> Sign Out
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid pt-4">
                <!-- Content goes here -->
