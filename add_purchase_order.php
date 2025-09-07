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

// Fetch all vendors for the dropdown
$vendors = [];
$sql_vendors = "SELECT VendorID, Name FROM vendors ORDER BY Name ASC";
if($stmt_vendors = $pdo->prepare($sql_vendors)){
    if($stmt_vendors->execute()){
        $vendors = $stmt_vendors->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($stmt_vendors);
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $vendor_id = $_POST['vendor_id'];
    $order_date = date('Y-m-d');
    $product_ids = $_POST['product_id'];
    $quantities = $_POST['quantity'];
    $total_amount = 0;

    // Basic validation
    if(empty($vendor_id) || empty($product_ids) || count($product_ids) !== count($quantities)) {
        // Handle error - redirect back or show message
        echo "Error: Invalid form submission.";
        exit;
    }

    $user_id = $_SESSION["id"]; // Get user ID from session

    try {
        // Start transaction
        $pdo->beginTransaction();

        // 1. Insert into purchase_orders table
        $sql_order = "INSERT INTO purchase_orders (VendorID, UserID, OrderDate, TotalAmount) VALUES (:vendor_id, :user_id, :order_date, :total_amount)";
        $stmt_order = $pdo->prepare($sql_order);
        $stmt_order->bindParam(':vendor_id', $vendor_id, PDO::PARAM_INT);
        $stmt_order->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt_order->bindParam(':order_date', $order_date, PDO::PARAM_STR);
        $stmt_order->bindParam(':total_amount', $total_amount, PDO::PARAM_STR); // Placeholder
        $stmt_order->execute();
        $purchase_order_id = $pdo->lastInsertId();

        // 2. Insert items into purchase_order_items and calculate total
        $sql_item = "INSERT INTO purchase_order_items (PurchaseOrderID, ProductID, Quantity, UnitPrice) VALUES (:purchase_order_id, :product_id, :quantity, :unit_price)";
        $stmt_item = $pdo->prepare($sql_item);

        $sql_price = "SELECT Price FROM products WHERE ProductID = :product_id";
        $stmt_price = $pdo->prepare($sql_price);

        for($i = 0; $i < count($product_ids); $i++) {
            $product_id = $product_ids[$i];
            $quantity = $quantities[$i];

            if($quantity <= 0) continue; // Skip items with no quantity

            // Fetch current price from DB
            $stmt_price->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt_price->execute();
            $product = $stmt_price->fetch(PDO::FETCH_ASSOC);
            $unit_price = $product['Price'];

            // Insert item
            $stmt_item->bindParam(':purchase_order_id', $purchase_order_id, PDO::PARAM_INT);
            $stmt_item->bindParam(':product_id', $product_id, PDO::PARAM_INT);
            $stmt_item->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt_item->bindParam(':unit_price', $unit_price, PDO::PARAM_STR);
            $stmt_item->execute();

            // Add to total amount
            $total_amount += $unit_price * $quantity;
        }

        // 3. Update the total amount in the purchase_orders table
        $sql_update_total = "UPDATE purchase_orders SET TotalAmount = :total_amount WHERE PurchaseOrderID = :purchase_order_id";
        $stmt_update_total = $pdo->prepare($sql_update_total);
        $stmt_update_total->bindParam(':total_amount', $total_amount, PDO::PARAM_STR);
        $stmt_update_total->bindParam(':purchase_order_id', $purchase_order_id, PDO::PARAM_INT);
        $stmt_update_total->execute();

        // Commit transaction
        $pdo->commit();

        // Redirect to the list page
        header("location: purchase_orders.php");
        exit();

    } catch (Exception $e) {
        // Rollback on error
        $pdo->rollBack();
        die("ERROR: Could not complete order. " . $e->getMessage());
    }

    unset($pdo);
}

$page_title = "Add Purchase Order";
include 'includes/header.php';
?>

<div class="page-header">
    <h2>Add Purchase Order</h2>
</div>
<p>Select a vendor to begin creating a purchase order.</p>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="order-form">
    <!-- Step 1: Select Vendor -->
    <div class="form-group">
        <label>Vendor</label>
        <select name="vendor_id" id="vendor-select" class="form-control">
            <option value="">Select a Vendor</option>
            <?php foreach($vendors as $vendor): ?>
                <option value="<?php echo $vendor['VendorID']; ?>"><?php echo htmlspecialchars($vendor['Name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Step 2: Add Products (Dynamically populated) -->
    <div id="product-selection-area" style="display: none;">
        <hr>
        <h4>Add Products to Order</h4>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Product</label>
                <select id="product-select" class="form-control"></select>
            </div>
            <div class="form-group col-md-3">
                <label>Quantity</label>
                <input type="number" id="quantity-input" class="form-control" value="1" min="1">
            </div>
            <div class="form-group col-md-3">
                <label>&nbsp;</label>
                <button type="button" id="add-item-btn" class="btn btn-primary form-control">Add Item</button>
            </div>
        </div>
    </div>

    <!-- Step 3: Order Items Table -->
    <table id="order-items-table" class="table table-bordered" style="display: none;">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <!-- Items will be added here -->
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total:</th>
                <th id="total-amount-display">$0.00</th>
                <th></th>
            </tr>
        </tfoot>
    </table>

    <!-- Step 4: Submit -->
    <div class="mt-4">
        <input type="submit" class="btn btn-success" value="Create Purchase Order" disabled>
        <a href="purchase_orders.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<script src="assets/js/add_purchase_order.js"></script>

<?php
include 'includes/footer.php';
?>
