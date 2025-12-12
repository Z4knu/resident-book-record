<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
require "user.php";
$user = new User();
$message = null;


$jsonFile = __DIR__ . '/users.json';
function loadUsersJson($jsonFile) {
    if (!file_exists($jsonFile)) return [];
    $content = file_get_contents($jsonFile);
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}
function saveUsersJson($jsonFile, $users) {
    file_put_contents($jsonFile, json_encode(array_values($users), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
}

if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}


if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST['delete_selected'])) {
    $selected = $_POST['selected'] ?? [];
    if (!empty($selected)) {
        $users = loadUsersJson($jsonFile);
        $users = array_filter($users, function($u) use ($selected) {
            return !in_array($u['username'] ?? '', $selected, true);
        });
        saveUsersJson($jsonFile, $users);

        $_SESSION['flash_message'] = 'Selected user(s) deleted.';
        $_SESSION['flash_type'] = 'success';
    } else {
        $_SESSION['flash_message'] = 'No users selected to delete.';
        $_SESSION['flash_type'] = 'error';
    }

    header('Location: dashboard.php');
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST['delete_user'])) {
    $delUsername = $_POST['delete_user'] ?? '';
    if ($delUsername !== '') {
        $users = loadUsersJson($jsonFile);
        $originalCount = count($users);
        $users = array_filter($users, function($u) use ($delUsername) {
            return ($u['username'] ?? '') !== $delUsername;
        });
        saveUsersJson($jsonFile, $users);
        $_SESSION['flash_message'] = (count($users) < $originalCount) ? 'User deleted.' : 'User not found.';
        $_SESSION['flash_type'] = 'success';
    } else {
        $_SESSION['flash_message'] = 'No user specified to delete.';
        $_SESSION['flash_type'] = 'error';
    }
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST['edit_user'])) {
    $origUsername = $_POST['orig_username'] ?? '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? ''; 
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $birthday = $_POST['birthday'] ?? '';
    $age = $_POST['age'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = trim($_POST['gender'] ?? '');

    if ($origUsername === '') {
        $_SESSION['flash_message'] = 'Original username missing.';
        $_SESSION['flash_type'] = 'error';
        header('Location: dashboard.php'); exit();
    }

    $users = loadUsersJson($jsonFile);
    $index = null;
    foreach ($users as $i => $u) {
        if (($u['username'] ?? '') === $origUsername) { $index = $i; break; }
    }

    if ($index === null) {
        $_SESSION['flash_message'] = 'User not found.';
        $_SESSION['flash_type'] = 'error';
        header('Location: dashboard.php'); exit();
    }

    if ($username !== $origUsername) {
        foreach ($users as $u) {
            if (($u['username'] ?? '') === $username) {
                $_SESSION['flash_message'] = 'Username already exists.';
                $_SESSION['flash_type'] = 'error';
                header('Location: dashboard.php'); exit();
            }
        }
    }

    if ($password === '') {
        $password = $users[$index]['password'] ?? '';
    } else {

        $password = $password;
    }

    $users[$index] = [
        'username'  => $username,
        'password'  => $password,
        'firstName' => $firstName,
        'lastName'  => $lastName,
        'email'     => $email,
        'birthday'  => $birthday,
        'age'       => $age,
        'address'   => $address,
        'phone'     => $phone,
        'gender'     => $gender,
    ];

    saveUsersJson($jsonFile, $users);

    $_SESSION['flash_message'] = 'User updated.';
    $_SESSION['flash_type'] = 'success';
    header('Location: dashboard.php');
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST['add_user'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $birthday = $_POST['birthday'] ?? '';
    $age = $_POST['age'] ?? '';
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = trim($_POST['gender'] ?? '');

    if (empty($username) || empty($password) || empty($firstName) || empty($lastName) || empty($gender)) {
        $_SESSION['flash_message'] = 'Please fill required fields (username, password, first and last name, and gender).';
        $_SESSION['flash_type'] = 'error';
    } else {

        $users = loadUsersJson($jsonFile);
        foreach ($users as $u) {
            if (($u['username'] ?? '') === $username) {
                $_SESSION['flash_message'] = 'Username already exists.';
                $_SESSION['flash_type'] = 'error';
                header('Location: dashboard.php'); exit();
            }
        }
        $users[] = [
            'username'  => $username,
            'password'  => $password, 
            'firstName' => $firstName,
            'lastName'  => $lastName,
            'email'     => $email,
            'birthday'  => $birthday,
            'age'       => $age,
            'address'   => $address,
            'phone'     => $phone,
            'gender'     => $gender,
        ];
        saveUsersJson($jsonFile, $users);

        if (method_exists($user, 'register')) {
            $user->register($username, $password, $firstName, $lastName, $email, $birthday, $age, $address, $phone, $gender);
        }

        $_SESSION['flash_message'] = 'Registration Successful';
        $_SESSION['flash_type'] = 'success';
    }

    header('Location: dashboard.php');
    exit();
}


$allUsers = loadUsersJson($jsonFile);
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
        <button form="usersForm" type="submit" name="delete_selected" class="btn delete">- DELETE</button>
        <button id="openAdd" class="btn add">+ ADD</button>
    </div>

    <form id="usersForm" method="POST" action="dashboard.php">
        <?php if (isset($message)): ?>
            <div class="message <?php echo htmlspecialchars($message_type ?? 'error'); ?>"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
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
                    <th>Gender</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($allUsers as $userData): ?>
                    <tr data-username="<?php echo htmlspecialchars($userData['username'] ?? ''); ?>"
                        data-firstname="<?php echo htmlspecialchars($userData['firstName'] ?? ''); ?>"
                        data-lastname="<?php echo htmlspecialchars($userData['lastName'] ?? ''); ?>"
                        data-address="<?php echo htmlspecialchars($userData['address'] ?? ''); ?>"
                        data-birthday="<?php echo htmlspecialchars($userData['birthday'] ?? ''); ?>"
                        data-age="<?php echo htmlspecialchars($userData['age'] ?? ''); ?>"
                        data-phone="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>"
                        data-email="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>"
                        data-gender="<?php echo htmlspecialchars($userData['gender'] ?? ''); ?>"
                    >
                        <td><input type="checkbox" name="selected[]" value="<?php echo htmlspecialchars($userData["username"] ?? ""); ?>"></td>
                        <td><?php echo htmlspecialchars($userData["firstName"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($userData["lastName"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($userData["address"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($userData["birthday"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($userData["age"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($userData["phone"] ?? ""); ?></td>
                        <td><?php echo htmlspecialchars($userData["gender"] ?? ""); ?></td>
                        <td style="display:flex;gap:8px;">
                            <button type="button" class="btn edit-row">Edit</button>

                            
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </form>


    <div id="addModal" class="modal" aria-hidden="true">
        <div class="modal-content">
            <h3>Add Resident</h3>
            <form method="POST" action="dashboard.php" class="add-form">
                <div class="modal-row">
                    <div class="col">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="col">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                </div>
                <div class="modal-row">
                    <div class="col">
                        <label>First Name</label>
                        <input type="text" name="firstName" required>
                    </div>
                    <div class="col">
                        <label>Last Name</label>
                        <input type="text" name="lastName" required>
                    </div>
                </div>
                <label>Email</label>
                <input type="email" name="email">
                <div class="modal-row">
                    <div class="col">
                        <label>Birthday</label>
                        <input type="date" name="birthday">
                    </div>
                    <div class="col">
                        <label>Age</label>
                        <input type="number" name="age">
                    </div>
                </div>
                <label>Address</label>
                <input type="text" name="address">
                <label>Phone Number</label>
                <input type="text" name="phone" pattern="^09\d{9}$" maxlength="11">
                <label>Gender</label>
                <input type="text" name="gender" required>

                <div style="display:flex;gap:10px;margin-top:12px;justify-content:flex-end;">
                    <button type="button" id="cancelAdd" class="btn cancel">Cancel</button>
                    <button type="submit" name="add_user" class="btn add">Add</button>
                </div>
            </form>
        </div>
    </div>


    <div id="editModal" class="modal" aria-hidden="true">
        <div class="modal-content">
            <h3>Edit Resident</h3>
            <form method="POST" action="dashboard.php" class="edit-form" id="editForm">
                <input type="hidden" name="orig_username" id="orig_username">
                <div class="modal-row">
                    <div class="col">
                        <label>Username</label>
                        <input type="text" name="username" id="edit_username" required>
                    </div>
                    <div class="col">
                        <label>Password (leave blank to keep)</label>
                        <input type="password" name="password" id="edit_password">
                    </div>
                </div>
                <div class="modal-row">
                    <div class="col">
                        <label>First Name</label>
                        <input type="text" name="firstName" id="edit_firstName" required>
                    </div>
                    <div class="col">
                        <label>Last Name</label>
                        <input type="text" name="lastName" id="edit_lastName" required>
                    </div>
                </div>
                <label>Email</label>
                <input type="email" name="email" id="edit_email">
                <div class="modal-row">
                    <div class="col">
                        <label>Birthday</label>
                        <input type="date" name="birthday" id="edit_birthday">
                    </div>
                    <div class="col">
                        <label>Age</label>
                        <input type="number" name="age" id="edit_age">
                    </div>
                </div>
                <label>Address</label>
                <input type="text" name="address" id="edit_address">
                <label>Phone Number</label>
                <input type="text" name="phone" id="edit_phone" pattern="^09\d{9}$" maxlength="11">
                <label>Gender</label>
                <input type="text" name="gender" id="edit_gender" required>

                <div style="display:flex;gap:10px;margin-top:12px;justify-content:flex-end;">
                    <button type="button" id="cancelEdit" class="btn cancel">Cancel</button>
                    <button type="submit" name="edit_user" class="btn add">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('openAdd').addEventListener('click', function() {
            document.getElementById('addModal').classList.add('show');
            document.getElementById('addModal').setAttribute('aria-hidden', 'false');
        });
        document.getElementById('cancelAdd').addEventListener('click', function() {
            document.getElementById('addModal').classList.remove('show');
            document.getElementById('addModal').setAttribute('aria-hidden', 'true');
        });
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('show');
                this.setAttribute('aria-hidden', 'true');
            }
        });

        const editModal = document.getElementById('editModal');
        const cancelEdit = document.getElementById('cancelEdit');
        cancelEdit.addEventListener('click', closeEditModal);
        editModal.addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });

        function closeEditModal() {
            editModal.classList.remove('show');
            editModal.setAttribute('aria-hidden', 'true');
            document.getElementById('editForm').reset();
        }

        document.querySelectorAll('.edit-row').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const tr = btn.closest('tr');
                if (!tr) return;
                document.getElementById('orig_username').value = tr.getAttribute('data-username') || '';
                document.getElementById('edit_username').value = tr.getAttribute('data-username') || '';
                document.getElementById('edit_firstName').value = tr.getAttribute('data-firstname') || '';
                document.getElementById('edit_lastName').value = tr.getAttribute('data-lastname') || '';
                document.getElementById('edit_address').value = tr.getAttribute('data-address') || '';
                document.getElementById('edit_birthday').value = tr.getAttribute('data-birthday') || '';
                document.getElementById('edit_age').value = tr.getAttribute('data-age') || '';
                document.getElementById('edit_phone').value = tr.getAttribute('data-phone') || '';
                document.getElementById('edit_email').value = tr.getAttribute('data-email') || '';
                document.getElementById('edit_gender').value = tr.getAttribute('data-gender') || '';

                editModal.classList.add('show');
                editModal.setAttribute('aria-hidden', 'false');
            });
        });
    </script>

</body>

</html>