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

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-dolly-flatbed"></i> VMS
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item <?php echo $is_active('index.php'); ?>">
                    <a class="nav-link" href="index.php">Dashboard</a>
                </li>
                <li class="nav-item <?php echo $is_active('vendors.php'); ?>">
                    <a class="nav-link" href="vendors.php">Vendors</a>
                </li>
                <li class="nav-item <?php echo $is_active('products.php'); ?>">
                    <a class="nav-link" href="products.php">Products</a>
                </li>
                <li class="nav-item <?php echo $is_active('purchase_orders.php'); ?>">
                    <a class="nav-link" href="purchase_orders.php">Purchase Orders</a>
                </li>
                <li class="nav-item <?php echo $is_active('about.php'); ?>">
                    <a class="nav-link" href="about.php">About</a>
                </li>
                <?php if ($is_admin): ?>
                    <li class="nav-item <?php echo $is_active('users.php'); ?>">
                        <a class="nav-link" href="users.php">Users</a>
                    </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                 <li class="nav-item">
                    <a class="nav-link" href="profile.php">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION["username"]); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="btn btn-outline-light ml-2">
                        <i class="fas fa-sign-out-alt"></i> Sign Out
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="main-content">
    <div class="container-fluid pt-4">
        <!-- Content goes here -->
