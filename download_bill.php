<?php
include 'config.php';
session_start();

if (!isset($_SESSION['customer_id'])) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['bill_id'])) {
    die("Invalid request.");
}

$bill_id = $_GET['bill_id'];
$customer_id = $_SESSION['customer_id'];

// Fetch bill details
$result = $conn->query("SELECT * FROM WaterBills WHERE BillID = '$bill_id' AND CustomerID = '$customer_id'");
if ($result->num_rows == 0) {
    die("No bill found.");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Water Bill</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7fc;
            text-align: center;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: left;
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 15px;
        }
        .bill-details p {
            font-size: 16px;
            color: #333;
            padding: 5px 0;
        }
        .btn {
            display: block;
            width: 100%;
            text-align: center;
            padding: 12px;
            font-size: 16px;
            color: white;
            background: #007bff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 15px;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container" id="bill">
        <h2>💧 Water Bill Receipt</h2>
        <div class="bill-details">
            <p><strong>📅 Bill Date:</strong> <?= $row['Date'] ?></p>
            <p><strong>💦 Water Consumption:</strong> <?= $row['WaterConsumption'] ?> m³</p>
            <p><strong>💰 Amount Due:</strong> $<?= number_format($row['AmountDue'], 2) ?></p>
            <p><strong>🔹 Status:</strong> <?= $row['Status'] ?></p>
        </div>
    </div>
    <button class="btn" onclick="downloadPDF()">⬇️ Download PDF</button>

    <script>
        function downloadPDF() {
            const element = document.getElementById('bill');
            html2pdf().from(element).save('Water_Bill_<?= $bill_id ?>.pdf');
        }
    </script>
</body>
</html>
