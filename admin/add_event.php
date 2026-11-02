<?php
require_once '../session_check.php';
if (!isAdmin()) { header("Location: " . BASE_URL . "login.php"); exit(); }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category = trim($_POST['category']);
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = trim($_POST['location']);
    $capacity = (int)$_POST['capacity'];
    $price = (float)$_POST['price'];
    $status = $_POST['status'];
    
    $imageName = 'default.jpg';
    $uploadDir = '../uploads/events/';
    
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024;
        
        $fileType = $_FILES['event_image']['type'];
        $fileSize = $_FILES['event_image']['size'];
        $fileName = $_FILES['event_image']['name'];
        
        if (!in_array($fileType, $allowedTypes)) {
            $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        } elseif ($fileSize > $maxSize) {
            $error = "File size must be less than 5MB.";
        } else {
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $imageName = uniqid('event_') . '.' . $fileExtension;
            $uploadPath = $uploadDir . $imageName;
            
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            if (!move_uploaded_file($_FILES['event_image']['tmp_name'], $uploadPath)) {
                $error = "Failed to upload image.";
                $imageName = 'default.jpg';
            }
        }
    }
    
    if (empty($error)) {
        if (empty($title) || empty($event_date) || empty($event_time) || empty($location)) {
            $error = "Please fill all required fields";
        } else {
            $stmt = $conn->prepare("INSERT INTO events (title, description, category, event_date, event_time, location, capacity, price, status, image, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssidssi", $title, $description, $category, $event_date, $event_time, $location, $capacity, $price, $status, $imageName, $_SESSION['admin_id']);            
            if ($stmt->execute()) {
                setFlash('success', 'Event created successfully!');
                header("Location: events.php");
                exit();
            } else {
                $error = "Failed to create event: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Event - EventHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <?php include 'topbar.php'; ?>
        <div class="content-wrapper">
            <div class="page-header">
                <h1>Add New Event</h1>
                <p>Create a new event for users to register</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="form-grid">
                        <div class="form-group full-width">
                            <label>Event Title *</label>
                            <input type="text" name="title" required placeholder="e.g., Tech Conference 2026">
                        </div>

                        <div class="form-group full-width">
                            <label>Description</label>
                            <textarea name="description" rows="4" placeholder="Describe the event..."></textarea>
                        </div>

                        <div class="form-group">
                            <label>Category</label>
                            <select name="category">
                                <option value="Conference">Conference</option>
                                <option value="Workshop">Workshop</option>
                                <option value="Seminar">Seminar</option>
                                <option value="Concert">Concert</option>
                                <option value="Sports">Sports</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="active">Active</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Event Date *</label>
                            <input type="date" name="event_date" required>
                        </div>

                        <div class="form-group">
                            <label>Event Time *</label>
                            <input type="time" name="event_time" required>
                        </div>

                        <div class="form-group full-width">
                            <label>Location *</label>
                            <input type="text" name="location" required placeholder="e.g., Convention Center, NYC">
                        </div>

                        <div class="form-group">
                            <label>Capacity</label>
                            <input type="number" name="capacity" value="100" min="1">
                        </div>

                        <div class="form-group">
                            <label>Price ($)</label>
                            <input type="number" name="price" value="0" min="0" step="0.01">
                        </div>

                        <div class="form-group full-width">
                            <label>Event Image</label>
                            <input type="file" name="event_image" id="event_image" accept="image/*" onchange="previewImage(this)">
                            <div class="file-upload-info">
                                <i class="fas fa-info-circle"></i>
                                <span>Allowed: JPG, JPEG, PNG, GIF (Max 5MB)</span>
                            </div>
                            <div id="image_preview" class="image-preview"></div>
                        </div>

                        <div class="form-actions full-width">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Event
                            </button>
                            <a href="events.php" class="btn btn-outline">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function previewImage(input) {
            const preview = document.getElementById('image_preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 300px; max-height: 200px; margin-top: 10px; border-radius: 8px; border: 2px solid #e2e8f0;">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>