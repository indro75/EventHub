<?php
require_once '../session_check.php';
if (!isUser()) { header("Location: " . BASE_URL . "login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$myRegs = $conn->query("SELECT COUNT(*) as c FROM registrations WHERE user_id = $user_id")->fetch_assoc()['c'];
$upcoming = $conn->query("SELECT COUNT(*) as c FROM events WHERE status='active' AND event_date >= CURDATE()")->fetch_assoc()['c'];
$allEvents = $conn->query("SELECT * FROM events WHERE status='active' ORDER BY event_date ASC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Dashboard - EventHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <?php include 'topbar.php'; ?>
        <div class="content-wrapper">
            <div class="page-header">
                <h1>Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>! 👋</h1>
                <p>Discover and register for amazing events</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card stat-blue">
                    <div class="stat-icon"><i class="fas fa-ticket-alt"></i></div>
                    <div class="stat-info">
                        <h3><?= $myRegs ?></h3>
                        <p>My Registrations</p>
                    </div>
                </div>
                <div class="stat-card stat-green">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-info">
                        <h3><?= $upcoming ?></h3>
                        <p>Upcoming Events</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Upcoming Events</h2>
                    <a href="events.php" class="btn btn-sm">View All</a>
                </div>
                <div class="card-body">
                    <div class="events-grid">
                        <?php while ($e = $allEvents->fetch_assoc()): 
                            $imagePath = '../uploads/events/' . $e['image'];
                            $displayImage = (file_exists($imagePath) && $e['image'] !== 'default.jpg') ? $imagePath : 'https://via.placeholder.com/400x200?text=Event+Image';
                        ?>
                        <div class="event-card">
                            <div class="event-image" style="background-image: url('<?= $displayImage ?>'); background-size: cover; background-position: center;">
                                <span class="event-category"><?= htmlspecialchars($e['category']) ?></span>
                            </div>
                            <div class="event-content">
                                <h3><?= htmlspecialchars($e['title']) ?></h3>
                                <p class="event-meta">
                                    <i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($e['event_date'])) ?>
                                </p>
                                <p class="event-meta">
                                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($e['location']) ?>
                                </p>
                                <div class="event-footer">
                                    <span class="event-price">$<?= number_format($e['price'], 2) ?></span>
                                    <a href="register_event.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-primary">Register</a>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>