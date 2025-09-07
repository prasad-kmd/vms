<?php
// Initialize the session
session_start();

// Check if the user is logged in and is an admin
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== 'Admin'){
    header("location: index.php");
    exit;
}

// Include config file
require_once "config/db.php";

// Define variables and initialize with empty values
$username = $email = $role = "";
$email_err = $role_err = "";
$user_id = 0;

// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    $user_id = trim($_GET["id"]);

    // Prevent admin from editing their own profile here
    if($user_id == $_SESSION['id']){
        header("location: profile.php");
        exit;
    }
} else {
    header("location: users.php");
    exit;
}


// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user_id = $_POST["id"];

    // Validate email
    $new_email = trim($_POST["email"]);
    if(empty($new_email) || !filter_var($new_email, FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email.";
    } else {
        // Check if email is taken by another user
        $sql_check = "SELECT UserID FROM Users WHERE Email = :email AND UserID != :id";
        if($stmt_check = $pdo->prepare($sql_check)){
            $stmt_check->bindParam(":email", $new_email, PDO::PARAM_STR);
            $stmt_check->bindParam(":id", $user_id, PDO::PARAM_INT);
            if($stmt_check->execute()){
                if($stmt_check->rowCount() > 0){
                    $email_err = "This email is already taken.";
                } else {
                    $email = $new_email;
                }
            }
            unset($stmt_check);
        }
    }

    // Validate role
    $new_role = trim($_POST["role"]);
    if(empty($new_role) || !in_array($new_role, ['Admin', 'User'])){
        $role_err = "Please select a valid role.";
    } else {
        $role = $new_role;
    }

    // Check input errors before updating the database
    if(empty($email_err) && empty($role_err)){
        $sql_update = "UPDATE Users SET Email = :email, Role = :role WHERE UserID = :id";
        if($stmt = $pdo->prepare($sql_update)){
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":role", $role, PDO::PARAM_STR);
            $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
            if($stmt->execute()){
                header("location: users.php");
                exit();
            } else {
                echo "Something went wrong. Please try again later.";
            }
            unset($stmt);
        }
    }

} else {
    // Get current user data
    $sql = "SELECT Username, Email, Role FROM Users WHERE UserID = :id";
    if($stmt = $pdo->prepare($sql)){
        $stmt->bindParam(":id", $user_id, PDO::PARAM_INT);
        if($stmt->execute()){
            if($stmt->rowCount() == 1){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $username = $row["Username"];
                $email = $row["Email"];
                $role = $row["Role"];
            } else {
                header("location: users.php");
                exit();
            }
        }
        unset($stmt);
    }
}

unset($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        .wrapper{ width: 500px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="wrapper mt-5">
        <h2>Edit User: <?php echo htmlspecialchars($username); ?></h2>
        <p>Use this form to update the user's details.</p>
        <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($username); ?>" disabled>
            </div>
            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
                <span class="help-block text-danger"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group <?php echo (!empty($role_err)) ? 'has-error' : ''; ?>">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="User" <?php echo ($role == 'User') ? 'selected' : ''; ?>>User</option>
                    <option value="Admin" <?php echo ($role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                </select>
                <span class="help-block text-danger"><?php echo $role_err; ?></span>
            </div>
            <input type="hidden" name="id" value="<?php echo $user_id; ?>"/>
            <input type="submit" class="btn btn-primary" value="Update User">
            <a href="users.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
