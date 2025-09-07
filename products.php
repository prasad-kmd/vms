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

// Fetch vendors for the filter dropdown
$vendors = [];
$sql_vendors = "SELECT VendorID, Name FROM vendors ORDER BY Name ASC";
if($stmt_vendors = $pdo->prepare($sql_vendors)){
    if($stmt_vendors->execute()){
        $vendors = $stmt_vendors->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($stmt_vendors);
}

// Define variables
$products = [];
$selected_vendor = isset($_GET['vendor_id']) ? $_GET['vendor_id'] : '';

// Base SQL query
$sql = "SELECT p.ProductID, p.ProductName, p.Price, v.Name AS VendorName
        FROM products p
        JOIN vendors v ON p.VendorID = v.VendorID";

// Append WHERE clause if a vendor is selected
$params = [];
if(!empty($selected_vendor)){
    $sql .= " WHERE p.VendorID = :vendor_id";
    $params[':vendor_id'] = $selected_vendor;
}

$sql .= " ORDER BY p.ProductName ASC";

// Attempt to execute the query
if($stmt = $pdo->prepare($sql)){
    if($stmt->execute($params)){
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }
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

                    <!-- Filter Form -->
                    <form action="products.php" method="get" class="form-inline mb-3">
                        <div class="form-group mr-2">
                            <label for="vendor_id" class="mr-2">Filter by Vendor:</label>
                            <select name="vendor_id" id="vendor_id" class="form-control">
                                <option value="">All Vendors</option>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?php echo $vendor['VendorID']; ?>" <?php echo ($selected_vendor == $vendor['VendorID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($vendor['Name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="products.php" class="btn btn-secondary ml-2">Reset</a>
                    </form>

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
                        <p class='lead'><em>No products found.</em></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
