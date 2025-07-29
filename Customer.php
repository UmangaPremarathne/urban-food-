<?php
require_once 'db_oracle.php';

$conn = getConnection();

// Handle create or update
if (isset($_POST['submit'])) {
    $id = $_POST['customer_id'] ?? rand(1000, 9999);
    $name = $_POST['customer_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $date_register = $_POST['date_register'];

    try {
        $conn->beginTransaction();

        if (isset($_POST['update_mode'])) {
            // Update existing
            $sql = "UPDATE URBAN_CUSTOMERS 
                    SET CUSTOMER_NAME = :name, EMAIL = :email, PASSWORD = :password,
                        ADDRESS = :address, DATE_REGISTER = TO_DATE(:date_register, 'YYYY-MM-DD') 
                    WHERE CUSTOMER_ID = :id";
        } else {
            // Insert new
            $sql = "INSERT INTO URBAN_CUSTOMERS 
                    (CUSTOMER_ID, CUSTOMER_NAME, EMAIL, PASSWORD, ADDRESS, DATE_REGISTER)
                    VALUES (:id, :name, :email, :password, :address, TO_DATE(:date_register, 'YYYY-MM-DD'))";
        }

        executeQuery($conn, $sql, [
            ':id' => $id,
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,
            ':address' => $address,
            ':date_register' => $date_register
        ]);

        $conn->commit();
        header("Location: Customer.php");
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        executeQuery($conn, "DELETE FROM URBAN_CUSTOMERS WHERE CUSTOMER_ID = :id", [':id' => $delete_id]);
        header("Location: Customer.php");
        exit;
    } catch (Exception $e) {
        echo "Error deleting: " . $e->getMessage();
    }
}

// Fetch customers
$customers = executeQuery($conn, "SELECT * FROM URBAN_CUSTOMERS ORDER BY CUSTOMER_ID DESC")->fetchAll();

// For editing
$edit_customer = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM URBAN_CUSTOMERS WHERE CUSTOMER_ID = :id");
    $stmt->execute([':id' => $edit_id]);
    $edit_customer = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UrbanFood - Customer Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding-top: 80px; }
        .form-card { max-width: 600px; margin: auto; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="#"><img src="img/logo.png" alt="UrbanFood" height="40"></a>
    </div>
</nav>

<!-- Header -->
<div class="container text-center mb-4">
    <h2 class="mt-4 fw-bold"><?= $edit_customer ? 'Update Customer' : 'Register Customer' ?></h2>
</div>

<!-- Registration / Update Form -->
<div class="container mb-5">
    <div class="card p-4 shadow form-card">
        <form method="POST" action="Customer.php">
            <?php if ($edit_customer): ?>
                <input type="hidden" name="update_mode" value="1">
                <input type="hidden" name="customer_id" value="<?= $edit_customer['CUSTOMER_ID'] ?>">
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label">Customer Name</label>
                <input type="text" class="form-control" name="customer_name" required value="<?= $edit_customer['CUSTOMER_NAME'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required value="<?= $edit_customer['EMAIL'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="text" class="form-control" name="password" required value="<?= $edit_customer['PASSWORD'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" class="form-control" name="address" required value="<?= $edit_customer['ADDRESS'] ?? '' ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Date Registered</label>
                <input type="date" class="form-control" name="date_register" required value="<?= isset($edit_customer['DATE_REGISTER']) ? date('Y-m-d', strtotime($edit_customer['DATE_REGISTER'])) : '' ?>">
            </div>
            <div class="text-center">
                <button type="submit" name="submit" class="btn btn-success"><?= $edit_customer ? 'Update' : 'Register' ?></button>
                <?php if ($edit_customer): ?>
                    <a href="Customer.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Customer Table -->
<div class="container">
    <h3 class="text-center fw-bold mb-3">Registered Customers</h3>
    <div class="card p-3 shadow">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark text-center">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Address</th>
                    <th>Date Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customers as $cust): ?>
                    <tr class="text-center">
                        <td><?= $cust['CUSTOMER_ID'] ?></td>
                        <td><?= $cust['CUSTOMER_NAME'] ?></td>
                        <td><?= $cust['EMAIL'] ?></td>
                        <td><?= $cust['PASSWORD'] ?></td>
                        <td><?= $cust['ADDRESS'] ?></td>
                        <td><?= date('Y-m-d', strtotime($cust['DATE_REGISTER'])) ?></td>
                        <td>
                            <a href="Customer.php?edit_id=<?= $cust['CUSTOMER_ID'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="Customer.php?delete_id=<?= $cust['CUSTOMER_ID'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this customer?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($customers)): ?>
                    <tr><td colspan="7" class="text-center text-muted">No customers found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
