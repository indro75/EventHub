<?php
require_once '../session_check.php';
if (!isUser()) { header("Location: " . BASE_URL . "login.php"); exit(); }

$user_id = $_SESSION['user_id'];
$flash = getFlash();
$regs = $conn->query("SELECT r.*, e.title, e.event_date, e.event_time, e.location, e.price, e.image 
                      FROM registrations r 
                      JOIN events e ON r.event_id = e.id 
                      WHERE r.user_id = $user_id 
                      ORDER BY e.event_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Registrations - EventHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <?php include 'topbar.php'; ?>
        <div class="content-wrapper">
            <div class="page-header">
                <h1>My Registrations</h1>
                <p>Events you've registered for</p>
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
                                <th>Event</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Price</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($regs->num_rows > 0): ?>
                                <?php while ($r = $regs->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($r['title']) ?></strong></td>
                                    <td><?= date('M d, Y', strtotime($r['event_date'])) ?></td>
                                    <td><?= date('h:i A', strtotime($r['event_time'])) ?></td>
                                    <td><?= htmlspecialchars($r['location']) ?></td>
                                    <td>$<?= number_format($r['price'], 2) ?></td>
                                    <td><span class="badge badge-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">No registrations yet. <a href="events.php">Browse events</a></td></tr>
                            <?php endif; ?>
                        </tbody>
                        
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>