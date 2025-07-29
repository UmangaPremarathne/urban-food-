<?php

// Define your connection parameters globally
$host = 'localhost';   // Hostname or IP
$port = '1521';        // Oracle port
$sid = 'xe';           // Oracle SID (change if necessary)
$username = 'system';  // Oracle DB username
$password = 'oneli123';// Oracle DB password

// Establish database connection using PDO
function getConnection() {
    global $host, $port, $sid, $username, $password;

    try {
        // Define the PDO connection string
        $dsn = "oci:dbname=//{$host}:{$port}/{$sid}";

        // Create the PDO instance
        $connection = new PDO($dsn, $username, $password);

        // Set error mode to exception for better debugging
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connection;
    } catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
        exit;
    }
}

// Function to handle SQL execution and return result
function executeQuery($conn, $sql, $params = []) {
    try {
        // Prepare the SQL statement
        $stmt = $conn->prepare($sql);

        // Bind parameters if any
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                // Check if the key is numeric (for positional parameters)
                if (is_numeric($key)) {
                    $stmt->bindValue($key + 1, $value);  // 1-based index for positional binding
                } else {
                    $stmt->bindValue($key, $value);  // Named parameter binding
                }
            }
        }

        // Execute the query
        $stmt->execute();

        return $stmt; // Return statement object to fetch results if needed
    } catch (PDOException $e) {
        echo "Error executing query: " . $e->getMessage();
        return false;
    }
}
?>
