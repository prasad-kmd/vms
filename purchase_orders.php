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

// Base SQL query
$sql = "SELECT po.PurchaseOrderID, po.OrderDate, po.TotalAmount, v.Name AS VendorName
        FROM purchase_orders po
        JOIN vendors v ON po.VendorID = v.VendorID";

// If the user is not an admin, only show their own orders
if($_SESSION["role"] !== 'Admin'){
    $sql .= " WHERE po.UserID = :user_id";
}

$sql .= " ORDER BY po.OrderDate DESC";

$purchase_orders = [];
if($stmt = $pdo->prepare($sql)){
    // Bind user_id if the user is not an admin
    if($_SESSION["role"] !== 'Admin'){
        $stmt->bindParam(":user_id", $_SESSION["id"], PDO::PARAM_INT);
    }

    if($stmt->execute()){
        if($stmt->rowCount() > 0){
            $purchase_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }
    // Close statement
    unset($stmt);
}
// Close connection
unset($pdo);

$page_title = "Manage Purchase Orders";
include 'includes/header.php';
?>

<div class="page-header clearfix">
    <h2 class="float-left">Purchase Order Details</h2>
    <a href="add_purchase_order.php" class="btn btn-success float-right">Add New Purchase Order</a>
</div>
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
    <p class='lead'><em>No purchase orders found.</em></p>
<?php endif; ?>

<?php
include 'includes/footer.php';
?>
