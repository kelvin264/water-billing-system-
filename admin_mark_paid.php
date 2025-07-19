<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if (!isset($_GET['bill_id'])) {
    die("Invalid request.");
}

$bill_id = $_GET['bill_id'];

// Update bill status to Paid
$stmt = $conn->prepare("UPDATE WaterBills SET Status = 'Paid' WHERE BillID = ?");
$stmt->bind_param("i", $bill_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "✅ Bill marked as Paid successfully!";
} else {
    $_SESSION['error'] = "❌ Error updating bill: " . $conn->error;
}

header("Location: admin_dashboard.php");
exit();
?>
