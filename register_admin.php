<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if the email is already in use
    $check_email = $conn->prepare("SELECT * FROM Admins WHERE Email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $result = $check_email->get_result();

    if ($result->num_rows > 0) {
        $error = "❌ Email already exists. Try another.";
    } else {
        $sql = "INSERT INTO Admins (Name, Email, Password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $password);

        if ($stmt->execute()) {
            $success = "✅ Admin registered successfully! <a href='admin_login.php'>Login here</a>";
        } else {
            $error = "❌ Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <style>
        /* General Styles */
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

        /* Registration Card */
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
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

        /* Form Fields */
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
        }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🆕 Register Admin</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <?php if (isset($success)) { echo "<p class='success'>$success</p>"; } ?>
        <form method="post">
            <input type="text" name="name" placeholder="Admin Name" required>
            <input type="email" name="email" placeholder="Admin Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
    </div>
</body>
</html>
