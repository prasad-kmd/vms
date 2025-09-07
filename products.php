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

// Attempt to select all products with their vendor names
$sql = "SELECT p.ProductID, p.ProductName, p.Price, v.Name AS VendorName
        FROM products p
        JOIN vendors v ON p.VendorID = v.VendorID
        ORDER BY p.ProductName ASC";

$products = [];
if($stmt = $pdo->prepare($sql)){
    if($stmt->execute()){
        if($stmt->rowCount() > 0){
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Manage Products</title>
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
                <li class="nav-item">
                    <a class="nav-link" href="vendors.php">Vendors</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="products.php">Products <span class="sr-only">(current)</span></a>
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
                        <h2 class="float-left">Product Details</h2>
                        <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'Admin'): ?>
                        <a href="add_product.php" class="btn btn-success float-right">Add New Product</a>
                        <?php endif; ?>
                    </div>
                    <?php if(!empty($products)): ?>
                        <table class='table table-bordered table-striped'>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Vendor</th>
                                     <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'Admin'): ?>
                                    <th>Action</th>
                                     <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($products as $product): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($product['ProductID']); ?></td>
                                    <td><?php echo htmlspecialchars($product['ProductName']); ?></td>
                                    <td>$<?php echo htmlspecialchars(number_format($product['Price'], 2)); ?></td>
                                    <td><?php echo htmlspecialchars($product['VendorName']); ?></td>
                                     <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'Admin'): ?>
                                    <td>
                                        <a href='edit_product.php?id=<?php echo $product['ProductID']; ?>' class='btn btn-warning btn-sm'>Edit</a>
                                        <a href='delete_product.php?id=<?php echo $product['ProductID']; ?>' class='btn btn-danger btn-sm'>Delete</a>
                                    </td>
                                     <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class='lead'><em>No products found. Please add one.</em></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
