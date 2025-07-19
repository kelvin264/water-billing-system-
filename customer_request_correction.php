<?php
include 'config.php';
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Check if the bill ID is provided
if (!isset($_GET['bill_id'])) {
    header("Location: customer_dashboard.php");
    exit();
}

$bill_id = $_GET['bill_id'];

// Fetch bill details
$sql = "SELECT * FROM WaterBills WHERE BillID = ? AND CustomerID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $bill_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$bill = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reason = trim($_POST['reason']);

    if (!empty($reason)) {
        $insert_sql = "INSERT INTO CorrectionRequests (BillID, CustomerID, Reason, Status) VALUES (?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("iis", $bill_id, $customer_id, $reason);
        
        if ($stmt->execute()) {
            $success_msg = "Your correction request has been submitted!";
        } else {
            $error_msg = "Something went wrong. Please try again.";
        }
    } else {
        $error_msg = "Please provide a reason for the correction request.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Correction</title>
    <style>
        /* General Page Styling */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #16a085, #138d75);
            margin: 0;
            padding: 0;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Container Styling */
        .container {
            width: 90%;
            max-width: 500px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        /* Alert Styling */
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
            display: inline-block;
            width: 100%;
        }

        .success {
            background: #2ecc71;
            color: white;
        }

        .error {
            background: #e74c3c;
            color: white;
        }

        /* Labels */
        label {
            font-weight: bold;
            display: block;
            text-align: left;
            margin-top: 10px;
        }

        /* Bill Details */
        .bill-info {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            text-align: left;
            margin-bottom: 15px;
            font-size: 15px;
        }

        /* Input Fields */
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            resize: none;
            font-size: 15px;
            transition: 0.3s;
        }

        textarea:focus {
            border-color: #16a085;
            box-shadow: 0px 0px 5px rgba(22, 160, 133, 0.5);
            outline: none;
        }

        /* Button */
        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            background: #16a085;
            transition: 0.3s;
        }

        .btn:hover {
            background: #138d75;
            transform: translateY(-2px);
            box-shadow: 0px 5px 10px rgba(22, 160, 133, 0.2);
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Request Correction</h2>

        <?php if (isset($success_msg)) echo "<div class='alert success'>$success_msg</div>"; ?>
        <?php if (isset($error_msg)) echo "<div class='alert error'>$error_msg</div>"; ?>

        <div class="bill-info">
            <p><strong>Bill ID:</strong> <?= $bill['BillID'] ?></p>
            <p><strong>Date:</strong> <?= $bill['Date'] ?></p>
            <p><strong>Water Consumption:</strong> <?= $bill['WaterConsumption'] ?> m³</p>
            <p><strong>Amount Due:</strong> $<?= number_format($bill['AmountDue'], 2) ?></p>
        </div>

        <form method="post">
            <label>Reason for Correction:</label>
            <textarea name="reason" rows="4" placeholder="Explain why this bill is incorrect..."></textarea>

            <button type="submit" class="btn">Submit Correction Request</button>
        </form>
    </div>
</body>
</html>
