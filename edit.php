<?php

if ($orig === '' || $username === '' || $firstName === '' || $lastName === '') {
    $_SESSION['flash_message'] = 'Required fields missing.';
    $_SESSION['flash_type'] = 'error';
    header('Location: edit.php?username=' . urlencode($orig));
    exit();
}

$updated = false;
foreach ($users as &$u) {
    if (($u['username'] ?? '') === $orig) {
        $u['username'] = $username;
        $u['firstName'] = $firstName;
        $u['lastName'] = $lastName;
        $u['email'] = $email;
        $u['birthday'] = $birthday;
        $u['age'] = $age;
        $u['address'] = $address;
        $u['phone'] = $phone;
        $u['gender'] = $gender;
        $updated = true;
        break;
    }
}
unset($u);

if ($updated) {
    if (file_put_contents($jsonFile, json_encode(array_values($users), JSON_PRETTY_PRINT), LOCK_EX) === false) {
        $_SESSION['flash_message'] = 'Failed to save changes.';
        $_SESSION['flash_type'] = 'error';
    } else {
        $_SESSION['flash_message'] = 'User updated.';
        $_SESSION['flash_type'] = 'success';
    }
} else {
    $_SESSION['flash_message'] = 'User not found.';
    $_SESSION['flash_type'] = 'error';
}

header('Location: dashboard.php');
exit();


?>