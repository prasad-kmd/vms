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

// Attempt to select all vendors
$sql = "SELECT * FROM vendors ORDER BY Name";
$vendors = [];
if($stmt = $pdo->prepare($sql)){
    if($stmt->execute()){
        if($stmt->rowCount() > 0){
            $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }
    // Close statement
    unset($stmt);
}
// Close connection
unset($pdo);

$page_title = "Manage Vendors";
include 'includes/header.php';
?>

<div class="page-header clearfix">
    <h2 class="float-left">Vendor Details</h2>
    <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'Admin'): ?>
    <a href="add_vendor.php" class="btn btn-success float-right">Add New Vendor</a>
    <?php endif; ?>
</div>
<?php
// Display error message from session if it exists
if(isset($_SESSION['error_message']) && !empty($_SESSION['error_message'])){
    echo '<div class="alert alert-danger mt-3">' . $_SESSION['error_message'] . '</div>';
    // Unset the session variable so it doesn't show again
    unset($_SESSION['error_message']);
}
?>
<?php
if(!empty($vendors)): ?>
    <table class='table table-bordered table-striped'>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Contact Info</th>
                <th>Email</th>
                <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'Admin'): ?>
                <th>Action</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach($vendors as $vendor): ?>
            <tr>
                <td><?php echo htmlspecialchars($vendor['VendorID']); ?></td>
                <td><?php echo htmlspecialchars($vendor['Name']); ?></td>
                <td><?php echo htmlspecialchars($vendor['ContactInfo']); ?></td>
                <td><?php echo htmlspecialchars($vendor['Email']); ?></td>
                <?php if(isset($_SESSION["role"]) && $_SESSION["role"] === 'Admin'): ?>
                <td>
                    <a href='edit_vendor.php?id=<?php echo $vendor['VendorID']; ?>' class='btn btn-warning btn-sm'>Edit</a>
                    <a href='delete_vendor.php?id=<?php echo $vendor['VendorID']; ?>' class='btn btn-danger btn-sm'>Delete</a>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class='lead'><em>No vendors found. Please add one.</em></p>
<?php endif; ?>

<?php
include 'includes/footer.php';
?>
