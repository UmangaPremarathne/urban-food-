<?php
include 'db_oracle.php'; // Include the connection setup

// Check if POST request has data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_id = $_POST['payment_id'];
    $status = $_POST['status'];

    // Prepare the SQL query to update the payment status
    $sql = "UPDATE URBAN_PAYMENTS SET status = :status WHERE payment_id = :payment_id";

    // Get the database connection
    $conn = getConnection();

    try {
        // Prepare the SQL query
        $stmt = $conn->prepare($sql);

        // Bind parameters
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':payment_id', $payment_id);

        // Execute the query
        $stmt->execute();

        echo "Payment status updated successfully!";
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage();
    }
}
?>
