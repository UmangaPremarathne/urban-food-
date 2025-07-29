<?php
// --- Database Connection ---
$host = 'localhost';
$port = '1521';
$sid = 'xe';
$username = 'system';
$password = 'oneli123';

function getConnection() {
    global $host, $port, $sid, $username, $password;
    try {
        $dsn = "oci:dbname=//{$host}:{$port}/{$sid}";
        $conn = new PDO($dsn, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        exit;
    }
}

function fetchOrders($conn) {
    $sql = "SELECT * FROM URBAN_ORDERS"; // Make sure this table exists in your DB
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Query error: " . $e->getMessage();
        return [];
    }
}
?>

<!-- Order Cards UI -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- Optional external CSS -->
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f8f9fa;
            padding: 2rem;
        }
        .dashboard-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }
        .order-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
        }
        .order-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
            padding: 1.2rem;
            transition: 0.3s ease;
        }
        .order-card:hover {
            transform: scale(1.02);
        }
        .order-card h3 {
            margin: 0 0 0.5rem;
            font-size: 1.2rem;
            color: #333;
        }
        .order-info {
            margin: 0.3rem 0;
            color: #555;
        }
        .order-status {
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>
    <div class="dashboard-title">Order Management</div>
    <div class="order-container">
        <?php
            $conn = getConnection();
            $orders = fetchOrders($conn);

            if (count($orders) > 0) {
                foreach ($orders as $order) {
                    echo '<div class="order-card">';
                    echo '<h3>Order ID: ' . $order['order_id'] . '</h3>';
                    echo '<div class="order-info">Customer ID: ' . $order['customer_id'] . '</div>';
                    echo '<div class="order-info">Product ID: ' . $order['product_id'] . '</div>';
                    echo '<div class="order-info">Quantity: ' . $order['quantity'] . '</div>';
                    echo '<div class="order-info">Order Date: ' . $order['order_date'] . '</div>';
                    echo '<div class="order-info order-status">Status: ' . $order['status'] . '</div>';
                    echo '</div>';
                }
            } else {
                echo "<p>No orders found.</p>";
            }
        ?>
    </div>
</body>
</html>
