<?php
session_start();

require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Registration successful! Please login.";
        header("Location: login.php");
        exit;
    } else {
        $error = "Registration failed. Username might be taken.";
    }
}
?>

<!DOCTYPE html>
<html>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #1a1a1a;
        color: #e0e0e0;
        max-width: 400px;
        margin: 50px auto;
        padding: 20px;
    }

    .form-group {
        margin-bottom: 15px;
    }

    input {
        width: 100%;
        padding: 8px;
        margin-top: 5px;
    }

    button {
        background: #4CAF50;
        color: #e0e0e0;
        padding: 10px 15px;
        border-radius: 3px;
        border: none;
        cursor: pointer;
        transition: linear 200ms;
    }

    button:hover {
        color: white;
        transform: scale(1.1);
    }

    .error {
        color: red;
        margin-bottom: 15px;
    }
</style>

<head>
    <title>Register</title>

</head>

<body>
    <h2>Register</h2>
    <?php if (isset($error))
        echo "<div class='error'>$error</div>"; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Register</button>
    </form>
    <div style="margin:10px">or</div>
    <form action="login.php">
        <button style="background:blue" type="submit">Go To Login</button>
    </form>
</body>

</html>