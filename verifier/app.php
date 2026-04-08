<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('verifier');

$verification_result = null;
$ticket_data = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_number = trim($_POST['ticket_number'] ?? '');

    // Some scanners might pass "Ticket: TKT-123 | BookingID: 456" 
    // Let's parse just the ticket number if it contains the full QR string
    if (strpos($ticket_number, 'Ticket: ') !== false) {
        preg_match('/Ticket: (TKT-[A-Z0-9]+-\d+)/', $ticket_number, $matches);
        if (isset($matches[1])) {
            $ticket_number = $matches[1];
        }
    }

    if (empty($ticket_number)) {
        $error = "Please provide a ticket number.";
    }
    else {
        $stmt = $pdo->prepare("
            SELECT b.*, u.name as passenger_name, tr.departure_time, tr.travel_date,
                   r.origin, r.destination, bus.bus_name, bus.bus_number
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN trips tr ON b.trip_id = tr.id
            JOIN routes r ON tr.route_id = r.id
            JOIN buses bus ON tr.bus_id = bus.id
            WHERE b.ticket_number = ?
        ");
        $stmt->execute([$ticket_number]);
        $ticket_data = $stmt->fetch();

        if (!$ticket_data) {
            $verification_result = 'INVALID';
            $error = "Ticket not found in the system.";
        }
        else if ($ticket_data['status'] !== 'paid') {
            $verification_result = 'INVALID';
            $error = "Ticket exists but payment is pending or cancelled.";
        }
        else if ($ticket_data['is_verified']) {
            $verification_result = 'USED';
        }
        else {
            // Valid and not used. Mark as verified.
            $updateStmt = $pdo->prepare("UPDATE bookings SET is_verified = 1 WHERE id = ?");
            $updateStmt->execute([$ticket_data['id']]);
            $verification_result = 'VALID';
            $ticket_data['is_verified'] = 1; // Update local state for display
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Verification - Bus Ticket System</title>
    <!-- Include html5-qrcode library -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <script>
        (function(){var t=localStorage.getItem("mobus_theme")||"dark";
        document.documentElement.setAttribute("data-theme",t);})();
    </script>
</head>

<body>
    <div class="header">
        <h2>Conductor App</h2>
        <div>
            Hi,
            <?= htmlspecialchars($_SESSION['name'])?> |
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL ?>/logout.php" class="nav-logout">Logout</a>
        </div>
    </div>

    <div class="content">
        <?php if ($verification_result): ?>
        <div class="result-box 
                <?= $verification_result === 'VALID' ? 'result-valid' :
        ($verification_result === 'USED' ? 'result-used' : 'result-invalid')?>">
            <?= $verification_result?>
        </div>

        <?php if ($verification_result !== 'INVALID' && $ticket_data): ?>
        <div class="panel">
            <h3>Ticket Details</h3>
            <div class="ticket-details">
                <div class="detail-row">
                    <span class="detail-label">Passenger Name</span>
                    <span class="detail-value">
                        <?= htmlspecialchars($ticket_data['passenger_name'])?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Route</span>
                    <span class="detail-value">
                        <?= htmlspecialchars($ticket_data['origin'])?> -
                        <?= htmlspecialchars($ticket_data['destination'])?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Departure</span>
                    <span class="detail-value">
                        <?= htmlspecialchars($ticket_data['travel_date'])?> @
                        <?= substr($ticket_data['departure_time'], 0, 5)?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Seat Number</span>
                    <span class="detail-value" style="font-size: 18px; color: #007bff;">
                        <?= htmlspecialchars($ticket_data['seat_number'])?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Bus</span>
                    <span class="detail-value">
                        <?= htmlspecialchars($ticket_data['bus_name'])?> (
                        <?= htmlspecialchars($ticket_data['bus_number'])?>)
                    </span>
                </div>
            </div>
            <a href="app.php" style="display: block; text-align: center; margin-top: 20px; color: #007bff;">Verify
                Another Ticket</a>
        </div>
        <?php
    else: ?>
        <div class="panel" style="text-align: center;">
            <p style="color: red;">
                <?= htmlspecialchars($error)?>
            </p>
            <a href="app.php" style="color: #007bff;">Try Again</a>
        </div>
        <?php
    endif; ?>

        <?php
else: ?>

        <div class="panel">
            <h3>QR Code Scanner</h3>
            <div id="reader"></div>
        </div>

        <div class="divider">OR</div>

        <div class="panel">
            <h3>Manual Entry</h3>
            <form method="POST" id="verifyForm">
                <input type="text" name="ticket_number" id="ticket_number_input"
                    placeholder="Enter Ticket Number (e.g. TKT-ABC-1)" required autocomplete="off">
                <button type="submit">Verify Ticket</button>
            </form>
        </div>

        <script>
            function onScanSuccess(decodedText, decodedResult) {
                // Stop scanner to prevent multiple submissions
                html5QrcodeScanner.clear();

                // Put the scanned text into the hidden field and submit the form
                document.getElementById('ticket_number_input').value = decodedText;
                document.getElementById('verifyForm').submit();
            }

            function onScanFailure(error) {
                // handle scan failure, usually better to ignore and keep scanning.
            }

            let html5QrcodeScanner = new Html5QrcodeScanner(
                "reader",
                { fps: 10, qrbox: { width: 250, height: 250 } },
                    /* verbose= */ false);
            html5QrcodeScanner.render(onScanSuccess, onScanFailure);
        </script>
        <?php
endif; ?>
    </div>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js"></script>
</body>

</html>