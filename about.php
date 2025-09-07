<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$page_title = "About Us";
include 'includes/header.php';
?>

<div class="page-header">
    <h2>About the Vendor Management System</h2>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Our Mission</h5>
        <p class="card-text">
            The Vendor Management System (VMS) is designed to streamline and simplify the complex process of managing relationships with suppliers. Our mission is to provide a robust, user-friendly platform that empowers businesses to efficiently track vendors, manage product catalogs, and oversee purchase orders. By centralizing these key operations, we aim to enhance transparency, improve communication, and drive operational efficiency.
        </p>

        <h5 class="card-title mt-4">Core Features</h5>
        <ul>
            <li><strong>Vendor Information Management:</strong> Keep a comprehensive and up-to-date directory of all your vendors, including their contact details and performance history.</li>
            <li><strong>Product Cataloging:</strong> Maintain a detailed catalog of products from each vendor, with pricing and availability information at your fingertips.</li>
            <li><strong>Purchase Order Tracking:</strong> Create, manage, and track purchase orders from initiation to completion, ensuring a smooth procurement process.</li>
            <li><strong>Role-Based Access Control:</strong> Secure your data with a simple permission system that distinguishes between regular users and administrators.</li>
        </ul>

        <h5 class="card-title mt-4">Why Choose VMS?</h5>
        <p class="card-text">
            In a fast-paced business environment, efficient vendor management is crucial for success. Our system is built with the end-user in mind, featuring an intuitive interface and powerful features that save time and reduce administrative overhead. Whether you are a small business or a large enterprise, the VMS provides the tools you need to build stronger supplier relationships and make smarter procurement decisions.
        </p>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
