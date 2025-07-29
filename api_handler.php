<?php
include 'db_oracle.php';

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($data->action)) {
    if ($data->action === "add_payment") {
        $stmt = $conn->prepare("INSERT INTO Payments (paymet_ID, Order_ID, method, date, status) 
                               VALUES (:payment_ID, :order_ID, :payment_method, :date, :status)");
        $stmt->execute([
            ":payment_ID" => $data->payment_ID,
            ":order_ID" => $data->order_ID,
            ":method" => $data->method,
            ":date" => $data->date,
            ":status" => "Pending"
        ]);
        echo json_encode(["message" => "Payment added successfully"]);
    } elseif ($data->action === "update_payment_status") {
        $stmt = $conn->prepare("UPDATE Payments SET PaymentStatus = :status WHERE Payment_ID = :payment_ID");
        $stmt->execute([
            ":status" => $data->status,
            ":payment_ID" => $data->payment_ID
        ]);
        echo json_encode(["message" => "Payment status updated"]);
    }
}

// Handle Supplier actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $address = $_POST['address'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $category = $_POST['category'] ?? '';

        if (empty($name) || empty($email) || empty($password)) {
            echo "Please fill all required fields.";
            exit;
        }

        $conn = getConnection();
        $sql = "INSERT INTO suppliers (name, email, password, address, phone, category) 
                VALUES (:name, :email, :password, :address, :phone, :category)";
        $params = [
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,
            ':address' => $address,
            ':phone' => $phone,
            ':category' => $category
        ];

        $stmt = executeQuery($conn, $sql, $params);
        if ($stmt) {
            echo "Supplier added successfully!";
        }
    }
}
?>
