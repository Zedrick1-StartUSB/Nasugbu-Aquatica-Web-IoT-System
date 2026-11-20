<?php
require '../database_conn.php';
require_once '../global/auth.php';
$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');
$isSignup = isset($_POST['signup']);

if ($username === '' || $password === '') {
    http_response_code(400);
    exit('Username and password are required.');
}

if ($isSignup) {
    if (empty($_SESSION['username']) || strtolower((string) ($_SESSION['role'] ?? '')) !== 'admin') {
        http_response_code(403);
        exit('Only an administrator can create accounts.');
    }
    $role = strtolower(trim((string) ($_POST['role'] ?? '')));
    if (!in_array($role, ['admin', 'staff'], true)) {
        http_response_code(400);
        exit('Select a valid account role.');
    }
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

$statement = $conn->prepare('SELECT id, username, password, role FROM users WHERE username = ? ORDER BY id DESC');
$statement->bind_param('s', $username);
$statement->execute();
$users = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
$user = null;
$needsPasswordUpgrade = false;

foreach ($users as $candidateUser) {
    $storedPassword = (string) $candidateUser['password'];
    if (password_verify($password, $storedPassword)) {
        $user = $candidateUser;
        break;
    }
    if (hash_equals($storedPassword, $password)) {
        $user = $candidateUser;
        $needsPasswordUpgrade = true;
        break;
    }
}

if (!$user) {
    header('Location: html/login.php?error=invalid');
    exit;
}

// Accept existing plaintext rows once, then upgrade them to password hashes.
if ($needsPasswordUpgrade) {
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $upgrade = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
    $upgrade->bind_param('si', $newHash, $user['id']);
    $upgrade->execute();
}

session_regenerate_id(true);
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = strtolower((string) $user['role']);
if (!in_array($_SESSION['role'], ['admin', 'staff'], true)) {
    $_SESSION = [];
    http_response_code(403);
    exit('This account has no supported role. Contact an administrator.');
}
if ($_SESSION['role'] === 'admin') {
    header('Location: ../Admin/html/homepage.php');
    exit;
}
if ($_SESSION['role'] === 'staff') {
    header('Location: ../Staff/html/staff.php');
    exit;
}
