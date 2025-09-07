<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then return an error
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'User not logged in.']);
    exit;
}

// Include config file
require_once "config/db.php";

$vendor_id = 0;
if(isset($_GET['vendor_id']) && is_numeric($_GET['vendor_id'])){
    $vendor_id = $_GET['vendor_id'];
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Invalid vendor ID.']);
    exit;
}

$products = [];
$sql = "SELECT ProductID, ProductName, Price FROM products WHERE VendorID = :vendor_id ORDER BY ProductName ASC";

if($stmt = $pdo->prepare($sql)){
    $stmt->bindParam(":vendor_id", $vendor_id, PDO::PARAM_INT);
    if($stmt->execute()){
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($stmt);
}

unset($pdo);

// Set content type to JSON and output the data
header('Content-Type: application/json');
echo json_encode($products);
?>
