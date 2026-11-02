<?php
require_once '../config.php';

// --- Session timeout (30 minutes) ---
$timeout = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "login.php?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time();

// --- Prevent session fixation ---
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 300) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// --- AUTO-REPAIR: reload missing profile data from database ---
if (isset($_SESSION['user_id']) && empty($_SESSION['user_name'])) {
    $uid = (int)$_SESSION['user_id'];
    $res = $conn->query("SELECT name, email FROM users WHERE id = $uid");
    if ($res && $res->num_rows > 0) {
        $u = $res->fetch_assoc();
        $_SESSION['user_name']  = $u['name'];
        $_SESSION['user_email'] = $u['email'];
    } else {
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "login.php");
        exit();
    }
}

if (isset($_SESSION['admin_id']) && empty($_SESSION['admin_name'])) {
    $aid = (int)$_SESSION['admin_id'];
    $res = $conn->query("SELECT name, email FROM admins WHERE id = $aid");
    if ($res && $res->num_rows > 0) {
        $a = $res->fetch_assoc();
        $_SESSION['admin_name']  = $a['name'];
        $_SESSION['admin_email'] = $a['email'];
    } else {
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "login.php");
        exit();
    }
}
?>