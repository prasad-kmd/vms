<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Check existence of id parameter before processing further
if(!isset($_GET["id"]) || empty(trim($_GET["id"]))){
    header("location: error.php");
    exit();
}

// Include config file
require_once "config/db.php";

$order_id = trim($_GET["id"]);
$order = null;
$order_items = [];

// Fetch main order details
$sql_order = "SELECT po.PurchaseOrderID, po.OrderDate, po.TotalAmount, v.Name AS VendorName, v.ContactInfo, v.Email
              FROM purchase_orders po
              JOIN vendors v ON po.VendorID = v.VendorID
              WHERE po.PurchaseOrderID = :id";

if($stmt_order = $pdo->prepare($sql_order)) {
    $stmt_order->bindParam(":id", $order_id, PDO::PARAM_INT);
    if($stmt_order->execute()){
        if($stmt_order->rowCount() == 1){
            $order = $stmt_order->fetch(PDO::FETCH_ASSOC);
        } else {
            header("location: error.php");
            exit();
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit();
    }
    unset($stmt_order);
}

// Fetch order line items
$sql_items = "SELECT i.Quantity, i.UnitPrice, p.ProductName
              FROM purchase_order_items i
              JOIN products p ON i.ProductID = p.ProductID
              WHERE i.PurchaseOrderID = :id";

if($stmt_items = $pdo->prepare($sql_items)){
    $stmt_items->bindParam(":id", $order_id, PDO::PARAM_INT);
    if($stmt_items->execute()){
        $order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "Oops! Something went wrong. Please try again later.";
        exit();
    }
    unset($stmt_items);
}

unset($pdo);

$page_title = "View Purchase Order";
include 'includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3 class="float-left">Purchase Order #<?php echo htmlspecialchars($order['PurchaseOrderID']); ?></h3>
        <h3 class="float-right">Date: <?php echo htmlspecialchars($order['OrderDate']); ?></h3>
    </div>
    <div class="card-body">
        <h5 class="card-title">Vendor Details</h5>
        <p class="card-text">
            <strong>Name:</strong> <?php echo htmlspecialchars($order['VendorName']); ?><br>
            <strong>Contact:</strong> <?php echo htmlspecialchars($order['ContactInfo']); ?><br>
            <strong>Email:</strong> <?php echo htmlspecialchars($order['Email']); ?>
        </p>
        <hr>
        <h5 class="card-title">Order Items</h5>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($order_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['ProductName']); ?></td>
                        <td><?php echo htmlspecialchars($item['Quantity']); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($item['UnitPrice'], 2)); ?></td>
                        <td>$<?php echo htmlspecialchars(number_format($item['Quantity'] * $item['UnitPrice'], 2)); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Grand Total:</th>
                    <th>$<?php echo htmlspecialchars(number_format($order['TotalAmount'], 2)); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="card-footer">
         <a href="purchase_orders.php" class="btn btn-primary">Back</a>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
