<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('passenger');

if (!isset($_SESSION['pending_bookings']) || empty($_SESSION['pending_bookings'])) {
    header("Location: app.php");
    exit;
}

$booking_ids  = $_SESSION['pending_bookings'];
$total_amount = $_SESSION['pending_amount'];
$trip_id      = $_SESSION['pending_trip'];
$user_id      = $_SESSION['user_id'];

$error = '';
$success = '';
$tickets = [];

/**
 * Handle Payment Submission
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $network = mysqli_real_escape_string($conn, $_POST['network'] ?? '');
    $phone_number = mysqli_real_escape_string($conn, trim($_POST['phone_number'] ?? ''));

    if (empty($network) || empty($phone_number)) {
        $error = "Please provide your phone number and network.";
    }
    else {
        // Start Transaction
        // Use a database transaction to ensure all booking steps succeed or fail together
        mysqli_begin_transaction($conn);
        try {
            $transaction_id = 'TXN-' . strtoupper(uniqid());
            $seat_price = $total_amount / count($booking_ids);

            foreach ($booking_ids as $b_id) {
                $b_id = (int)$b_id;
                $ticket_number = 'TKT-' . date('Ymd') . '-' . $b_id;

                // 1. Record payment
                $sqlPay = "INSERT INTO payments (booking_id, amount, phone_number, network, transaction_id, payment_status) 
                           VALUES ($b_id, $seat_price, '$phone_number', '$network', '$transaction_id', 'completed')";
                mysqli_query($conn, $sqlPay);

                // 2. Update booking
                $sqlUpd = "UPDATE bookings SET status = 'paid', ticket_number = '$ticket_number' WHERE id = $b_id";
                mysqli_query($conn, $sqlUpd);

                // 3. Generate QR and save ticket
                $qr_data = "Ticket: " . $ticket_number;
                // Generate a QR code URL using the open-source QRServer API
                $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qr_data);
                
                $sqlTkt = "INSERT INTO tickets (booking_id, qr_code_url) VALUES ($b_id, '$qr_url')";
                mysqli_query($conn, $sqlTkt);

                $tickets[] = $ticket_number;
            }

            mysqli_commit($conn);

            unset($_SESSION['pending_bookings']);
            unset($_SESSION['pending_amount']);
            unset($_SESSION['pending_trip']);

            $success = "Success! Payment processed. You booked " . count($tickets) . " seat(s).";
        }
        catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Payment failed. System error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment - Mobus</title>
    
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
        <h2>Checkout</h2>
        <div>
            Hi, <?= htmlspecialchars($_SESSION['name'])?> 
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL ?>/logout.php" class="nav-logout">Logout</a>
        </div>
    </div>

    <div class="content">
        <?php if ($success): ?>
            <div class="success-panel">
                <div class="success-icon">✓</div>
                <h3>Payment Completed</h3>
                <p><?= htmlspecialchars($success)?></p>
                <div style="margin-top: 25px;">
                    <a href="tickets.php" class="btn-primary">View My Tickets</a>
                    <a href="app.php" class="btn-secondary">Back to Search</a>
                </div>
            </div>
        <?php else: ?>
            <a href="book.php?trip_id=<?= $trip_id?>" class="back-link">&larr; Change Seats</a>
            
            <div class="checkout-box">
                <h3>Select Payment Method</h3>
                <p style="color: #666; font-size: 0.9em; margin-bottom: 20px;">Safe & Secure Mobile Money Checkout</p>

                <?php if ($error): ?>
                    <div class="error"><?= htmlspecialchars($error)?></div>
                <?php endif; ?>

                <div class="total-bar">
                    <span>Payable Amount</span>
                    // Format number with commas for better readability
                    <strong>UGX <?= number_format($total_amount, 0)?></strong>
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label>Network</label>
                        <select name="network" required>
                            <option value="">-- Choose Carrier --</option>
                            <option value="MTN">MTN Mobile Money</option>
                            <option value="Airtel">Airtel Money</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Phone Number (Mobile Money)</label>
                        <input type="text" name="phone_number" placeholder="e.g. 0771234567" required>
                    </div>

                    <p style="font-size: 0.8em; color: #888; margin: 15px 0;">
                        Wait for the prompt on your phone after clicking confirm.
                    </p>

                    <button type="submit" class="btn-pay">Confirm & Pay</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. TRANSACTION SAFETY:
 * When processing a payment, we multiple things happen: we insert a record into 'payments', 
 * update 'bookings', and insert into 'tickets'. We use mysqli_begin_transaction() to 
 * make sure that either all three happen perfectly, or none of them happen at all.
 * 
 * 2. TICKET GENERATION:
 * We create a unique ticket number string (e.g., TKT-20240101-15) and then call 
 * a free External API (qrserver.com) to generate a QR code image for it.
 * 
 * 3. SESSION CLEANUP:
 * Once the payment is successful, we use unset() to remove the "pending" booking 
 * information from the user's session. This prevents them from accidentally 
 * paying for the same seats twice.
 * 
 * 4. MOBILE MONEY SIMULATION:
 * In this system, we simply mark the payment as 'completed' immediately. In a 
 * real system, this is where you would wait for a "webhook" or a confirmation 
 * message from the telecom company (MTN/Airtel).
 */
?>