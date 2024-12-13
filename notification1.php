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

$notification_message = "Waiting for data...";

// Process POST data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['employee_id']) && isset($data['temperature'])) {
        $employee_id = $data['employee_id'] ??'';
        
        $temperature = $data['temperature']??'';
        

        if (!empty($employee_id) && !empty($temperature)) {
            $sql = "INSERT INTO employeedata (employee_id, temperature, entry_time) 
                    VALUES ('$employee_id', '$temperature', NOW())";

            if ($conn->query($sql) === TRUE) {
                $notification_message = "Employee ID: $employee_id recorded with temperature: $temperature";
                
            } else {
                $notification_message = "Error: " . $conn->error;
                
            }
        } else {
            $notification_message = "Invalid data received: Missing employee ID or temperature.";
        }
    } else {
        $notification_message = "Invalid JSON data received.";
    }
}

// Return the notification message as JSON
header('Content-Type: application/json');

echo json_encode(['notification_message' => $notification_message]);

$conn->close();
?>
