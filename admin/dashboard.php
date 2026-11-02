<?php
require_once '../session_check.php';

if (!isAdmin()) {
    header("Location: " . BASE_URL . "login.php");
    exit();
}

$totalEvents = $conn->query("SELECT COUNT(*) as c FROM events")->fetch_assoc()['c'];
$totalUsers = $conn->query("SELECT COUNT(*) as c FROM users")->fetch_assoc()['c'];
$totalRegs = $conn->query("SELECT COUNT(*) as c FROM registrations")->fetch_assoc()['c'];
$activeEvents = $conn->query("SELECT COUNT(*) as c FROM events WHERE status='active'")->fetch_assoc()['c'];

$recentEvents = $conn->query("SELECT * FROM events ORDER BY created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - EventHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <?php include 'topbar.php'; ?>

        <div class="content-wrapper">
            <div class="page-header">
                <h1>Dashboard Overview</h1>
                <p>Welcome back, <?= htmlspecialchars($_SESSION['admin_name']) ?>!</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card stat-blue">
                    <div class="stat-icon"><i class="fas fa-calendar-alt"></i></div>
                    <div class="stat-info">
                        <h3><?= $totalEvents ?></h3>
                        <p>Total Events</p>
                    </div>
                </div>
                <div class="stat-card stat-green">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3><?= $totalUsers ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
                <div class="stat-card stat-purple">
                    <div class="stat-icon"><i class="fas fa-ticket-alt"></i></div>
                    <div class="stat-info">
                        <h3><?= $totalRegs ?></h3>
                        <p>Registrations</p>
                    </div>
                </div>
                <div class="stat-card stat-orange">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <h3><?= $activeEvents ?></h3>
                        <p>Active Events</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Recent Events</h2>
                    <a href="events.php" class="btn btn-sm">View All</a>
                </div>
                <div class="card-body">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentEvents->num_rows > 0): ?>
                                <?php while ($event = $recentEvents->fetch_assoc()): 
                                    $imagePath = '../uploads/events/' . $event['image'];
                                    $displayImage = (file_exists($imagePath) && $event['image'] !== 'default.jpg') ? $imagePath : 'https://via.placeholder.com/50x50?text=No+Img';
                                ?>
                                <tr>
                                    <td><img src="<?= $displayImage ?>" alt="<?= htmlspecialchars($event['title']) ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;"></td>
                                    <td><strong><?= htmlspecialchars($event['title']) ?></strong></td>
                                    <td><?= date('M d, Y', strtotime($event['event_date'])) ?></td>
                                    <td><?= htmlspecialchars($event['location']) ?></td>
                                    <td><span class="badge badge-<?= $event['status'] ?>"><?= ucfirst($event['status']) ?></span></td>
                                    <td>
                                        <a href="edit_event.php?id=<?= $event['id'] ?>" class="btn-icon btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="delete_event.php?id=<?= $event['id'] ?>" class="btn-icon btn-delete" title="Delete" onclick="return confirm('Delete this event?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">No events yet</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>