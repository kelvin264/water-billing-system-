<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle correction request actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_id = intval($_POST['request_id']); // Ensure valid integer
    $action = $_POST['action'];
    $stmt = null; // Initialize to prevent "undefined variable" error

    if ($action == "reviewed") {
        $stmt = $conn->prepare("UPDATE correctionrequests SET Status = 'Reviewed' WHERE RequestID = ?");
    } elseif ($action == "resolved") {
        $stmt = $conn->prepare("UPDATE correctionrequests SET Status = 'Resolved' WHERE RequestID = ?");
    }

    if ($stmt) {
        $stmt->bind_param("i", $request_id);
        if ($stmt->execute()) {
            $success = "✅ Correction request updated successfully!";
        } else {
            $error = "❌ Error: " . $stmt->error;
        }
        $stmt->close(); // Close statement after execution
    }
}

// Fetch all correction requests
$result = $conn->query("SELECT cr.RequestID, c.Name, w.Date, w.AmountDue, cr.Description, cr.Status 
                        FROM CorrectionRequests cr 
                        JOIN WaterBills w ON cr.BillID = w.BillID 
                        JOIN Customers c ON cr.CustomerID = c.CustomerID 
                        ORDER BY cr.Status ASC, cr.RequestID DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Corrections</title>
    <link rel="icon" type="image/png" href="favicon.png"> <!-- Favicon Added -->
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; margin: 0; padding: 0; }
        .container { width: 90%; margin: auto; padding: 20px; }
        .sidebar { width: 250px; background: #343a40; color: white; height: 100vh; position: fixed; padding: 20px; }
        .sidebar h2 { text-align: center; }
        .sidebar a { display: block; color: white; padding: 10px; text-decoration: none; margin: 10px 0; border-radius: 5px; }
        .sidebar a:hover { background: #dc3545; }
        .main-content { margin-left: 270px; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #007bff; color: white; }
        tr:hover { background: #f1f1f1; }
        .btn { padding: 6px 12px; text-decoration: none; color: white; border-radius: 5px; cursor: pointer; }
        .btn-reviewed { background: #ffc107; } /* Yellow for Reviewed */
        .btn-resolved { background: #28a745; } /* Green for Resolved */
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
        <h2>Manage Correction Requests</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        
        <table>
            <tr>
                <th>Customer</th>
                <th>Bill Date</th>
                <th>Amount Due</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['Name']) ?></td>
                    <td><?= htmlspecialchars($row['Date']) ?></td>
                    <td>KSH<?= number_format($row['AmountDue'], 2) ?></td>
                    <td><?= htmlspecialchars($row['Description']) ?></td>
                    <td><?= $row['Status'] ?></td>
                    <td>
                        <?php if ($row['Status'] == 'Pending') { ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?= $row['RequestID'] ?>">
                                <button type="submit" name="action" value="reviewed" class="btn btn-reviewed">Mark as Reviewed</button>
                            </form>
                        <?php } ?>

                        <?php if ($row['Status'] == 'Reviewed') { ?>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="request_id" value="<?= $row['RequestID'] ?>">
                                <button type="submit" name="action" value="resolved" class="btn btn-resolved">Mark as Resolved</button>
                            </form>
                        <?php } ?>

                        <?php if ($row['Status'] == 'Resolved') { ?>
                            ✅ Resolved
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
