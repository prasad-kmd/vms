<?php
// Initialize the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 750px;
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
                        <h1>Invalid Request</h1>
                    </div>
                    <div class="alert alert-danger fade-in">
                        <p>Sorry, you've made an invalid request. Please <a href="vendors.php" class="alert-link">go back</a> and try again.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
