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

// Define variables and initialize with empty values
$name = $contact = $email = "";
$name_err = $contact_err = $email_err = "";
$id = 0;

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Get hidden input value
    $id = $_POST["id"];

    // Validate name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Please enter a name.";
    } else{
        $name = $input_name;
    }

    // Validate contact
    $input_contact = trim($_POST["contact"]);
    $contact = $input_contact;

    // Validate email
    $input_email = trim($_POST["email"]);
    if(empty($input_email)){
        $email_err = "Please enter an email address.";
    } elseif(!filter_var($input_email, FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email address.";
    } else {
        // Check if email has changed and if the new one already exists
        $sql = "SELECT VendorID FROM vendors WHERE Email = :email AND VendorID != :id";
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":email", $input_email, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            if($stmt->execute()){
                if($stmt->rowCount() > 0){
                    $email_err = "This email is already registered to another vendor.";
                } else {
                    $email = $input_email;
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }

    // Check input errors before updating the database
    if(empty($name_err) && empty($contact_err) && empty($email_err)){
        // Prepare an update statement
        $sql = "UPDATE vendors SET Name = :name, ContactInfo = :contact, Email = :email WHERE VendorID = :id";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":contact", $contact, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records updated successfully. Redirect to landing page
                header("location: vendors.php");
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
        $sql = "SELECT * FROM vendors WHERE VendorID = :id";
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Retrieve individual field value
                    $name = $row["Name"];
                    $contact = $row["ContactInfo"];
                    $email = $row["Email"];
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
        // unset($pdo); // This was causing issues
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}

$page_title = "Edit Vendor";
include 'includes/header.php';
?>

<div class="page-header">
    <h2>Edit Vendor</h2>
</div>
<p>Please edit the input values and submit to update the vendor.</p>
<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
    <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
        <span class="invalid-feedback"><?php echo $name_err;?></span>
    </div>
    <div class="form-group">
        <label>Contact Info</label>
        <input type="text" name="contact" class="form-control" value="<?php echo $contact; ?>">
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="text" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
        <span class="invalid-feedback"><?php echo $email_err;?></span>
    </div>
    <input type="hidden" name="id" value="<?php echo $id; ?>"/>
    <input type="submit" class="btn btn-primary mt-4" value="Submit">
    <a href="vendors.php" class="btn btn-secondary mt-4">Cancel</a>
</form>

<?php
include 'includes/footer.php';
?>
