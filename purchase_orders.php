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

// Fetch products for the filter dropdown
$products = [];
$sql_products = "SELECT ProductID, ProductName FROM products ORDER BY ProductName ASC";
if($stmt_products = $pdo->prepare($sql_products)){
    if($stmt_products->execute()){
        $products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($stmt_products);
}


// Define variables
$purchase_orders = [];
$selected_vendor = isset($_GET['vendor_id']) ? $_GET['vendor_id'] : '';
$selected_product = isset($_GET['product_id']) ? $_GET['product_id'] : '';

// Base SQL query
$sql = "SELECT po.PurchaseOrderID, po.OrderDate, po.TotalAmount, v.Name AS VendorName
        FROM purchase_orders po
        JOIN vendors v ON po.VendorID = v.VendorID";

// Dynamically build WHERE clause
$where_clauses = [];
$params = [];

// Filter by role
if($_SESSION["role"] !== 'Admin'){
    $where_clauses[] = "po.UserID = :user_id";
    $params[':user_id'] = $_SESSION["id"];
}

// Filter by vendor
if(!empty($selected_vendor)){
    $where_clauses[] = "po.VendorID = :vendor_id";
    $params[':vendor_id'] = $selected_vendor;
}

// Filter by product
if(!empty($selected_product)){
    $where_clauses[] = "po.PurchaseOrderID IN (SELECT PurchaseOrderID FROM purchase_order_items WHERE ProductID = :product_id)";
    $params[':product_id'] = $selected_product;
}


if(count($where_clauses) > 0){
    $sql .= " WHERE " . implode(' AND ', $where_clauses);
}

$sql .= " ORDER BY po.OrderDate DESC";

// Attempt to execute the query
if($stmt = $pdo->prepare($sql)){
    if($stmt->execute($params)){
        $purchase_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }
    unset($stmt);
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Purchase Orders</title>
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
                <li class="nav-item">
                    <a class="nav-link" href="products.php">Products</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="purchase_orders.php">Purchase Orders <span class="sr-only">(current)</span></a>
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
                        <h2 class="float-left">Purchase Order Details</h2>
                        <a href="add_purchase_order.php" class="btn btn-success float-right">Add New Purchase Order</a>
                    </div>

                    <!-- Filter Form -->
                    <form action="purchase_orders.php" method="get" class="form-inline mb-3">
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
                        <div class="form-group mr-2">
                            <label for="product_id" class="mr-2">Filter by Product:</label>
                            <select name="product_id" id="product_id" class="form-control">
                                <option value="">All Products</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['ProductID']; ?>" <?php echo ($selected_product == $product['ProductID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($product['ProductName']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="purchase_orders.php" class="btn btn-secondary ml-2">Reset</a>
                    </form>

                    <?php if(!empty($purchase_orders)): ?>
                        <table class='table table-bordered table-striped'>
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Vendor</th>
                                    <th>Order Date</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($purchase_orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['PurchaseOrderID']); ?></td>
                                    <td><?php echo htmlspecialchars($order['VendorName']); ?></td>
                                    <td><?php echo htmlspecialchars($order['OrderDate']); ?></td>
                                    <td>$<?php echo htmlspecialchars(number_format($order['TotalAmount'], 2)); ?></td>
                                    <td>
                                        <a href='view_purchase_order.php?id=<?php echo $order['PurchaseOrderID']; ?>' class='btn btn-info btn-sm'>View</a>
                                        <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'Admin'): ?>
                                        <a href='delete_purchase_order.php?id=<?php echo $order['PurchaseOrderID']; ?>' class='btn btn-danger btn-sm'>Delete</a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class='lead'><em>No purchase orders found matching your criteria.</em></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
