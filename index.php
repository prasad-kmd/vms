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

// Define variables for stats
$vendor_count = $product_count = $po_count = 0;
$po_total_value = 0.00;
$recent_pos = [];
$user_id = $_SESSION['id'];
$is_admin = ($_SESSION['role'] === 'Admin');

try {
    if ($is_admin) {
        // Admin stats (site-wide)
        $vendor_count = $pdo->query("SELECT count(*) FROM vendors")->fetchColumn();
        $product_count = $pdo->query("SELECT count(*) FROM products")->fetchColumn();
        $po_count = $pdo->query("SELECT count(*) FROM purchase_orders")->fetchColumn();
        $po_total_value = $pdo->query("SELECT SUM(TotalAmount) FROM purchase_orders")->fetchColumn();

        // Recent POs for Admin
        $sql_recent_pos = "SELECT po.PurchaseOrderID, v.Name AS VendorName, po.OrderDate, po.TotalAmount
                           FROM purchase_orders po
                           JOIN vendors v ON po.VendorID = v.VendorID
                           ORDER BY po.OrderDate DESC, po.PurchaseOrderID DESC
                           LIMIT 5";
        $stmt_recent = $pdo->prepare($sql_recent_pos);
    } else {
        // User stats
        // Users can still see total vendors and products
        $vendor_count = $pdo->query("SELECT count(*) FROM vendors")->fetchColumn();
        $product_count = $pdo->query("SELECT count(*) FROM products")->fetchColumn();

        $sql_user_po = "SELECT count(*), SUM(TotalAmount) FROM purchase_orders WHERE UserID = :user_id";
        $stmt_user_po = $pdo->prepare($sql_user_po);
        $stmt_user_po->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt_user_po->execute();
        list($po_count, $po_total_value) = $stmt_user_po->fetch(PDO::FETCH_NUM);

        // Recent POs for User
        $sql_recent_pos = "SELECT po.PurchaseOrderID, v.Name AS VendorName, po.OrderDate, po.TotalAmount
                           FROM purchase_orders po
                           JOIN vendors v ON po.VendorID = v.VendorID
                           WHERE po.UserID = :user_id
                           ORDER BY po.OrderDate DESC, po.PurchaseOrderID DESC
                           LIMIT 5";
        $stmt_recent = $pdo->prepare($sql_recent_pos);
        $stmt_recent->bindParam(":user_id", $user_id, PDO::PARAM_INT);
    }

    // Execute recent POs query
    $stmt_recent->execute();
    $recent_pos = $stmt_recent->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // For simplicity, we'll just show an error message. In a real app, log this.
    $dashboard_error = "Error fetching dashboard data: " . $e->getMessage();
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="index.php">Vendor Management</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="vendors.php">Vendors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="purchase_orders.php">Purchase Orders</a>
                </li>
                <?php if($is_admin): ?>
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

    <div class="container mt-4">
        <div class="page-header">
            <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to the dashboard.</h1>
        </div>

        <?php if(isset($dashboard_error)): ?>
            <div class="alert alert-danger"><?php echo $dashboard_error; ?></div>
        <?php else: ?>
            <!-- Stats Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-white bg-primary mb-3">
                        <div class="card-header">Total Vendors</div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $vendor_count; ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-secondary mb-3">
                        <div class="card-header">Total Products</div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $product_count; ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-header"><?php echo $is_admin ? 'Total Purchase Orders' : 'My Purchase Orders'; ?></div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $po_count; ?></h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-dark mb-3">
                        <div class="card-header"><?php echo $is_admin ? 'Total PO Value' : 'My PO Value'; ?></div>
                        <div class="card-body">
                            <h5 class="card-title">$<?php echo number_format($po_total_value ?? 0, 2); ?></h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Purchase Orders -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <h3>Recent Purchase Orders</h3>
                    <?php if(!empty($recent_pos)): ?>
                        <table class="table table-bordered table-striped">
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
                                <?php foreach($recent_pos as $po): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($po['PurchaseOrderID']); ?></td>
                                    <td><?php echo htmlspecialchars($po['VendorName']); ?></td>
                                    <td><?php echo htmlspecialchars($po['OrderDate']); ?></td>
                                    <td>$<?php echo htmlspecialchars(number_format($po['TotalAmount'], 2)); ?></td>
                                    <td>
                                        <a href='view_purchase_order.php?id=<?php echo $po['PurchaseOrderID']; ?>' class='btn btn-info btn-sm'>View</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="lead"><em>No recent purchase orders found.</em></p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
