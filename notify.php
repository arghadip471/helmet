<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Decode the incoming JSON payload
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract employee_id and alert_type
    $employee_id = $data['employee_id'] ?? null;
    $alert_type = $data['alert_type'] ?? null;

    // Check for missing fields
    if (!$employee_id || !$alert_type) {
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "Both employee_id and alert_type are required"]);
        exit;
    }
    date_default_timezone_set('Asia/Kolkata');
    $time = date("Y-m-d H:i:s");
    // Create a message based on the alert type
    $message = "";
    switch ($alert_type) {
        case "temperature":
            $message = "High Temperature!!! near Employee $employee_id";
            break;
        case "smoke":
            $message = "Smoke Detected!!! near Employee $employee_id";
            break;
        case "fall detected":
            $message = "Fall Detected!!! Employee $employee_id at $time ";
            break;
        case "gas":
            $message="Gas Detected!!! near Employee $employee_id";
            break;

        default:
            http_response_code(400); // Bad Request
            echo json_encode(["error" => "Invalid alert type"]);
            exit;
    }

    // Send a JSON response
    header("Content-Type: application/json");
    // Return success response with notification message

    echo json_encode([
        "success" => true,
        "employee_id" => $employee_id,
        "alert_type" => $alert_type,
        "message" => $message
    ]);
} else {
    // Handle invalid request methods
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Invalid request method. Only POST is allowed."]);
}
?>
