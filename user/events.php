<?php
require_once '../session_check.php';
if (!isUser()) { header("Location: " . BASE_URL . "login.php"); exit(); }

$flash = getFlash();
$today = date('Y-m-d');
$events = $conn->query("SELECT * FROM events WHERE status='active' ORDER BY event_date ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Events - EventHub</title>
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
                    <h1>Browse Events</h1>
                    <p>Live seat availability • Double-booking protection • E-tickets</p>
                </div>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>">
                    <i class="fas fa-info-circle"></i> <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <div class="events-grid">
                <?php if ($events->num_rows > 0): ?>
                    <?php while ($e = $events->fetch_assoc()): 
                        $imagePath = '../uploads/events/' . $e['image'];
                        $displayImage = (file_exists($imagePath) && $e['image'] !== 'default.jpg') ? $imagePath : 'https://via.placeholder.com/400x200?text=Event+Image';

                        // Live seat calculation
                        $cnt = $conn->query("SELECT COUNT(*) c FROM registrations WHERE event_id = {$e['id']} AND status = 'confirmed'")->fetch_assoc()['c'];
                        $left = max(0, $e['capacity'] - $cnt);
                        $pct = $e['capacity'] > 0 ? min(100, round($cnt / $e['capacity'] * 100)) : 100;
                        $isFull = ($left <= 0);
                        $isPast = ($e['event_date'] < $today);
                    ?>
                    <div class="event-card">
                        <div class="event-image" style="background-image: url('<?= $displayImage ?>'); background-size: cover; background-position: center;">
                            <span class="event-category"><?= htmlspecialchars($e['category']) ?></span>
                        </div>
                        <div class="event-content">
                            <h3><?= htmlspecialchars($e['title']) ?></h3>
                            <p class="event-description"><?= htmlspecialchars(substr($e['description'], 0, 100)) ?>...</p>
                            <p class="event-meta">
                                <i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($e['event_date'])) ?>
                                <i class="fas fa-clock"></i> <?= date('h:i A', strtotime($e['event_time'])) ?>
                            </p>
                            <p class="event-meta">
                                <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($e['location']) ?>
                            </p>

                            <!--  Live Seat Availability -->
                            <div style="margin: 10px 0;">
                                <div style="display:flex; justify-content:space-between; font-size:12px; color:#5C635A; margin-bottom:4px;">
                                    <span><i class="fas fa-chair"></i> <?= $cnt ?>/<?= $e['capacity'] ?> booked</span>
                                    <?php if ($isFull): ?>
                                        <span class="badge badge-cancelled">FULL</span>
                                    <?php elseif ($isPast): ?>
                                        <span class="badge badge-completed">ENDED</span>
                                    <?php elseif ($left <= max(5, $e['capacity'] * 0.2)): ?>
                                        <span class="badge badge-info">Only <?= $left ?> left!</span>
                                    <?php else: ?>
                                        <span class="badge badge-active"><?= $left ?> seats left</span>
                                    <?php endif; ?>
                                </div>
                                <div style="background:#E2E6DB; border-radius:20px; height:8px; overflow:hidden;">
                                    <div style="background:<?= $isFull ? '#6D0808' : '#757D6F' ?>; height:8px; border-radius:20px; width:<?= $pct ?>%;"></div>
                                </div>
                            </div>

                            <div class="event-footer">
                                <span class="event-price">$<?= number_format($e['price'], 2) ?></span>
                                <?php if ($isFull || $isPast): ?>
                                    <span class="btn btn-sm" style="opacity:0.5; cursor:not-allowed; background:#757D6F; color:#EEEAD7;">
                                        <?= $isPast ? 'Ended' : 'Full' ?>
                                    </span>
                                <?php else: ?>
                                    <a href="register_event.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-primary">Register</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-events">No events available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>