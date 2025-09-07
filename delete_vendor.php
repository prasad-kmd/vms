<?php
// Process delete operation after confirmation
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Initialize the session
    session_start();

    // Check if the user is logged in, otherwise redirect to login page
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

    // Get URL parameter
    $id = trim($_POST["id"]);

    // Check for related records in products or purchase_orders
    $sql_check = "SELECT (SELECT COUNT(*) FROM products WHERE VendorID = :id) + (SELECT COUNT(*) FROM purchase_orders WHERE VendorID = :id) as total";
    if($stmt_check = $pdo->prepare($sql_check)) {
        $stmt_check->bindParam(":id", $id, PDO::PARAM_INT);
        if($stmt_check->execute()){
            $result = $stmt_check->fetch(PDO::FETCH_ASSOC);
            if($result['total'] > 0){
                // There are related records, so we cannot delete.
                // Redirect back with an error message.
                $_SESSION['error_message'] = "Cannot delete this vendor because they have associated products or purchase orders.";
                header("location: vendors.php");
                exit();
            }
        } else {
            echo "Oops! Something went wrong while checking for related records.";
            exit();
        }
        unset($stmt_check);
    }


    // Prepare a delete statement
    $sql = "DELETE FROM vendors WHERE VendorID = :id";

    if($stmt = $pdo->prepare($sql)){
        // Bind variables to the prepared statement as parameters
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        // Attempt to execute the prepared statement
        if($stmt->execute()){
            // Records deleted successfully. Redirect to landing page
            header("location: vendors.php");
            exit();
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    // Close statement
    unset($stmt);
    // Close connection
    unset($pdo);

} else{
    // Check existence of id parameter
    if(empty(trim($_GET["id"]))){
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
    <title>Delete Vendor</title>
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
                        <h1>Delete Vendor</h1>
                    </div>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="alert alert-danger">
                            <input type="hidden" name="id" value="<?php echo trim($_GET["id"]); ?>"/>
                            <p>Are you sure you want to delete this vendor record?</p><br>
                            <p>
                                <input type="submit" value="Yes" class="btn btn-danger">
                                <a href="vendors.php" class="btn btn-default">No</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
