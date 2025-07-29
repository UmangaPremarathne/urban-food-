<?php
require_once 'db_oracle.php';

// Handle form submission
if (isset($_POST['submit'])) {
    $conn = getConnection();

    $supplier_name = $_POST['supplier_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    try {
        $conn->beginTransaction();
        $supplier_id = rand(1000, 9999); // Use sequence or UUID in production
        $sql_insert = "INSERT INTO URBAN_SUPPLIERS (SUPPLIER_ID, SUPPLIER_NAME, EMAIL, PASSWORD, ADDRESS, PHONE) 
                       VALUES (:supplier_id, :supplier_name, :email, :password, :address, :phone)";
        executeQuery($conn, $sql_insert, [
            ':supplier_id' => $supplier_id,
            ':supplier_name' => $supplier_name,
            ':email' => $email,
            ':password' => $password,
            ':address' => $address,
            ':phone' => $phone
        ]);
        $conn->commit();
        echo "<script>alert('Supplier added successfully!'); window.location.href='Supplier.php';</script>";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Failed to add supplier: " . $e->getMessage();
    }
}

// Fetch all suppliers
$conn = getConnection();
$sql_select = "SELECT * FROM URBAN_SUPPLIERS";
$stmt = $conn->prepare($sql_select);
$stmt->execute();
$suppliers = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UrbanFood - Suppliers</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 80px;
        }
        .navbar-brand img {
            height: 40px;
        }
        .form-card {
            max-width: 600px;
            margin: 0 auto;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#"><img src="img/logo.png" alt="UrbanFood Logo"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="index.html">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="categories.html">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="order.html">Order</a></li>
                <li class="nav-item"><a class="nav-link active" href="Supplier.php">Supplier</a></li>
                <li class="nav-item"><a class="nav-link" href="Customer.html">Customer</a></li>
                <li class="nav-item"><a class="nav-link" href="payment.html">Payment</a></li>
                <li class="nav-item"><a class="nav-link" href="Feedback.html">Feedback</a></li>
                <li class="nav-item"><a class="nav-link" href="login.html">Login</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Header -->
<div class="container text-center mb-4">
    <h2 class="mt-4 fw-bold">Manage Suppliers</h2>
    <p class="text-muted">Add new suppliers and view your existing supplier list.</p>
</div>

<!-- Supplier Form -->
<div class="container mb-5">
    <div class="card shadow form-card p-4">
        <h4 class="card-title mb-3 text-center"><i class="fas fa-user-plus me-2"></i>Add New Supplier</h4>
        <form method="POST" action="Supplier.php">
            <div class="mb-3">
                <label for="supplier_name" class="form-label">Supplier Name</label>
                <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" class="form-control" id="address" name="address" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" class="form-control" id="phone" name="phone" required>
            </div>
            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-primary"><i class="fas fa-plus-circle me-1"></i>Add Supplier</button>
            </div>
        </form>
    </div>
</div>

<!-- Suppliers Table -->
<div class="container mb-5">
    <div class="card shadow p-4">
        <h4 class="card-title mb-3 text-center"><i class="fas fa-list me-2"></i>Supplier List</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Supplier ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($suppliers as $supplier): ?>
                    <tr class="text-center">
                        <td><?php echo $supplier['SUPPLIER_ID']; ?></td>
                        <td><?php echo $supplier['SUPPLIER_NAME']; ?></td>
                        <td><?php echo $supplier['EMAIL']; ?></td>
                        <td><?php echo $supplier['PHONE']; ?></td>
                        <td><?php echo $supplier['ADDRESS']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
