<?php
// Database credentials
$username = "root";
$password = "";
$host = "localhost";
$port = "3306";
$database = "lemonade";

// Create connection
$conn = mysqli_init();
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);
mysqli_real_connect($conn, $host, $username, $password, $database, $port);

if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    // SQL to delete table
    $sql = "DROP TABLE address";

    if ($conn->query($sql) === TRUE) {
        echo "Table deleted successfully";
    } else {
        echo "Error deleting table: " . $conn->error;
    }
}
$conn->close();