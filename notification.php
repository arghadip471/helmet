<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "employee";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

$notification_message = "Waiting for data...";
$temperature_threshold = 37.5; // Set the temperature threshold

// Process POST Request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['employee_id']) || !isset($data['temperature'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid data received.']);
        exit;
    }

    $employee_id = $data['employee_id'];
    $temperature = $data['temperature'];

    // Fetch employee name
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
    date_default_timezone_set('Asia/Kolkata');
    $entry_time = date("Y-m-d H:i:s");
    if ($area === "area_default") {
        $notification_message = "$employee_name entered at $entry_time";
    } elseif ($area === "area_1") {
        $notification_message = "$employee_name exited at $entry_time";
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