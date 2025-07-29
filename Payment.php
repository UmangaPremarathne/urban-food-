<?php
require_once 'db_oracle.php';
$conn = getConnection();

// Handle payment form submit
if (isset($_POST['submit'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $method = $_POST['method'];
    $payment_date = date('Y-m-d');

    // Auto-generate Payment ID using a sequence (adjust as needed)
    $stmt = $conn->prepare("SELECT URBAN_PAYMENTS_SEQ.NEXTVAL FROM DUAL");
    $stmt->execute();
    $payment_id = $stmt->fetchColumn(); // Fetch the next value for payment_id

    try {
        $sql = "INSERT INTO URBAN_PAYMENTS (PAYMENT_ID, ORDER_ID, STATUS, METHOD, PAYMENT_DATE)
                VALUES (:payment_id, :order_id, :status, :method, TO_DATE(:payment_date, 'YYYY-MM-DD'))";

        executeQuery($conn, $sql, [
            ':payment_id' => $payment_id,
            ':order_id' => $order_id,
            ':status' => $status,
            ':method' => $method,
            ':payment_date' => $payment_date
        ]);

        echo "<script>alert('Payment recorded successfully!');window.location='payment.php';</script>";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Get confirmed orders
$orders = executeQuery($conn, "SELECT * FROM URBAN_ORDERS WHERE ORDER_STATUS = 'Confirmed' ORDER BY ORDER_DATE DESC")->fetchAll();

// Get past payments
$payments = executeQuery($conn, "SELECT * FROM URBAN_PAYMENTS ORDER BY PAYMENT_DATE DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>UrbanFood - Payments</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; padding-top: 80px; }
    .form-card { max-width: 600px; margin: auto; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container">
    <a class="navbar-brand" href="#">UrbanFood</a>
  </div>
</nav>

<div class="container">
  <h2 class="text-center mt-4 mb-4 fw-bold">Process Payment</h2>

  <?php if (empty($orders)): ?>
    <div class="alert alert-info text-center">No confirmed orders to process payment.</div>
  <?php else: ?>
    <div class="card shadow p-4 form-card">
      <form method="POST" action="payment.php">
        <div class="mb-3">
          <label class="form-label">Payment ID (Auto-generated)</label>
          <input type="number" class="form-control" name="payment_id" value="<?= isset($payment_id) ? $payment_id : '' ?>" readonly>
        </div>

        <div class="mb-3">
          <label class="form-label">Select Order</label>
          <select name="order_id" class="form-select" required>
            <option value="" disabled selected>-- Choose Confirmed Order --</option>
            <?php foreach ($orders as $order): ?>
              <option value="<?= $order['ORDER_ID'] ?>">
                Order #<?= $order['ORDER_ID'] ?> - <?= $order['TOTAL_AMOUNT'] ?> Rs - <?= date('Y-m-d', strtotime($order['ORDER_DATE'])) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Payment Status</label>
          <select name="status" class="form-select" required>
            <option value="Paid">Paid</option>
            <option value="Failed">Failed</option>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Payment Method</label>
          <select name="method" class="form-select" required>
            <option value="Card">Card</option>
            <option value="Cash">Cash</option>
            <option value="UPI">UPI</option>
            <option value="Net Banking">Net Banking</option>
          </select>
        </div>

        <div class="text-center">
          <button type="submit" name="submit" class="btn btn-success">Submit Payment</button>
        </div>
      </form>
    </div>
  <?php endif; ?>

  <h2 class="text-center mt-4 fw-bold">Past Payments</h2>

  <?php if (empty($payments)): ?>
    <div class="alert alert-info text-center">No past payments found.</div>
  <?php else: ?>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Payment ID</th>
          <th>Order ID</th>
          <th>Status</th>
          <th>Method</th>
          <th>Payment Date</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($payments as $payment): ?>
          <tr>
            <td><?= $payment['PAYMENT_ID'] ?></td>
            <td><?= $payment['ORDER_ID'] ?></td>
            <td><?= $payment['STATUS'] ?></td>
            <td><?= $payment['METHOD'] ?></td>
            <td><?= date('Y-m-d', strtotime($payment['PAYMENT_DATE'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

</div>

</body>
</html>
