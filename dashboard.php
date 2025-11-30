<?php 
session_start();
if(!isset($_SESSION["username"])){
    header("Location: login.php");
    exit();
}
require "user.php";
$user = new User();
$allUsers = $user->getAllUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="style.css">
<title>Resident Accounts</title>
</head>
<body class="dashboard-page">

<div class="top-nav">
    <span>RESIDENT ACCOUNTS</span>
    <a href="logout.php" class="logout-btn">LOGOUT</a>
</div>

<div class="page-header">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></h1>
    <p>Create and manage resident accounts with the ability to assign them to specific buildings. <a href="#">Learn More</a></p>
</div>

<div class="actions">
    <button class="btn add">+ ADD</button>
    <button class="btn add">- DELETE</button>
    <button class="btn filter">FILTER</button>
    <button class="btn assign">ASSIGN</button>
    <button class="btn upload">UPLOAD</button>
    <button class="btn export">EDIT</button>
</div>

<table>
    <thead>
        <tr>
            <th></th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Address</th>
            <th>Birthday</th>
            <th>Age</th>
            <th>Phone Number</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($allUsers as $userData): ?>
        <tr>
            <td><input type="checkbox"></td>
            <td><?php echo htmlspecialchars($userData["firstName"] ?? ""); ?></td>
            <td><?php echo htmlspecialchars($userData["lastName"] ?? ""); ?></td>
            <td><?php echo htmlspecialchars($userData["address"] ?? ""); ?></td>
            <td><?php echo htmlspecialchars($userData["birthday"] ?? ""); ?></td>
            <td><?php echo htmlspecialchars($userData["age"] ?? ""); ?></td>
            <td><?php echo htmlspecialchars($userData["phone"] ?? ""); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>