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

$page_title = "Manage Products";
include 'includes/header.php';
?>

<div class="page-header clearfix">
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

<?php
include 'includes/footer.php';
?>
