<?php
require 'vendor/autoload.php'; // Ensure you have installed MongoDB PHP library via Composer

try {
    $client = new MongoDB\Client("mongodb://localhost:27017"); // Update if your MongoDB runs on a different host/port
    $database = $client->selectDatabase('Urban_Feedback'); // Change to your actual database name
    $collection = $database->selectCollection('Feedback'); // Change to your collection name
} catch (Exception $e) {
    die("Error connecting to MongoDB: " . $e->getMessage());
}
?>
