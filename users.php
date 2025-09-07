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
    header("location: index.php"); // Redirect non-admins
    exit;
}

// Include config file
require_once "config/db.php";

// Attempt to select all users
$sql = "SELECT UserID, Username, Email, Role, CreatedAt FROM Users ORDER BY Username ASC";
$users = [];
if($stmt = $pdo->prepare($sql)){
    if($stmt->execute()){
        if($stmt->rowCount() > 0){
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }
    unset($stmt);
}
unset($pdo);

$page_title = "User Management";
include 'includes/header.php';
?>

<div class="page-header">
    <h2>User Management</h2>
</div>
<table class='table table-bordered table-striped'>
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($users as $user): ?>
        <tr>
            <td><?php echo htmlspecialchars($user['UserID']); ?></td>
            <td><?php echo htmlspecialchars($user['Username']); ?></td>
            <td><?php echo htmlspecialchars($user['Email']); ?></td>
            <td><?php echo htmlspecialchars($user['Role']); ?></td>
            <td><?php echo htmlspecialchars($user['CreatedAt']); ?></td>
            <td>
                <?php if($user['UserID'] != $_SESSION['id']): // Prevent admin from editing their own account on this page ?>
                    <a href='edit_user.php?id=<?php echo $user['UserID']; ?>' class='btn btn-warning btn-sm'>Edit</a>
                <?php else: ?>
                    <a href='profile.php' class='btn btn-info btn-sm'>My Profile</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php
include 'includes/footer.php';
?>
