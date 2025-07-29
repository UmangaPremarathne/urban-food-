<?php
require 'vendor/autoload.php'; // Make sure MongoDB driver is loaded

$client = new MongoDB\Client("mongodb://localhost:27017");
$collection = $client->urbanfood->feedback;

// Insert feedback
if (isset($_POST['submit'])) {
    $customer_id = $_POST['customer_id'] ?? '';
    $rating = (int) ($_POST['rating'] ?? 0);
    $description = $_POST['description'] ?? '';
    $dateInput = $_POST['date'] ?? '';

    // Convert the date string (YYYY-MM-DD) to MongoDB Date object
    if (!empty($dateInput)) {
        $date = new MongoDB\BSON\UTCDateTime(strtotime($dateInput) * 1000);
    } else {
        $date = new MongoDB\BSON\UTCDateTime(); // fallback to current time
    }

    $collection->insertOne([
        'customer_id' => $customer_id,
        'rating' => $rating,
        'description' => $description,
        'date' => $date
    ]);
}

// Fetch and display all feedback
$feedbacks = $collection->find([], ['sort' => ['date' => -1]]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Feedback List</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .table-stars {
      font-size: 20px;
      color: #ffd700;
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">Customer Feedback</h2>
  <table class="table table-bordered">
    <thead class="table-dark">
      <tr>
        <th>Customer ID</th>
        <th>Rating</th>
        <th>Description</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($feedbacks as $fb): ?>
        <tr>
          <td><?= htmlspecialchars($fb['customer_id']) ?></td>
          <td>
            <div class="table-stars">
              <?php
              $rating = (int) $fb['rating'];
              echo str_repeat("&#9733;", $rating) . str_repeat("&#9734;", 5 - $rating);
              ?>
            </div>
          </td>
          <td><?= htmlspecialchars($fb['description']) ?></td>
          <td>
            <?= isset($fb['date']) ? $fb['date']->toDateTime()->format('Y-m-d') : 'N/A' ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
</body>
</html>