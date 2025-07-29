<?php
require 'vendor/autoload.php'; // Ensure MongoDB PHP library is installed via Composer

// MongoDB connection
try {
    $client = new MongoDB\Client("mongodb://localhost:27017"); // Update the connection string if needed
    $database = $client->selectDatabase('Urban_Feedback'); // Replace with your actual database name
    $collection = $database->selectCollection('Feedback'); // Replace with your collection name
} catch (Exception $e) {
    die("Error connecting to MongoDB: " . $e->getMessage());
}

// Fetch all feedbacks from MongoDB
$feedbacks = $collection->find();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UrbanFood - Feedbacks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 80px;
        }
        .feedback-card {
            margin-bottom: 20px;
        }
        .stars {
            color: #ffd700;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#">UrbanFood</a>
    </div>
</nav>

<div class="container">
    <h2 class="text-center mt-4 mb-4 fw-bold">Customer Feedbacks</h2>

    <!-- Feedback List -->
    <div class="row">
        <?php foreach ($feedbacks as $feedback): ?>
            <div class="col-md-6">
                <div class="card feedback-card">
                    <div class="card-body">
                        <h5 class="card-title">Customer ID: <?php echo $feedback['customer_id']; ?></h5>
                        <p class="card-text">
                            <strong>Rating:</strong> 
                            <?php
                            // Display stars based on rating
                            for ($i = 0; $i < $feedback['Rating']; $i++) {
                                echo "&#9733;";
                            }
                            for ($i = $feedback['Rating']; $i < 5; $i++) {
                                echo "&#9734;";
                            }
                            ?>
                        </p>
                        <p class="card-text"><strong>Description:</strong> <?php echo $feedback['Description']; ?></p>
                        <p class="card-text"><strong>Date:</strong> <?php echo $feedback['date']; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- JQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>

</body>
</html>
