<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employee";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Receive POST data from ESP
if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Retrieve data from the form
        // Get JSON input
    $data = json_decode(file_get_contents('php://input'), true);

    // Access fields
    $employee_id = $data['employee_id'] ?? '';
    $temperature=$data['temperature'] ??'';
    
    // Insert data into database
    $sql_update = "UPDATE employeedata 
                           SET  temperature = '$temperature'  
                           WHERE employee_id = '$employee_id' ";

    if ($conn->query($sql) === TRUE) {
        echo "Data inserted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}



       

$conn->close();
?>