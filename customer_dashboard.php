<?php
include 'config.php';
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: index.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];
$result = $conn->query("SELECT * FROM WaterBills WHERE CustomerID = '$customer_id' ORDER BY Date DESC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bill_id = $_POST['bill_id'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO correctionrequests (BillID, CustomerID, Description, Status) VALUES (?, ?, ?, 'Pending')");
    $stmt->bind_param("iis", $bill_id, $customer_id, $description);

    if ($stmt->execute()) {
        $success = "✅ Your correction request has been submitted successfully!";
    } else {
        $error = "❌ Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link rel="icon" type="image/png" href="favicon.png"> <!-- Favicon Added -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 90%;
            margin: auto;
            padding: 20px;
        }
        .sidebar {
            width: 250px;
            background: #23272a;
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px;
            transition: 0.3s;
        }
        .sidebar h2 {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .sidebar a {
            display: block;
            color: white;
            padding: 12px;
            text-decoration: none;
            margin: 10px 0;
            border-radius: 5px;
            font-size: 16px;
            transition: 0.3s;
        }
        .sidebar a:hover {
            background: #008cff;
        }
        .main-content {
            margin-left: 270px;
            padding: 30px;
        }
        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #007bff;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
            font-size: 16px;
        }
        tr:hover {
            background: #f1f1f1;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            color: white;
            background: #28a745;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #218838;
        }
        .form-container {
            background: white;
            padding: 20px;
            margin-top: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-container h3 {
            margin-bottom: 15px;
            font-size: 20px;
            color: #007bff;
        }
        select, textarea, button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: 0.3s;
        }
        button {
            background: #007bff;
            color: white;
            cursor: pointer;
            font-size: 16px;
            border: none;
            transition: 0.3s;
        }
        button:hover {
            background: #0056b3;
        }
        .success {
            color: green;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .error {
            color: red;
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Customer Dashboard</h2>
        <a href="customer_logout.php">🚪 Logout</a>
    </div>

    <div class="main-content">
        <h2>💧 Your Water Bills</h2>
        <?php 
            if (isset($error)) echo "<p class='error'>$error</p>";
            if (isset($success)) echo "<p class='success'>$success</p>";
        ?>
        <table>
            <tr>
                <th>Date</th>
                <th>Consumption (m³)</th>
                <th>Amount Due</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['Date'] ?></td>
                    <td><?= $row['WaterConsumption'] ?> m³</td>
                    <td>KSH<?= number_format($row['AmountDue'], 2) ?></td>
                    <td><?= $row['Status'] ?></td>
                    <td>
                        <a class="btn" href="download_bill.php?bill_id=<?= $row['BillID'] ?>">⬇️ Download Bill</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
<!-- Correction Request Form -->
<div class="form-container">
            <h3>📝 Request a Bill Correction</h3>
            <form method="post">
                <label for="bill_id">Select Bill:</label>
                <select name="bill_id" required>
                    <option value="">-- Select a Bill --</option>
                    <?php
                    $bills = $conn->query("SELECT BillID, Date, AmountDue FROM WaterBills WHERE CustomerID = '$customer_id'");
                    while ($bill = $bills->fetch_assoc()) {
                        echo "<option value='{$bill['BillID']}'>Bill on {$bill['Date']} - $ {$bill['AmountDue']}</option>";
                    }
                    ?>
                </select>
                <label for="description">Describe the Issue:</label>
                <textarea name="description" rows="4" placeholder="Explain the issue with the bill" required></textarea>
                <button type="submit">📨 Submit Request</button>
        <!-- Correction Requests Status -->
        <div class="form-container">
            <h3>📋 Correction Request Status</h3>
            <table>
                <tr>
                    <th>Bill ID</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
                <?php 
                $requests = $conn->query("SELECT * FROM correctionrequests WHERE CustomerID = '$customer_id'");
                while ($request = $requests->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $request['BillID'] ?></td>
                        <td><?= htmlspecialchars($request['Description']) ?></td>
                        <td><?= $request['Status'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>
</html>
