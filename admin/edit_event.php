<?php
require_once '../session_check.php';
if (!isAdmin()) { header("Location: " . BASE_URL . "login.php"); exit(); }

$id = (int)$_GET['id'];
$error = '';

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    setFlash('error', 'Event not found');
    header("Location: events.php");
    exit();
}

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
    
    $imageName = $event['image'];
    
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024;
        
        $fileType = $_FILES['event_image']['type'];
        $fileSize = $_FILES['event_image']['size'];
        $fileName = $_FILES['event_image']['name'];
        
        if (in_array($fileType, $allowedTypes) && $fileSize <= $maxSize) {
            if ($event['image'] !== 'default.jpg' && file_exists('../uploads/events/' . $event['image'])) {
                unlink('../uploads/events/' . $event['image']);
            }
            
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $imageName = uniqid('event_') . '.' . $fileExtension;
            $uploadPath = '../uploads/events/' . $imageName;
            
            if (!move_uploaded_file($_FILES['event_image']['tmp_name'], $uploadPath)) {
                $imageName = $event['image'];
            }
        } else {
            $error = "Invalid image file. Only JPG, PNG, GIF allowed (Max 5MB).";
        }
    }
    
    if (empty($error)) {
        $stmt = $conn->prepare("UPDATE events SET title=?, description=?, category=?, event_date=?, event_time=?, location=?, capacity=?, price=?, status=?, image=? WHERE id=?");
$stmt->bind_param("ssssssidssi", $title, $description, $category, $event_date, $event_time, $location, $capacity, $price, $status, $imageName, $id);        
        if ($stmt->execute()) {
            setFlash('success', 'Event updated successfully!');
            header("Location: events.php");
            exit();
        } else {
            $error = "Update failed: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Event - EventHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <?php include 'topbar.php'; ?>
        <div class="content-wrapper">
            <div class="page-header">
                <h1>Edit Event</h1>
                <p>Update event details</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data" class="form-grid">
                        <div class="form-group full-width">
                            <label>Event Title *</label>
                            <input type="text" name="title" required value="<?= htmlspecialchars($event['title']) ?>">
                        </div>

                        <div class="form-group full-width">
                            <label>Description</label>
                            <textarea name="description" rows="4"><?= htmlspecialchars($event['description']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Category</label>
                            <select name="category">
                                <?php foreach (['Conference','Workshop','Seminar','Concert','Sports','Other'] as $cat): ?>
                                    <option value="<?= $cat ?>" <?= $event['category']==$cat?'selected':'' ?>><?= $cat ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status">
                                <option value="active" <?= $event['status']=='active'?'selected':'' ?>>Active</option>
                                <option value="cancelled" <?= $event['status']=='cancelled'?'selected':'' ?>>Cancelled</option>
                                <option value="completed" <?= $event['status']=='completed'?'selected':'' ?>>Completed</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Event Date *</label>
                            <input type="date" name="event_date" required value="<?= $event['event_date'] ?>">
                        </div>

                        <div class="form-group">
                            <label>Event Time *</label>
                            <input type="time" name="event_time" required value="<?= $event['event_time'] ?>">
                        </div>

                        <div class="form-group full-width">
                            <label>Location *</label>
                            <input type="text" name="location" required value="<?= htmlspecialchars($event['location']) ?>">
                        </div>

                        <div class="form-group">
                            <label>Capacity</label>
                            <input type="number" name="capacity" value="<?= $event['capacity'] ?>" min="1">
                        </div>

                        <div class="form-group">
                            <label>Price ($)</label>
                            <input type="number" name="price" value="<?= $event['price'] ?>" min="0" step="0.01">
                        </div>

                        <div class="form-group full-width">
                            <label>Current Image</label>
                            <?php 
                            $imagePath = '../uploads/events/' . $event['image'];
                            $displayImage = (file_exists($imagePath) && $event['image'] !== 'default.jpg') ? $imagePath : 'https://via.placeholder.com/300x200?text=No+Image';
                            ?>
                            <img src="<?= $displayImage ?>" alt="Current Image" style="max-width: 300px; border-radius: 8px; margin-bottom: 10px;">
                            
                            <label style="margin-top: 10px;">Upload New Image (optional)</label>
                            <input type="file" name="event_image" id="event_image" accept="image/*" onchange="previewImage(this)">
                            <div class="file-upload-info">
                                <i class="fas fa-info-circle"></i>
                                <span>Leave empty to keep current image. Allowed: JPG, PNG, GIF (Max 5MB)</span>
                            </div>
                            <div id="image_preview" class="image-preview"></div>
                        </div>

                        <div class="form-actions full-width">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Event
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
                    preview.innerHTML = `<img src="${e.target.result}" alt="New Preview" style="max-width: 300px; max-height: 200px; margin-top: 10px; border-radius: 8px; border: 2px solid #e2e8f0;">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>