<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM Admins WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['Password'])) {
            $_SESSION['admin_id'] = $admin['AdminID'];
            $_SESSION['admin_name'] = $admin['Name'];
            header("Location: admin_dashboard.php");
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
    <title>Admin Login</title>
    <link rel="icon" type="image/png" href="favicon.png"> <!-- Favicon Added -->
    <style>
        /* Background and General Styling */
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
            padding: 35px;
            width: 400px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
            border-radius: 12px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .container:hover {
            transform: scale(1.03);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.4);
        }

        /* Title */
        h2 {
            color: #333;
            font-weight: 700;
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
            transition: border 0.3s ease;
        }
        input:focus {
            border-color: #1e3c72;
            outline: none;
        }

        /* Login Button */
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

        /* Error Message */
        .error {
            color: red;
            font-size: 14px;
            margin-bottom: 15px;
        }

        /* Register Section */
        .register {
            font-size: 14px;
            margin-top: 15px;
        }
        .register a {
            color: #1e3c72;
            text-decoration: none;
            font-weight: bold;
        }
        .register a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔑 Admin Login</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="post">
            <input type="email" name="email" placeholder="Admin Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p class="register">Don't have an account? <a href="register_admin.php">Register Here</a></p>
    </div>
</body>
</html>
