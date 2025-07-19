<?php
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$message = ""; // Store success/error message

if (isset($_POST['update'])) {
    $customer_id = $_POST['customer_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    $sql = "UPDATE Customers SET Name=?, Email=?, Address=? WHERE CustomerID=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $email, $address, $customer_id);

    if ($stmt->execute()) {
        $message = "<p class='success'>✅ Customer details updated successfully.</p>";
    } else {
        $message = "<p class='error'>❌ Error updating record: " . $conn->error . "</p>";
    }
}

// Fetch customers
$result = $conn->query("SELECT * FROM Customers");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Customer</title>
    <link rel="icon" type="image/png" href="favicon.png"> <!-- Favicon Added -->
    <style>
        /* General Styles */
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #1e3c72, #2a5298);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* Card */
        .container {
            background: white;
            padding: 30px;
            width: 420px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .container:hover {
            transform: scale(1.03);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.4);
        }

        /* Headings */
        h2 {
            color: #1e3c72;
            margin-bottom: 15px;
        }

        /* Form Fields */
        input, select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border 0.3s ease;
        }
        input:focus, select:focus {
            border-color: #1e3c72;
            outline: none;
        }

        /* Button */
        button {
            width: 100%;
            padding: 12px;
            background: #1e3c72;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #16335a;
        }

        /* Messages */
        .error, .success {
            font-size: 14px;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }
        .error { background: #ffdddd; color: red; }
        .success { background: #ddffdd; color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔄 Update Customer</h2>
        <?= $message ?>

        <form method="post">
            <select name="customer_id" required>
                <option value="">Select Customer</option>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <option value="<?= $row['CustomerID'] ?>"><?= $row['Name'] ?> (<?= $row['Email'] ?>)</option>
                <?php } ?>
            </select>

            <input type="text" name="name" placeholder="Customer Name" required>
            <input type="email" name="email" placeholder="Customer Email" required>
            <input type="text" name="address" placeholder="Address" required>

            <button type="submit" name="update">Update Customer</button>
        </form>
    </div>
</body>
</html>
