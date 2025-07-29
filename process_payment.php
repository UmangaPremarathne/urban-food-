<?php
// Define connection parameters
$host = 'localhost';
$port = '1521';
$sid = 'xe';  // Your Oracle SID
$username = 'system';  // Your Oracle username
$password = 'oneli123';  // Your Oracle password

// Establish the connection using PDO
try {
    $dsn = "oci:dbname=//{$host}:{$port}/{$sid}";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Process the form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $payment_method = $_POST['payment_method'];
    $payment_date = $_POST['payment_date'];

    // Insert the payment details into the URBAN_PAYMENTS table
    try {
        $sql = "INSERT INTO URBAN_PAYMENTS (ORDER_ID, STATUS, PAYMENT_DATE, METHOD)
                VALUES (:order_id, 'Pending', TO_DATE(:payment_date, 'YYYY-MM-DD'), :payment_method)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':payment_method', $payment_method);
        $stmt->bindParam(':payment_date', $payment_date);

        // Execute the query
        $stmt->execute();

        // Redirect or display a success message
        echo "Payment added successfully!";
        header("Location: payment.html");  // Redirect back to the payment page after successful submission
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
