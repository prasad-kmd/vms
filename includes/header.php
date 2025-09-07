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
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Custom CSS -->
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-light border-right" id="sidebar-wrapper">
            <div class="sidebar-heading">VMS</div>
            <div class="list-group list-group-flush">
                <a href="index.php" class="list-group-item list-group-item-action <?php echo $is_active('index.php'); ?>">Dashboard</a>
                <a href="vendors.php" class="list-group-item list-group-item-action <?php echo $is_active('vendors.php'); ?>">Vendors</a>
                <a href="products.php" class="list-group-item list-group-item-action <?php echo $is_active('products.php'); ?>">Products</a>
                <a href="purchase_orders.php" class="list-group-item list-group-item-action <?php echo $is_active('purchase_orders.php'); ?>">Purchase Orders</a>
                <?php if ($is_admin): ?>
                    <a href="users.php" class="list-group-item list-group-item-action <?php echo $is_active('users.php'); ?>">User Management</a>
                <?php endif; ?>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php echo htmlspecialchars($_SESSION["username"]); ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="profile.php">My Profile</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php">Sign Out</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="container-fluid">
                <!-- Content goes here -->
