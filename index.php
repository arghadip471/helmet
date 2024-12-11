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

$sql = "SELECT * FROM employeedata ORDER BY entry_time DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Records</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
            color: #444;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007BFF;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        tr:last-child td {
            border-bottom: none;
        }

        td {
            color: #555;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            table {
                width: 95%;
            }

            th, td {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
    <script>
        // Refresh the page every second
        setInterval(() => {
            location.reload();
        }, 1000); // 1000 milliseconds = 1 second
    </script>
</head>
<body>
    <h1>Employee Data</h1>
    <table>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Area</th>
                <th>Entry Time</th>
                <th>Exit Time</th>
                <th>Temperature</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['employee_id']}</td>
                            <td>{$row['employee_name']}</td>
                            <td>{$row['area']}</td>
                            <td>{$row['entry_time']}</td>
                            <td>{$row['exit_time']}</td>
                            <td>{$row['temperature']}</td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>No records found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
<?php $conn->close(); ?>
