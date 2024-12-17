<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers for CORS and JSON handling
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Start a session to store notifications
session_start();

// Initialize notifications array if not already set
if (!isset($_SESSION['notifications'])) {
    $_SESSION['notifications'] = [];
}

// Handle POST requests from ESP
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Decode the incoming JSON payload
    $data = json_decode(file_get_contents('php://input'), true);

    // Validate incoming data
    $employee_id = $data['employee_id'] ?? null;
    $alert_type = $data['alert_type'] ?? null;

    if (!$employee_id || !$alert_type) {
        http_response_code(400); // Bad Request
        echo json_encode(["error" => "Both employee_id and alert_type are required."]);
        exit;
    }

    // Format the message
    date_default_timezone_set('Asia/Kolkata');
    $time = date("Y-m-d H:i:s");
    $message = "Employee ID: $employee_id is detected with a '$alert_type' alert at $time.";

    // Store the notification in the session
    $_SESSION['notifications'][] = $message;

    // Respond with success
    echo json_encode(["success" => true, "message" => $message]);

}

// For GET requests, display the HTML page
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        h1 {
            text-align: center;
            color: #333;
            margin: 20px 0;
        }

        #notificationPanel {
            width: 90%;
            max-width: 800px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 15px;
            overflow-y: auto;
            max-height: 400px;
        }

        #notificationPanel h2 {
            font-size: 1.5em;
            color: #555;
            margin-bottom: 15px;
        }

        .notification-item {
            padding: 10px;
            margin-bottom: 10px;
            border-left: 4px solid #007bff;
            background: #f9f9f9;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <h1>Notification Panel</h1>
    <div id="notificationPanel">
        <h2>Incoming Notifications</h2>
        <div id="notificationList">
            <?php
            // Display all notifications stored in the session
            if (!empty($_SESSION['notifications'])) {
                foreach ($_SESSION['notifications'] as $notification) {
                    echo "<div class='notification-item'>" . htmlspecialchars($notification) . "</div>";
                }
            } else {
                echo "<div class='notification-item'>No notifications yet.</div>";
            }
            ?>
        </div>
    </div>
</body>

</html>
