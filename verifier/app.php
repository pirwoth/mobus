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
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .header {
            background: #343a40;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content {
            padding: 20px;
            max-width: 600px;
            margin: auto;
        }

        a.logout {
            color: white;
            text-decoration: underline;
        }

        .panel {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .panel h3 {
            margin-top: 0;
            text-align: center;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            margin-bottom: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
        }

        button:hover {
            background: #0056b3;
        }

        .result-box {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: white;
            font-weight: bold;
            font-size: 24px;
        }

        .result-valid {
            background: #28a745;
        }

        .result-used {
            background: #ffc107;
            color: black;
        }

        .result-invalid {
            background: #dc3545;
        }

        .ticket-details {
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 5px;
            background: #fafafa;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #ddd;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-size: 14px;
        }

        .detail-value {
            font-weight: bold;
            color: #333;
        }

        #reader {
            width: 100%;
            max-width: 500px;
            margin: 0 auto 20px auto;
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            color: #888;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Conductor App</h2>
        <div>
            Hi,
            <?= htmlspecialchars($_SESSION['name'])?> |
            <a href="/logout.php" class="logout">Logout</a>
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
</body>

</html>