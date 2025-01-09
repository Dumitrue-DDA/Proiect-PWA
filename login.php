<?php
session_start();

require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: main.php");
            exit;
        }
    }
    $error = "Invalid username or password";
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
    <title>Login</title>
</head>

<body>
    <h2>Login</h2>
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
        <button type="submit">Login</button>
    </form>
    <div style="margin:10px">or</div>
    <form action="register.php">
        <button style="background: blue">Go To Register</button>
    </form>
</body>

</html>