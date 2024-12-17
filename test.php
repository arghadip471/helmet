<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


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


date_default_timezone_set('Asia/Kolkata');
$current_time = date('Y-m-d H:i'); // Current timestamp

// Check if employee_id exists and get the area
$sql_check = "SELECT area FROM employeedata WHERE employee_id = '$employee_id'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    // Record exists
    $row = $result->fetch_assoc();
    $area = $row['area'];

    if ($area === "area_default") {
        // Update entry_time
        $sql_update = "UPDATE employeedata 
                       SET entry_time = '$current_time', area = 'area_1', exit_time='0' 
                       WHERE employee_id = '$employee_id' AND area = 'area_default'";
        if ($conn->query($sql_update) === TRUE) {
            echo "Entry time updated successfully.";
        } else {
            echo "Error updating entry time: " . $conn->error;
        }
    } elseif ($area === "area_1") {
        // Update exit_time
        $sql_update = "UPDATE employeedata 
                       SET exit_time = '$current_time', area = 'area_default'  
                       WHERE employee_id = '$employee_id' AND area = 'area_1'";
        if ($conn->query($sql_update) === TRUE) {
            echo "Exit time updated successfully.";
        } else {
            echo "Error updating exit time: " . $conn->error;
        }
    } else {
        echo "Area is not valid for employee.";
    }
} else {
    echo "Employee ID not found.";
}
    $employee_query = "SELECT employee_name,area FROM employeedata WHERE employee_id = ?";
    $stmt = $conn->prepare($employee_query);
    $stmt->bind_param("s", $employee_id);

    if (!$stmt->execute()) {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Database query failed: ' . $stmt->error]);
        exit;
    }

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404); // Not Found
        echo json_encode(['error' => 'Employee ID not found.']);
        exit;
    }

    $row = $result->fetch_assoc();
    $employee_name = $row['employee_name'];
    $area = $row['area'];

    // Generate notification message based on area
    $entry_time = date("Y-m-d H:i:s");
    if ($area === "area_default") {
        $notification_message = "$employee_name exited at $entry_time";
    } elseif ($area === "area_1") {
        $notification_message = "$employee_name entered at $entry_time";
    } else {
        $notification_message = "$employee_name moved to an $area area at $entry_time";
    }
    header("Content-Type: application/json");
    // Return success response with notification message
    echo json_encode(['success' => true, 'notification_message' => $notification_message]);
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method.']);
}




       

$conn->close();
?>