<?php 
require "user.php";
$user=new User();

if($_SERVER["REQUEST_METHOD"]== "POST"){
    $username=$_POST["username"] ?? '';
    $password=$_POST["password"] ?? '';
    $firstName=$_POST["firstName"] ?? '';
    $lastName=$_POST["lastName"] ?? '';
    $email=$_POST["email"] ?? '';
    $birthday=$_POST["birthday"] ?? '';
    $age=$_POST["age"] ?? '';
    $address=$_POST["address"] ?? '';
    $phone=$_POST["phone"] ?? '';
    $gender=$_POST["gender"] ?? '';

    if (empty($username) || empty($password) || empty($firstName) || empty($lastName) || empty($email) || empty($birthday) || empty($age) || empty($address) || empty($phone) || empty($gender)) {
        $message = 'Please fill in all required fields.';
        $message_type = 'error';
    } else {
        $message = $user->register($username,$password,$firstName,$lastName,$email,$birthday,$age,$address,$phone,$gender);
        $message_type = ($message === 'Registration Successful') ? 'success' : 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Registration</title>
</head>
<body class="auth">

    <div class="container">
        <form method="POST" action="register.php" class="form-box">
            <h2 class="title">Sign Up</h2>

            <?php if (isset($message)): ?>
                <div class="message <?php echo htmlspecialchars($message_type ?? 'error'); ?>"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <label>Username:</label>
            <input type="text" placeholder="Username" name="username" required>

            <label>Password:</label>
            <input type="password" placeholder="Password" name="password" required>

            <label>First Name:</label>
            <input type="text" placeholder="First Name" name="firstName" required>

            <label>Last Name:</label>
            <input type="text" placeholder="Last Name" name="lastName" required>

            <label>Email:</label>
            <input type="email" placeholder="Email" name="email" required>

            <label>Birthday:</label>
            <input type="date" placeholder="Birthday" name="birthday" required>

            <label>Age:</label>
            <input type="number" placeholder="Age" name="age" required>

            <label>Address:</label>
            <input type="text" placeholder="Address" name="address" required>

            <label>Phone Number:</label>
            <input type="text" placeholder="Phone Number (e.g. 09123456789)" name="phone" 
            pattern="^09\d{9}$" maxlength="11" required>

            <label>Gender</label>
            <input type="text" placeholder="Gender" name="gender" required>

            

            <button type="submit">Sign Up</button>
        </form>
        <p class="signup-link">Already have an account? <a href="login.php">Login</a></p>
    </div>

</body>
</html>