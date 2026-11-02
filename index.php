<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Premium Event Management</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <i class="fas fa-calendar-alt"></i>
                <span>EventHub</span>
            </div>
            <ul class="nav-menu">
                <li><a href="#home">Home</a></li>
                <li><a href="#features">Features</a></li>
                <li><a href="#events">Events</a></li>
                <li><a href="#contact">Contact</a></li>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="admin/dashboard.php" class="btn-nav">Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="user/dashboard.php" class="btn-nav">Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="btn-nav btn-logout">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-nav">Login</a></li>
                    <li><a href="register.php" class="btn-nav btn-primary">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Discover Amazing <span>Events</span></h1>
            <p>Manage, discover, and register for the best events happening around you.</p>
            <div class="hero-buttons">
                <a href="register.php" class="btn btn-primary btn-lg">Get Started <i class="fas fa-arrow-right"></i></a>
                <a href="#events" class="btn btn-outline btn-lg">Browse Events</a>
            </div>
        </div>
        <div class="hero-stats">
            <div class="stat">
                <h3>500+</h3>
                <p>Events Hosted</p>
            </div>
            <div class="stat">
                <h3>10K+</h3>
                <p>Happy Users</p>
            </div>
            <div class="stat">
                <h3>50+</h3>
                <p>Cities</p>
            </div>
        </div>
    </section>

    <section class="features" id="features">
        <div class="container">
            <h2 class="section-title">Why Choose EventHub?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                    <h3>Easy Registration</h3>
                    <p>Register for events in just a few clicks.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>
                    <h3>Secure Platform</h3>
                    <p>Your data is protected with industry-standard security.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-calendar-check"></i></div>
                    <h3>Smart Management</h3>
                    <p>Powerful admin tools to manage events and users.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon"><i class="fas fa-mobile-alt"></i></div>
                    <h3>Responsive Design</h3>
                    <p>Access from any device - desktop, tablet, or mobile.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="events-preview" id="events">
        <div class="container">
            <h2 class="section-title">Upcoming Events</h2>
            <?php
            $result = $conn->query("SELECT * FROM events WHERE status='active' ORDER BY event_date DESC LIMIT 3");
            if ($result && $result->num_rows > 0): ?>
            <div class="events-grid">
                <?php while ($event = $result->fetch_assoc()): 
                    $imagePath = 'uploads/events/' . $event['image'];
                    $displayImage = (file_exists($imagePath) && $event['image'] !== 'default.jpg') ? $imagePath : 'https://via.placeholder.com/400x200?text=Event+Image';
                ?>
                <div class="event-card">
                    <div class="event-image" style="background-image: url('<?= $displayImage ?>'); background-size: cover; background-position: center;">
                        <span class="event-category"><?= htmlspecialchars($event['category']) ?></span>
                    </div>
                    <div class="event-content">
                        <h3><?= htmlspecialchars($event['title']) ?></h3>
                        <p class="event-meta">
                            <i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($event['event_date'])) ?>
                            <i class="fas fa-clock"></i> <?= date('h:i A', strtotime($event['event_time'])) ?>
                        </p>
                        <p class="event-meta">
                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?>
                        </p>
                        <div class="event-footer">
                            <span class="event-price">$<?= number_format($event['price'], 2) ?></span>
                            <a href="login.php" class="btn btn-sm">Register Now</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
                <p class="no-events">No upcoming events yet. Check back soon!</p>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>EventHub</h3>
                    <p>Your premium event management platform.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#features">Features</a></li>
                        <li><a href="#events">Events</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact</h4>
                    <p><i class="fas fa-envelope"></i> info@eventhub.com</p>
                    <p><i class="fas fa-phone"></i> +1 234 567 890</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 EventHub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>