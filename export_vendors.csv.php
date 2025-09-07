<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config/db.php";

// Set headers to trigger file download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=vendors.csv');

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Output the CSV header
fputcsv($output, ['Vendor ID', 'Name', 'Contact Info', 'Email']);

// Fetch the data
$sql = "SELECT VendorID, Name, ContactInfo, Email FROM vendors ORDER BY VendorID ASC";
if($stmt = $pdo->prepare($sql)){
    if($stmt->execute()){
        if($stmt->rowCount() > 0){
            // Loop through the rows and output them
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                fputcsv($output, $row);
            }
        }
    }
    unset($stmt);
}

unset($pdo);

// Exit to prevent any other output
exit();
?>
