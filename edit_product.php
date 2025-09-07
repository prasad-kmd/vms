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
$id = 0;

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Get hidden input value
    $id = $_POST["id"];

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

    // Check input errors before updating the database
    if(empty($product_name_err) && empty($price_err) && empty($vendor_id_err)){
        // Prepare an update statement
        $sql = "UPDATE products SET ProductName = :product_name, Price = :price, VendorID = :vendor_id WHERE ProductID = :id";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":product_name", $product_name, PDO::PARAM_STR);
            $stmt->bindParam(":price", $price, PDO::PARAM_STR);
            $stmt->bindParam(":vendor_id", $vendor_id, PDO::PARAM_INT);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records updated successfully. Redirect to landing page
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

} else {
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);

        // Prepare a select statement
        $sql = "SELECT * FROM products WHERE ProductID = :id";
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    // Retrieve individual field value
                    $product_name = $row["ProductName"];
                    $price = $row["Price"];
                    $vendor_id = $row["VendorID"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        // Close statement
        unset($stmt);
        // Close connection
        unset($pdo);
    } else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 500px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper mt-5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="page-header">
                        <h2>Edit Product</h2>
                    </div>
                    <p>Please edit the input values and submit to update the product.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group <?php echo (!empty($product_name_err)) ? 'has-error' : ''; ?>">
                            <label>Product Name</label>
                            <input type="text" name="product_name" class="form-control" value="<?php echo $product_name; ?>">
                            <span class="help-block text-danger"><?php echo $product_name_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($price_err)) ? 'has-error' : ''; ?>">
                            <label>Price</label>
                            <input type="text" name="price" class="form-control" value="<?php echo $price; ?>">
                            <span class="help-block text-danger"><?php echo $price_err;?></span>
                        </div>
                        <div class="form-group <?php echo (!empty($vendor_id_err)) ? 'has-error' : ''; ?>">
                            <label>Vendor</label>
                            <select name="vendor_id" class="form-control">
                                <option value="">Please select</option>
                                <?php foreach($vendors as $vendor): ?>
                                    <option value="<?php echo $vendor['VendorID']; ?>" <?php echo ($vendor_id == $vendor['VendorID']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($vendor['Name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="help-block text-danger"><?php echo $vendor_id_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="products.php" class="btn btn-default">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
