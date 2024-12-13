<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Helmet Alert System</title>
    <style>
        body, h1, p, div {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background-color: #f4f7fb;
            color: #333;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            flex-direction: column;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-align: center;
            color: #3b5998;
        }

        #alerts {
            width: 100%;
            max-width: 800px;
            margin-top: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            overflow-y: auto;
            height: 300px;
        }

        .alert {
            background-color: #ffeb3b;
            color: #212121;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .alert:hover {
            transform: scale(1.05);
        }

        .alert .worker-id {
            font-weight: bold;
        }

        .alert .alert-type {
            font-style: italic;
        }

        .timestamp {
            font-size: 0.9rem;
            color: #888;
            margin-top: 10px;
        }

        #current-time {
            font-size: 1.3rem;
            color: #444;
            margin-top: 20px;
            font-weight: bold;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 8px;
        }

        .bell {
            position: fixed;
            top: 20px;
            right: 20px;
            font-size: 2rem;
            cursor: pointer;
            color: #3b5998;
            transition: transform 0.3s ease;
        }

        .bell:hover {
            transform: scale(1.1);
        }

    </style>
</head>
<body>
    <h1>Helmet Alert System</h1>

    <div id="alerts"></div> <!-- Alerts container -->

    <div id="current-time"></div> <!-- Real-time clock -->

    <div class="bell" onclick="playNotificationSound()">🔔</div> <!-- Notification bell icon -->

    <script>
        // Function to fetch alerts from the server and process them
async function fetchAlerts() {
    try {
        // Fetch the JSON data from the server
        const response = await fetch('helmet_alerts.json');
        if (response.ok) {
            const alerts = await response.json(); // Parse the JSON response

            const alertContainer = document.getElementById('alerts');
            alertContainer.innerHTML = ''; // Clear previous alerts

            if (alerts.length > 0) {
                const alert = alerts[0]; // Process the first alert

                // Display the alert on the webpage
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert';
                alertDiv.innerHTML = `
                    <div><span class="worker-id">Worker ID:</span> ${alert.worker_id}</div>
                    <div><span class="alert-type">Alert Type:</span> ${alert.alert_type}</div>
                    <div class="timestamp"><strong>Timestamp:</strong> ${alert.timestamp}</div>
                `;
                alertContainer.appendChild(alertDiv);

                // Get the last notification timestamp from localStorage
                const lastNotificationTimestamp = localStorage.getItem('lastNotificationTimestamp');

                // Compare timestamps and trigger notification if it's a new alert
                if (alert.timestamp !== lastNotificationTimestamp) {
                    // Check for Notification API support and permission
                    if ("Notification" in window) {
                        if (Notification.permission === "granted") {
                            // Trigger a new notification
                            new Notification(Helmet Alert: ${alert.alert_type}, {
                                body: Worker ID: ${alert.worker_id}\nTime: ${alert.timestamp}
                            });
                        } else if (Notification.permission !== "denied") {
                            // Request permission if not already denied
                            Notification.requestPermission().then(permission => {
                                if (permission === "granted") {
                                    new Notification(Helmet Alert: ${alert.alert_type}, {
                                        body: Worker ID: ${alert.worker_id}\nTime: ${alert.timestamp}
                                    });
                                }
                            });
                        }
                    }

                    // Update localStorage with the new timestamp
                    localStorage.setItem('lastNotificationTimestamp', alert.timestamp);
                }
            }
        }
    } catch (error) {
        console.error("Error fetching alerts:", error);
    }
}

// Call the function on page load and set it to repeat every 10 seconds
fetchAlerts();
setInterval(fetchAlerts, 10000); // Adjust the interval as needed

        
    </script>
</body>
</html>