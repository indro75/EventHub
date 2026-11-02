<?php
require_once '../session_check.php';
if (!isAdmin()) { header("Location: " . BASE_URL . "login.php"); exit(); }

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT image FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $event = $result->fetch_assoc();
    if ($event['image'] !== 'default.jpg' && file_exists('../uploads/events/' . $event['image'])) {
        unlink('../uploads/events/' . $event['image']);
    }
}

$stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    setFlash('success', 'Event deleted successfully!');
} else {
    setFlash('error', 'Failed to delete event');
}

header("Location: events.php");
exit();
?>