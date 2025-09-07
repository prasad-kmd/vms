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

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Please enter a name.";
    } else{
        $name = $input_name;
    }

    // Validate contact
    $input_contact = trim($_POST["contact"]);
    $contact = $input_contact; // Contact is optional

    // Validate email
    $input_email = trim($_POST["email"]);
    if(empty($input_email)){
        $email_err = "Please enter an email address.";
    } elseif(!filter_var($input_email, FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email address.";
    } else{
        // Check if email already exists
        $sql = "SELECT VendorID FROM vendors WHERE Email = :email";
        if($stmt = $pdo->prepare($sql)){
            $stmt->bindParam(":email", $input_email, PDO::PARAM_STR);
            if($stmt->execute()){
                if($stmt->rowCount() > 0){
                    $email_err = "This email is already registered.";
                } else {
                    $email = $input_email;
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }

    // Check input errors before inserting in database
    if(empty($name_err) && empty($contact_err) && empty($email_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO vendors (Name, ContactInfo, Email) VALUES (:name, :contact, :email)";

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":contact", $contact, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records created successfully. Redirect to landing page
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
}

$page_title = "Add Vendor";
include 'includes/header.php';
?>

<div class="page-header">
    <h2>Add Vendor</h2>
</div>
<p>Please fill this form and submit to add a vendor to the database.</p>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
    <input type="submit" class="btn btn-primary" value="Submit">
    <a href="vendors.php" class="btn btn-secondary">Cancel</a>
</form>

<?php
include 'includes/footer.php';
?>
