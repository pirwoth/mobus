<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('passenger');

if (!isset($_SESSION['pending_bookings']) || empty($_SESSION['pending_bookings'])) {
    header("Location: app.php");
    exit;
}

$booking_ids = $_SESSION['pending_bookings'];
$total_amount = $_SESSION['pending_amount'];
$trip_id = $_SESSION['pending_trip'];
$user_id = $_SESSION['user_id'];

$error = '';
$success = '';
$tickets = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $network = $_POST['network'] ?? '';
    $phone_number = trim($_POST['phone_number'] ?? '');

    if (empty($network) || empty($phone_number)) {
        $error = "Please fill in all payment details.";
    }
    else {
        $pdo->beginTransaction();
        try {
            // Simulate Payment verification/processing
            $transaction_id = 'TXN-' . strtoupper(uniqid());

            // 1. Insert into payments
            $paymentStmt = $pdo->prepare("INSERT INTO payments (booking_id, amount, phone_number, network, transaction_id, payment_status) VALUES (?, ?, ?, ?, ?, 'completed')");

            // 2. Update bookings to 'paid' and generate ticket numbers
            $updateBookingStmt = $pdo->prepare("UPDATE bookings SET status = 'paid', ticket_number = ? WHERE id = ?");

            // 3. Insert into tickets
            $ticketStmt = $pdo->prepare("INSERT INTO tickets (booking_id, qr_code_url) VALUES (?, ?)");

            // Note: Since amount is total_amount and a payment row points to a single booking_id in our schema,
            // we will log the payment per seat or just use the first booking_id for the transaction?
            // The schema has booking_id in payments. Let's record a payment record for each booking to match schema,
            // dividing the amount or recording per seat. We'll record per seat price.
            $seat_price = $total_amount / count($booking_ids);

            foreach ($booking_ids as $b_id) {
                // Generate a unique ticket number
                $ticket_number = 'TKT-' . strtoupper(uniqid()) . '-' . $b_id;

                // Record Payment
                $paymentStmt->execute([$b_id, $seat_price, $phone_number, $network, $transaction_id . '-' . $b_id]);

                // Update Booking
                $updateBookingStmt->execute([$ticket_number, $b_id]);

                // Generate QR Code URL
                $qr_data = "Ticket: " . $ticket_number . " | BookingID: " . $b_id;
                $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qr_data);

                // Store Ticket
                $ticketStmt->execute([$b_id, $qr_url]);

                $tickets[] = $ticket_number;
            }

            $pdo->commit();

            // Clear session variables
            unset($_SESSION['pending_bookings']);
            unset($_SESSION['pending_amount']);
            unset($_SESSION['pending_trip']);

            $success = "Payment successful! Your tickets: " . implode(', ', $tickets);
        }
        catch (Exception $e) {
            $pdo->rollBack();
            $error = "Payment failed. Please try again. " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout - Bus Ticket System</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .header {
            background: #007bff;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content {
            padding: 40px 20px;
            max-width: 500px;
            margin: auto;
        }

        .checkout-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .checkout-box h3 {
            margin-top: 0;
            text-align: center;
            color: #333;
        }

        .amount-display {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            color: #28a745;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }

        select,
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        button {
            width: 100%;
            padding: 15px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
        }

        button:hover {
            background: #218838;
        }

        .error {
            color: red;
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
        }

        .success {
            color: green;
            margin-bottom: 15px;
            font-weight: bold;
            background: #d4edda;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            line-height: 1.5;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Bus Ticket System - App</h2>
        <div>
            Hi,
            <?= htmlspecialchars($_SESSION['name'])?> |
            <a href="/logout.php" style="color: white;">Logout</a>
        </div>
    </div>

    <div class="content">
        <?php if ($success): ?>
        <div class="success">
            <h3>Payment Confirmed</h3>
            <?= htmlspecialchars($success)?><br><br>
            <a href="app.php"
                style="display:inline-block; padding:10px 20px; background:#007bff; color:white; text-decoration:none; border-radius:5px;">Return
                to Home</a>
        </div>
        <?php
else: ?>
        <a href="book.php?trip_id=<?= $trip_id?>" class="back-link">&larr; Back to Seats (Cancel)</a>
        <div class="checkout-box">
            <h3>Mobile Money Payment</h3>

            <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error)?>
            </div>
            <?php
    endif; ?>

            <div class="amount-display">
                Total: $
                <?= number_format($total_amount, 2)?>
            </div>

            <form method="POST">
                <div class="form-group">
                    <label>Select Network</label>
                    <select name="network" required>
                        <option value="">-- Choose Network --</option>
                        <option value="MTN">MTN Mobile Money</option>
                        <option value="Airtel">Airtel Money</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Mobile Money Number</label>
                    <input type="text" name="phone_number" placeholder="e.g. 0771234567" required>
                </div>

                <button type="submit">Confirm Payment</button>
            </form>
        </div>
        <?php
endif; ?>
    </div>
</body>

</html>