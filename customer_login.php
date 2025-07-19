<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT CustomerID, Name, Password FROM Customers WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $customer = $result->fetch_assoc();
        
        if (password_verify($password, $customer['Password'])) {
            $_SESSION['customer_id'] = $customer['CustomerID'];
            $_SESSION['customer_name'] = $customer['Name'];
            header("Location: customer_dashboard.php");
            exit();
        } else {
            $error = "❌ Invalid email or password.";
        }
    } else {
        $error = "❌ Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('background.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            width: 400px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            text-align: center;
        }

        h2 {
            color: #1e3c72;
            margin-bottom: 15px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        
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
        }
        button:hover {
            background: #16335a;
        }

        .error {
            background: #ffdddd;
            color: red;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .login-link {
            font-size: 14px;
            margin-top: 15px;
        }
        .login-link a {
            color: #1e3c72;
            text-decoration: none;
            font-weight: bold;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔑 Customer Login</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="post">
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="password" name="password" placeholder="Enter your password" required>
            <button type="submit">Login</button>
        </form>
        <p class="login-link">Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
