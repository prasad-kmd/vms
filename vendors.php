<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config/db.php";

// Attempt to select all vendors
$sql = "SELECT * FROM vendors ORDER BY Name";
$vendors = [];
if($stmt = $pdo->prepare($sql)){
    if($stmt->execute()){
        if($stmt->rowCount() > 0){
            $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }
    // Close statement
    unset($stmt);
}
// Close connection
unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Vendors</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 80%;
            margin: 0 auto;
        }
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">Vendor Management</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="vendors.php">Vendors <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="purchase_orders.php">Purchase Orders</a>
                </li>
                <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'Admin'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="users.php">User Management</a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="profile.php" class="btn btn-info">My Profile</a>
                </li>
                <li class="nav-item ml-2">
                    <a href="logout.php" class="btn btn-danger">Sign Out</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header clearfix mt-4">
                        <h2 class="float-left">Vendor Details</h2>
                        <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'Admin'): ?>
                        <a href="add_vendor.php" class="btn btn-success float-right">Add New Vendor</a>
                        <?php endif; ?>
                    </div>
                    <?php
                    // Display error message from session if it exists
                    if(isset($_SESSION['error_message']) && !empty($_SESSION['error_message'])){
                        echo '<div class="alert alert-danger mt-3">' . $_SESSION['error_message'] . '</div>';
                        // Unset the session variable so it doesn't show again
                        unset($_SESSION['error_message']);
                    }
                    ?>
                    <?php
                    if(!empty($vendors)): ?>
                        <table class='table table-bordered table-striped'>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Contact Info</th>
                                    <th>Email</th>
                                    <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'Admin'): ?>
                                    <th>Action</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($vendors as $vendor): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($vendor['VendorID']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['Name']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['ContactInfo']); ?></td>
                                    <td><?php echo htmlspecialchars($vendor['Email']); ?></td>
                                    <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'Admin'): ?>
                                    <td>
                                        <a href='edit_vendor.php?id=<?php echo $vendor['VendorID']; ?>' class='btn btn-warning btn-sm'>Edit</a>
                                        <a href='delete_vendor.php?id=<?php echo $vendor['VendorID']; ?>' class='btn btn-danger btn-sm'>Delete</a>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class='lead'><em>No vendors found. Please add one.</em></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
