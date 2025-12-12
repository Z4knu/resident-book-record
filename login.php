<?php
session_start();
require "user.php";
$user = new User();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"] ?? '';
    $password = $_POST["password"] ?? '';

    $message = $user->login($username, $password);
    if ($message === "Login Successful") {
        $_SESSION["username"] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $message_type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
</head>
<body class="auth">

<div class="container">
    <form method="POST" action="login.php" class="form-box">

        <h2 class="title">Log in</h2>

        <?php if (isset($message) && $message !== "Login Successful"): ?>
            <div class="message <?php echo htmlspecialchars($message_type ?? 'error'); ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <label>Username:</label>
        <input type="text" placeholder="Username" name="username" required>

        <label>Password:</label>
        <input type="password" placeholder="Password" name="password" required>

        <button type="submit">Login</button>

    </form>
    
    <p class="signup-link">Don't have an account? <a href="register.php">Sign up</a></p>
</div>

</body>
</html>