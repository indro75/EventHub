<?php
require_once '../session_check.php';
if (!isUser()) { header("Location: " . BASE_URL . "login.php"); exit(); }

$event_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$check = $conn->prepare("SELECT id FROM registrations WHERE user_id = ? AND event_id = ?");
$check->bind_param("ii", $user_id, $event_id);
$check->execute();

if ($check->get_result()->num_rows > 0) {
    setFlash('warning', 'You are already registered for this event!');
} else {
    $stmt = $conn->prepare("INSERT INTO registrations (user_id, event_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $event_id);
    if ($stmt->execute()) {
        setFlash('success', 'Successfully registered for the event!');
    } else {
        setFlash('error', 'Registration failed');
    }
}

header("Location: my_registrations.php");
exit();
?>