<?php
require_once '../session_check.php';
if (!isUser()) { header("Location: " . BASE_URL . "login.php"); exit(); }

$reg_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT r.*, e.title, e.description, e.category, e.event_date, e.event_time, e.location, e.price 
                        FROM registrations r 
                        JOIN events e ON r.event_id = e.id 
                        WHERE r.id = ? AND r.user_id = ?");
$stmt->bind_param("ii", $reg_id, $user_id);
$stmt->execute();
$t = $stmt->get_result()->fetch_assoc();

if (!$t) { header("Location: my_registrations.php"); exit(); }

$ticketCode = 'EMS-' . date('Y', strtotime($t['registered_at'])) . '-' . str_pad($t['id'], 6, '0', STR_PAD_LEFT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Ticket <?= $ticketCode ?> - EventHub</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* === Base Ticket === */
        .ticket-wrap { max-width: 680px; margin: 30px auto; position: relative; }
        
        .ticket {
            background: #FDFBF3;
            border: 2px solid #6D0808;
            border-radius: 16px;
            overflow: visible;
            box-shadow: 0 20px 25px -5px rgba(45,0,0,0.15);
            position: relative;
        }

        /* === 1. WATERMARK SIGN === */
        .ticket::before {
            content: "E-TICKET";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 110px;
            font-weight: 900;
            color: rgba(109, 8, 8, 0.04);
            letter-spacing: 12px;
            pointer-events: none;
            z-index: 0;
            white-space: nowrap;
        }

        /* === Ticket Header === */
        .ticket-top {
            background: linear-gradient(135deg, #6D0808, #2D0000);
            color: #EEEAD7;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        .ticket-top h2 { font-size: 22px; margin: 0; }
        .ticket-top span { font-size: 12px; opacity: 0.85; }

        .ticket-body { padding: 30px; position: relative; z-index: 1; }
        
        .ticket-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-bottom: 10px;
        }
        .ticket-field small {
            display: block;
            color: #5C635A;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        .ticket-field strong { color: #2D0000; font-size: 15px; }

        /* === 2. PERFORATION NOTCHES (classic ticket cut-outs) === */
        .ticket-divider {
            position: relative;
            margin: 25px 0;
            height: 2px;
            background: transparent;
        }
        .ticket-divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 30px;
            right: 30px;
            height: 2px;
            border-top: 2px dashed #DDD8C0;
            transform: translateY(-50%);
        }
        /* Left notch */
        .ticket-divider::after {
            content: "";
            position: absolute;
            left: -22px;
            top: 50%;
            width: 40px;
            height: 40px;
            background: #EEEAD7; /* matches page background */
            border: 2px solid #6D0808;
            border-radius: 50%;
            transform: translateY(-50%);
            z-index: 2;
        }
        .ticket-notch-right {
            position: absolute;
            right: -22px;
            top: 50%;
            width: 40px;
            height: 40px;
            background: #EEEAD7;
            border: 2px solid #6D0808;
            border-radius: 50%;
            transform: translateY(-50%);
            z-index: 2;
        }

        /* === Ticket Code & Barcode === */
        .ticket-code {
            text-align: center;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 3px;
            color: #6D0808;
            margin: 10px 0 15px;
        }
        
        .barcode-qr-row {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 25px;
            margin: 20px 0;
        }
        
        /* CSS Barcode (decorative) */
        .barcode {
            height: 60px;
            width: 240px;
            background: repeating-linear-gradient(90deg, 
                #2D0000 0 2px, transparent 2px 5px, 
                #2D0000 5px 7px, transparent 7px 10px, 
                #2D0000 10px 11px, transparent 11px 14px,
                #2D0000 14px 16px, transparent 16px 19px,
                #2D0000 19px 20px, transparent 20px 23px);
            border-radius: 2px;
        }
        
        /* 3. REAL SCANNABLE QR CODE */
        .qr-code {
            width: 110px;
            height: 110px;
            padding: 6px;
            background: white;
            border: 2px solid #2D0000;
            border-radius: 8px;
        }
        .qr-code img { width: 100%; height: 100%; display: block; }

        .ticket-note {
            text-align: center;
            color: #5C635A;
            font-size: 12px;
            margin: 15px 0 0;
        }

        /* === 4. VERIFIED STAMP === */
        .verified-stamp {
            position: absolute;
            top: 120px;
            right: 40px;
            width: 90px;
            height: 90px;
            border: 4px double #6D0808;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-15deg);
            color: #6D0808;
            font-weight: 900;
            font-size: 14px;
            letter-spacing: 1px;
            text-align: center;
            opacity: 0.85;
            background: rgba(253, 251, 243, 0.7);
            z-index: 2;
        }
        .verified-stamp i { display: block; font-size: 20px; margin-bottom: 2px; }

        /* === 5. SIGNATURE AREA === */
        .signature-area {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 30px;
            padding-top: 20px;
            border-top: 1px solid #DDD8C0;
        }
        .signature-box {
            flex: 1;
            text-align: center;
        }
        .signature-line {
            border-bottom: 2px solid #2D0000;
            height: 40px;
            margin-bottom: 6px;
            font-family: 'Brush Script MT', cursive;
            font-size: 22px;
            color: #6D0808;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            padding-bottom: 4px;
        }
        .signature-label {
            font-size: 11px;
            color: #5C635A;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* === Print Styles === */
        @media print {
            .sidebar, .topbar, .no-print, .page-header { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0; }
            body { background: white; }
            .content-wrapper { padding: 0; }
            .ticket-wrap { margin: 0; }
            .ticket { box-shadow: none; border: 2px solid #6D0808; }
            .ticket-divider::after,
            .ticket-notch-right { background: white; }
        }
    </style>
</head>
<body class="dashboard-body">
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <?php include 'topbar.php'; ?>
        <div class="content-wrapper">
            <div class="page-header no-print">
                <div>
                    <h1><i class="fas fa-ticket-alt"></i> Your E-Ticket</h1>
                    <p>Print or save this ticket as proof of registration</p>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Ticket</button>
                    <a href="my_registrations.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
                </div>
            </div>

            <div class="ticket-wrap">
                <div class="ticket">
                    <!-- Ticket Header -->
                    <div class="ticket-top">
                        <div>
                            <h2><i class="fas fa-calendar-alt"></i> EventHub</h2>
                            <span>Official Event Ticket</span>
                        </div>
                        <span class="badge badge-<?= $t['status'] ?>"><?= ucfirst($t['status']) ?></span>
                    </div>

                    <!-- Verified Stamp Sign -->
                    <div class="verified-stamp">
                        <div>
                            <i class="fas fa-check-circle"></i>
                            VERIFIED
                        </div>
                    </div>

                    <div class="ticket-body">
                        <!-- Event Details -->
                        <div class="ticket-grid">
                            <div class="ticket-field" style="grid-column: 1 / -1;">
                                <small>Event</small>
                                <strong style="font-size:20px;"><?= htmlspecialchars($t['title']) ?></strong>
                            </div>
                            <div class="ticket-field">
                                <small>Date</small>
                                <strong><?= date('l, M d, Y', strtotime($t['event_date'])) ?></strong>
                            </div>
                            <div class="ticket-field">
                                <small>Time</small>
                                <strong><?= date('h:i A', strtotime($t['event_time'])) ?></strong>
                            </div>
                            <div class="ticket-field" style="grid-column: 1 / -1;">
                                <small>Venue</small>
                                <strong><?= htmlspecialchars($t['location']) ?></strong>
                            </div>
                            <div class="ticket-field">
                                <small>Attendee</small>
                                <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
                            </div>
                            <div class="ticket-field">
                                <small>Category</small>
                                <strong><?= htmlspecialchars($t['category']) ?></strong>
                            </div>
                            <div class="ticket-field">
                                <small>Paid Amount</small>
                                <strong>$<?= number_format($t['price'], 2) ?></strong>
                            </div>
                            <div class="ticket-field">
                                <small>Booked On</small>
                                <strong><?= date('M d, Y h:i A', strtotime($t['registered_at'])) ?></strong>
                            </div>
                        </div>

                        <!-- Perforation Divider with Notches -->
                        <div class="ticket-divider">
                            <div class="ticket-notch-right"></div>
                        </div>

                        <!-- Ticket Code -->
                        <div class="ticket-code"><?= $ticketCode ?></div>

                        <!-- Barcode + Real QR Code -->
                        <div class="barcode-qr-row">
                            <div class="barcode" title="Decorative barcode"></div>
                            <div class="qr-code">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode('EventHub|' . $ticketCode . '|User:' . $_SESSION['user_id'] . '|Event:' . $t['event_id']) ?>&color=2D0000" alt="QR Code">
                            </div>
                        </div>

                        <p class="ticket-note">
                            <i class="fas fa-info-circle"></i>
                            Present this ticket at the entrance. Non-transferable — linked to <strong><?= htmlspecialchars($_SESSION['user_email']) ?></strong>.
                        </p>

                        <!-- Signature Area -->
                        <div class="signature-area">
                            <div class="signature-box">
                                <div class="signature-line">EventHub</div>
                                <div class="signature-label">Authorized by EventHub</div>
                            </div>
                            <div class="signature-box">
                                <div class="signature-line"><?= date('M d, Y', strtotime($t['registered_at'])) ?></div>
                                <div class="signature-label">Date Issued</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>