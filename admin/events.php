<?php
require_once '../session_check.php';
if (!isAdmin()) { header("Location: " . BASE_URL . "login.php"); exit(); }

$flash = getFlash();
$events = $conn->query("SELECT e.*, a.name as admin_name FROM events e LEFT JOIN admins a ON e.created_by = a.id ORDER BY e.event_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Events - EventHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <?php include 'topbar.php'; ?>
        <div class="content-wrapper">
            <div class="page-header">
                <div>
                    <h1>Manage Events</h1>
                    <p>View, edit, and manage all events</p>
                </div>
                <a href="add_event.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Event
                </a>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <i class="fas fa-info-circle"></i> <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($events->num_rows > 0): ?>
                                <?php while ($e = $events->fetch_assoc()): 
                                    $imagePath = '../uploads/events/' . $e['image'];
                                    $displayImage = (file_exists($imagePath) && $e['image'] !== 'default.jpg') ? $imagePath : 'https://via.placeholder.com/50x50?text=No+Img';
                                ?>
                                <tr>
                                    <td><img src="<?= $displayImage ?>" alt="<?= htmlspecialchars($e['title']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;"></td>
                                    <td>#<?= $e['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($e['title']) ?></strong></td>
                                    <td><span class="badge badge-info"><?= htmlspecialchars($e['category']) ?></span></td>
                                    <td><?= date('M d, Y', strtotime($e['event_date'])) ?></td>
                                    <td><?= htmlspecialchars($e['location']) ?></td>
                                    <td>$<?= number_format($e['price'], 2) ?></td>
                                    <td><span class="badge badge-<?= $e['status'] ?>"><?= ucfirst($e['status']) ?></span></td>
                                    <td>
                                        <a href="edit_event.php?id=<?= $e['id'] ?>" class="btn-icon btn-edit"><i class="fas fa-edit"></i></a>
                                        <a href="delete_event.php?id=<?= $e['id'] ?>" class="btn-icon btn-delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="9" class="text-center">No events found. <a href="add_event.php">Create one</a></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>