<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Check if the user is an admin
if(!isset($_SESSION["role"]) || $_SESSION["role"] !== 'Admin'){
    // Redirect to an unauthorized page or the main page
    header("location: index.php");
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

// Define variables and initialize with empty values
$product_name = $price = $vendor_id = "";
$product_name_err = $price_err = $vendor_id_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate product name
    $input_product_name = trim($_POST["product_name"]);
    if(empty($input_product_name)){
        $product_name_err = "Please enter a product name.";
    } else{
        $product_name = $input_product_name;
    }

    // Validate price
    $input_price = trim($_POST["price"]);
    if(empty($input_price)){
        $price_err = "Please enter the price.";
    } elseif(!is_numeric($input_price) || $input_price <= 0){
        $price_err = "Please enter a positive number for the price.";
    } else{
        $price = $input_price;
    }

    // Validate vendor
    $input_vendor_id = trim($_POST["vendor_id"]);
    if(empty($input_vendor_id)){
        $vendor_id_err = "Please select a vendor.";
    } else{
        $vendor_id = $input_vendor_id;
    }

    // Check input errors before inserting in database
    if(empty($product_name_err) && empty($price_err) && empty($vendor_id_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO products (ProductName, Price, VendorID) VALUES (:product_name, :price, :vendor_id)";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":product_name", $product_name, PDO::PARAM_STR);
            $stmt->bindParam(":price", $price, PDO::PARAM_STR); // Price is decimal, bind as string
            $stmt->bindParam(":vendor_id", $vendor_id, PDO::PARAM_INT);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records created successfully. Redirect to landing page
                header("location: products.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
        }
        // Close statement
        unset($stmt);
    }
    // Close connection
    unset($pdo);
}

$page_title = "Add Product";
include 'includes/header.php';
?>

<div class="page-header">
    <h2>Add Product</h2>
</div>
<p>Please fill this form and submit to add a product to the database.</p>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group">
        <label>Product Name</label>
        <input type="text" name="product_name" class="form-control <?php echo (!empty($product_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $product_name; ?>">
        <span class="invalid-feedback"><?php echo $product_name_err;?></span>
    </div>
    <div class="form-group">
        <label>Price</label>
        <input type="text" name="price" class="form-control <?php echo (!empty($price_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $price; ?>">
        <span class="invalid-feedback"><?php echo $price_err;?></span>
    </div>
    <div class="form-group">
        <label>Vendor</label>
        <select name="vendor_id" class="form-control <?php echo (!empty($vendor_id_err)) ? 'is-invalid' : ''; ?>">
            <option value="">Please select</option>
            <?php foreach($vendors as $vendor): ?>
                <option value="<?php echo $vendor['VendorID']; ?>" <?php echo ($vendor_id == $vendor['VendorID']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($vendor['Name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="invalid-feedback"><?php echo $vendor_id_err;?></span>
    </div>
    <input type="submit" class="btn btn-primary" value="Submit">
    <a href="products.php" class="btn btn-secondary">Cancel</a>
</form>

<?php
include 'includes/footer.php';
?>
