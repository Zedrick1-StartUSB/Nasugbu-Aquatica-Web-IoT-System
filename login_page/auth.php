<?php
require '../database_conn.php';

session_start();
$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$role = trim((string) ($_POST['role'] ?? ''));
$isSignup = $role !== '';

if ($username === '' || $password === '') {
    http_response_code(400);
    exit('Username and password are required.');
}

if ($isSignup) {
    $confirmPassword = (string) ($_POST['confirm-password'] ?? '');
    if ($confirmPassword !== $password) {
        http_response_code(400);
        exit('Passwords do not match.');
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $statement = $conn->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
    $statement->bind_param('sss', $username, $passwordHash, $role);
    if (!$statement->execute()) {
        http_response_code(400);
        exit('Could not create account. The username may already be in use.');
    }
    header('Location: html/signup.php?created=1');
    exit;
}

$statement = $conn->prepare('SELECT username, password, role FROM users WHERE username = ? LIMIT 1');
$statement->bind_param('s', $username);
$statement->execute();
$user = $statement->get_result()->fetch_assoc();

if (!$user) {
    http_response_code(401);
    exit('Invalid username or password.');
}

// Accept existing plaintext rows once, then upgrade them to password hashes.
$storedPassword = (string) $user['password'];
$passwordIsValid = password_verify($password, $storedPassword);
if (!$passwordIsValid && hash_equals($storedPassword, $password)) {
    $passwordIsValid = true;
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $upgrade = $conn->prepare('UPDATE users SET password = ? WHERE username = ?');
    $upgrade->bind_param('ss', $newHash, $username);
    $upgrade->execute();
}

if (!$passwordIsValid) {
    http_response_code(401);
    exit('Invalid username or password.');
}

session_regenerate_id(true);
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];
$destination = strtolower((string) $user['role']) === 'staff'
    ? '../Staff/html/staff.php'
    : '../Admin/html/homepage.php';
header('Location: ' . $destination);
exit;
