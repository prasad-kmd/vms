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

$user_id = $_SESSION["id"];
$username = $_SESSION["username"];
$role = $_SESSION["role"];
$email = '';

// Messages
$email_msg = $pwd_msg = "";
$email_msg_type = $pwd_msg_type = "danger";


// Fetch current email
$sql_user = "SELECT Email FROM Users WHERE UserID = :id";
if($stmt_user = $pdo->prepare($sql_user)){
    $stmt_user->bindParam(":id", $user_id, PDO::PARAM_INT);
    if($stmt_user->execute()){
        $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);
        $email = $user_data['Email'];
    }
    unset($stmt_user);
}


// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Update Email
    if(isset($_POST['action']) && $_POST['action'] == 'update_email'){
        $new_email = trim($_POST['email']);
        if(empty($new_email) || !filter_var($new_email, FILTER_VALIDATE_EMAIL)){
            $email_msg = "Please enter a valid email address.";
        } else {
            // Check if email is already taken by another user
            $sql_check = "SELECT UserID FROM Users WHERE Email = :email AND UserID != :id";
            if($stmt_check = $pdo->prepare($sql_check)){
                $stmt_check->bindParam(":email", $new_email, PDO::PARAM_STR);
                $stmt_check->bindParam(":id", $user_id, PDO::PARAM_INT);
                if($stmt_check->execute()){
                    if($stmt_check->rowCount() > 0){
                        $email_msg = "This email is already taken.";
                    } else {
                        // Update email
                        $sql_update = "UPDATE Users SET Email = :email WHERE UserID = :id";
                        if($stmt_update = $pdo->prepare($sql_update)){
                            $stmt_update->bindParam(":email", $new_email, PDO::PARAM_STR);
                            $stmt_update->bindParam(":id", $user_id, PDO::PARAM_INT);
                            if($stmt_update->execute()){
                                $email_msg = "Email updated successfully.";
                                $email_msg_type = "success";
                                $email = $new_email; // Update email variable for display
                            } else {
                                $email_msg = "Oops! Something went wrong. Please try again later.";
                            }
                            unset($stmt_update);
                        }
                    }
                }
                unset($stmt_check);
            }
        }
    }

    // Update Password
    if(isset($_POST['action']) && $_POST['action'] == 'update_password'){
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if(empty($current_password) || empty($new_password) || empty($confirm_password)){
            $pwd_msg = "Please fill in all fields.";
        } elseif($new_password !== $confirm_password){
            $pwd_msg = "New password and confirmation do not match.";
        } elseif(strlen($new_password) < 6){
            $pwd_msg = "Password must have at least 6 characters.";
        } else {
            // Check current password
            $sql_pwd = "SELECT Password FROM Users WHERE UserID = :id";
            if($stmt_pwd = $pdo->prepare($sql_pwd)){
                $stmt_pwd->bindParam(":id", $user_id, PDO::PARAM_INT);
                if($stmt_pwd->execute()){
                    $row = $stmt_pwd->fetch(PDO::FETCH_ASSOC);
                    $hashed_password = $row['Password'];
                    if(password_verify($current_password, $hashed_password)){
                        // Current password is correct, update to new password
                        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        $sql_update_pwd = "UPDATE Users SET Password = :password WHERE UserID = :id";
                        if($stmt_update_pwd = $pdo->prepare($sql_update_pwd)){
                            $stmt_update_pwd->bindParam(":password", $new_hashed_password, PDO::PARAM_STR);
                            $stmt_update_pwd->bindParam(":id", $user_id, PDO::PARAM_INT);
                            if($stmt_update_pwd->execute()){
                                $pwd_msg = "Password updated successfully.";
                                $pwd_msg_type = "success";
                            } else {
                                $pwd_msg = "Oops! Something went wrong. Please try again later.";
                            }
                            unset($stmt_update_pwd);
                        }
                    } else {
                        $pwd_msg = "The current password you entered is incorrect.";
                    }
                }
                unset($stmt_pwd);
            }
        }
    }
}

unset($pdo);

$page_title = "My Profile";
include 'includes/header.php';
?>

<div class="page-header">
    <h2>My Profile</h2>
</div>
<p>Manage your account details below.</p>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Account Information</h5>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
        <p><strong>Role:</strong> <?php echo htmlspecialchars($role); ?></p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Update Email</h5>
        <?php if(!empty($email_msg)): ?>
            <div class="alert alert-<?php echo $email_msg_type; ?>"><?php echo $email_msg; ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="action" value="update_email">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Update Email</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Change Password</h5>
        <?php if(!empty($pwd_msg)): ?>
            <div class="alert alert-<?php echo $pwd_msg_type; ?>"><?php echo $pwd_msg; ?></div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="action" value="update_password">
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" name="current_password" class="form-control">
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" class="form-control">
            </div>
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
