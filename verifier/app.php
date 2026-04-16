<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('verifier');

$verification_result = null;
$ticket_data = null;
$error = '';

/**
 * Handle Verification Request
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket_number = mysqli_real_escape_string($conn, trim($_POST['ticket_number'] ?? ''));

    if (empty($ticket_number)) {
        $error = "Please provide a ticket number.";
    }
    else {
        // Step 1: Look for the ticket
        $sql = "SELECT b.*, u.name as passenger_name, tr.departure_time, tr.travel_date,
                       r.origin, r.destination, bus.bus_name, bus.bus_number
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN trips tr ON b.trip_id = tr.id
                JOIN routes r ON tr.route_id = r.id
                JOIN buses bus ON tr.bus_id = bus.id
                WHERE b.ticket_number = '$ticket_number'";
        
        $res = mysqli_query($conn, $sql);
        $ticket_data = mysqli_fetch_assoc($res);

        // Step 2: Validate the ticket status
        if (!$ticket_data) {
            $verification_result = 'INVALID';
            $error = "This ticket code does not exist in our system.";
        }
        elseif ($ticket_data['status'] !== 'paid') {
            $verification_result = 'INVALID';
            $error = "This ticket has not been paid for yet.";
        }
        elseif ($ticket_data['is_verified'] == 1) {
            $verification_result = 'USED';
            $error = "This ticket has already been used to board!";
        }
        else {
            // VALID TICKET! 
            // Step 3: Mark as verified
            $tid = $ticket_data['id'];
            mysqli_query($conn, "UPDATE bookings SET is_verified = 1 WHERE id = $tid");
            
            $verification_result = 'VALID';
            $ticket_data['is_verified'] = 1;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifier - Mobus</title>
    
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css?v=2.0">
    <script>
        (function(){
            var t = localStorage.getItem("mobus_theme") || "dark";
            document.documentElement.setAttribute("data-theme", t);
        })();
    </script>
</head>

<body>
    <div class="header">
        <h2>Conductor App</h2>
        <div>
            Hi, <?= htmlspecialchars($_SESSION['name'])?> 
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL ?>/logout.php" class="nav-logout">Logout</a>
        </div>
    </div>

    <div class="content">
        <?php if ($verification_result): ?>
            <div class="result-box <?= strtolower($verification_result)?>">
                <h3><?= $verification_result ?></h3>
                <?php if ($error) echo "<p>$error</p>"; ?>
            </div>

            <?php if ($verification_result === 'VALID' || $verification_result === 'USED'): ?>
                <div class="panel ticket-info-panel">
                    <h3>Ticket Details</h3>
                    <p><strong>Passenger:</strong> <?= htmlspecialchars($ticket_data['passenger_name'])?></p>
                    <p><strong>Seat Number:</strong> <span class="seat-highlight"><?= htmlspecialchars($ticket_data['seat_number'])?></span></p>
                    <p><strong>Route:</strong> <?= htmlspecialchars($ticket_data['origin'])?> to <?= htmlspecialchars($ticket_data['destination'])?></p>
                    <p><strong>Bus:</strong> <?= htmlspecialchars($ticket_data['bus_name'])?> (<?= htmlspecialchars($ticket_data['bus_number'])?>)</p>
                    
                    <a href="app.php" class="btn-primary" style="margin-top:20px; display:block; text-align:center;">Scan Next</a>
                </div>
            <?php else: ?>
                <a href="app.php" class="btn-secondary" style="display:block; text-align:center;">Try Again</a>
            <?php endif; ?>

        <?php else: ?>
            <div class="panel">
                <h3>Scan QR Code</h3>
                <div id="reader" style="width: 100%;"></div>
            </div>

            <div style="text-align:center; margin: 20px 0; color: #888;">OR</div>

            <div class="panel">
                <h3>Manual Entry</h3>
                <form method="POST" id="verifyForm">
                    <input type="text" name="ticket_number" id="manual_input" placeholder="Enter Ticket ID (e.g. TKT-2024...)" required>
                    <button type="submit">Verify Now</button>
                </form>
            </div>

            <script>
                function onScanSuccess(decodedText) {
                    html5QrcodeScanner.clear();
                    document.getElementById('manual_input').value = decodedText;
                    document.getElementById('verifyForm').submit();
                }

                let html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", { fps: 10, qrbox: 250 }
                );
                html5QrcodeScanner.render(onScanSuccess);
            </script>
        <?php endif; ?>
    </div>
    
    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. MULTI-TABLE JOIN:
 * We use a single SQL query to pull data from 5 different tables! Bookings, Users, Trips, 
 * Routes, and Buses are all linked using their ID fields. This gives the conductor 
 * a complete picture of who and what they are verifying.
 * 
 * 2. TICKET STATUS LOGIC:
 * - INVALID: The ticket code doesn't exist in the database.
 * - UNPAID: The booking exists but the passenger hasn't paid.
 * - USED: The passenger already verified this ticket once. This prevents people 
 *   from sharing the same ticket screenshot.
 * - VALID: Everything is correct, and the passenger can board.
 * 
 * 3. VERIFICATION MARKING:
 * Once a ticket is verified, we set `is_verified = 1`. This effectively "cancels" 
 * the ticket so it cannot be reused.
 * 
 * 4. QR SCANNER:
 * We use the 'html5-qrcode' JavaScript library. It accesses the device camera, 
 * turns the QR picture into text, and then submits a POST request to this PHP file.
 */
?>