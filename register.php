<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO Customers (Name, Email, Address, Password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $address, $password);

    if ($stmt->execute()) {
        $success = "✅ Registration successful! <a href='customer_login.php'>Login here</a>";
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
    <title>Register</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <style>
        /* General Page Styles */
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
        
        /* Card Container */
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            width: 400px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .container:hover {
            transform: scale(1.02);
        }

        /* Title */
        h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 20px;
        }

        /* Input Fields */
        input {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border 0.3s;
        }
        input:focus {
            border-color: #0072ff;
            outline: none;
        }

        /* Button */
        button {
            width: 100%;
            padding: 12px;
            background: #0072ff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            transition: background 0.3s;
        }
        button:hover {
            background: #005bbf;
        }

        /* Messages */
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .success {
            color: green;
            font-size: 14px;
            margin-bottom: 10px;
        }

        /* Login Link */
        .login-link {
            margin-top: 10px;
            font-size: 14px;
        }
        .login-link a {
            color: #0072ff;
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
        <h2>Create an Account</h2>
        <?php 
            if (isset($error)) echo "<p class='error'>$error</p>";
            if (isset($success)) echo "<p class='success'>$success</p>";
        ?>
        <form method="post">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="address" placeholder="Home Address" required>
            <input type="password" name="password" placeholder="Create Password" required>
            <button type="submit">Register</button>
        </form>
        <p class="login-link">Already have an account? <a href="customer_login.php">Login here</a></p>
    </div>
</body>
</html>
