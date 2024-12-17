<?php
$employee_id = '';
$temperature = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Content-Type: application/json");

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['employee_id']) || !isset($data['temperature'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid data received.']);
        exit;
    }

    $employee_id = $data['employee_id'];
    $alert_type=$data['alert_type'];

    echo json_encode([
        'success' => true,
        'notification_message' => "Employee ID: $employee_id, Temperature: $temperature",
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }

        h1 {
            text-align: center;
            color: #2196F3;
            margin-top: 20px;
        }

        /* Header Styling */
        .header-container {
            text-align: center;
            margin: 20px auto;
        }

        .header-container h1 {
            color: #2196F3;
            font-weight: bold;
        }

        /* Notification Panel */
        #notificationPanel {
            margin: 20px auto;
            width: 90%;
            max-width: 800px;
            border: 2px solid #000; /* Black border */
            border-radius: 5px;
            padding: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            background-color: #ffffff;
        }

        #notificationPanel h2 {
            color: #2196F3;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        /* Notification Items */
        .notification-item {
            margin: 10px 0;
            padding: 8px 12px;
            font-size: 1rem;
            background-color: #ffffff;
            border-left: 4px solid #2196F3;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .notification-item strong {
            color: #000000;
        }

        /* Button Styling */
        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .redirect-button {
            background-color: #2196F3;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .redirect-button:hover {
            background-color: #1976D2;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const notificationPanel = document.getElementById("notificationList");

            // Echo PHP variables into JavaScript
            const employeeId = "<?php echo htmlspecialchars($employee_id, ENT_QUOTES, 'UTF-8'); ?>";
            const alert = "<?php echo htmlspecialchars($alert_type, ENT_QUOTES, 'UTF-8'); ?>";
            console.log(employeeId);
            console.log(alert);

            // Request browser notification permission
            if ("Notification" in window && Notification.permission === "default") {
                Notification.requestPermission().then(permission => {
                    console.log("Notification permission:", permission);
                });
            }

            // Fetch notifications from the server
            async function fetchNotification() {
                try {
                    const res = await fetch("http://localhost:3000/helmet/notify.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            employee_id: employeeId  ,
                            alert_type: alert ,
                        }),
                    });

                    if (!res.ok) {
                        throw new Error(`HTTP error! Status: ${res.status}`);
                    }

                    const data = await res.json();

                    if (data.message) {
                        const listItem = document.createElement("div");
                        listItem.className = "notification-item";
                        listItem.innerHTML = `<strong>${data.message}</strong>`;
                        notificationPanel.appendChild(listItem);

                        // Trigger browser notification
                        if (Notification.permission === "granted") {
                            new Notification("Employee Notification", {
                                body: data.notification_message,
                                icon: "https://via.placeholder.com/128",
                            });
                        }
                    } else {
                        console.error("Invalid data received from server:", data);
                    }
                } catch (error) {
                    console.error("Error fetching notification:", error);
                }
            }

            fetchNotification();

            // Redirect button functionality
            document.getElementById("redirectButton").addEventListener("click", function () {
                window.location.href = "http://localhost:3000/helmet/index.php";
            });
        });
    </script>
</head>
<body>
    <div class="header-container">
        <h1>HELI SMART</h1>
    </div>
    <div id="notificationPanel">
        <h2>🔔 Notification Panel</h2>
        <div id="notificationList">
            <!-- Notifications will appear here -->
        </div>
    </div>
    <div class="button-container">
        <button id="redirectButton" class="redirect-button">Employee Data</button>
    </div>
</body>
</html>
