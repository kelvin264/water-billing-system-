<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = $_POST['customer_id'];
    $date = $_POST['date'];
    $water_consumption = $_POST['water_consumption'];
    $amount_due = $_POST['amount_due'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO WaterBills (CustomerID, Date, WaterConsumption, AmountDue, Status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issds", $customer_id, $date, $water_consumption, $amount_due, $status);

    if ($stmt->execute()) {
        $success = "✅ Bill added successfully!";
    } else {
        $error = "❌ Error: " . $conn->error;
    }
}

$result = $conn->query("SELECT w.BillID, c.Name, w.Date, w.WaterConsumption, w.AmountDue, w.Status FROM WaterBills w JOIN Customers c ON w.CustomerID = c.CustomerID");
$customers = $conn->query("SELECT CustomerID, Name FROM Customers");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/png" href="favicon.png"> <!-- Favicon Added -->
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            background: url('background.png') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: auto;
            padding: 20px;
        }

        .sidebar {
            width: 250px;
            background: #343a40;
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px;
        }

        .sidebar h2 {
            text-align: center;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            text-decoration: none;
            margin: 10px 0;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background: #dc3545;
        }

        .main-content {
            margin-left: 270px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        .form-container {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background: #007bff;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border-radius: 5px;
            overflow: hidden;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #007bff;
            color: white;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
        }

        .btn-review {
            background: #ffc107;
        }

        .btn-paid {
            background: #28a745;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="admin_dashboard.php">Manage Bills</a>
        <a href="admin_manage_corrections.php">Manage Corrections</a>
        <a href="admin_update_customer.php">Update Customers</a>
        <a href="admin_logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h2>All Customer Bills</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        
        <div class="form-container">
            <h3>Add New Bill</h3>
            <form method="post">
                <label for="customer_id">Customer:</label>
                <select name="customer_id" required>
                    <option value="">-- Select Customer --</option>
                    <?php while ($cust = $customers->fetch_assoc()) { ?>
                        <option value="<?= $cust['CustomerID'] ?>"><?= $cust['Name'] ?></option>
                    <?php } ?>
                </select>
                
                <label for="date">Date:</label>
                <input type="date" name="date" required>
                
                <label for="water_consumption">Water Consumption (m³):</label>
                <input type="number" name="water_consumption" step="0.01" required>
                
                <label for="amount_due">Amount Due (KSH):</label>
                <input type="number" name="amount_due" step="0.01" required>
                
                <label for="status">Status:</label>
                <select name="status" required>
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                </select>
                
                <button type="submit">Add Bill</button>
            </form>
        </div>

        <table>
            <tr>
                <th>Customer</th>
                <th>Date</th>
                <th>Consumption (m³)</th>
                <th>Amount Due</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['Name'] ?></td>
                    <td><?= $row['Date'] ?></td>
                    <td><?= $row['WaterConsumption'] ?> m³</td>
                    <td>KSH<?= number_format($row['AmountDue'], 2) ?></td>
                    <td><?= $row['Status'] ?></td>
                    <td>
                        <a class="btn btn-review" href="admin_manage_corrections.php?bill_id=<?= $row['BillID'] ?>">Manage Corrections</a>
                        <a class="btn btn-paid" href="admin_mark_paid.php?bill_id=<?= $row['BillID'] ?>">Mark as Paid</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
