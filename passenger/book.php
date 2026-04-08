<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('passenger');

$user_id = $_SESSION['user_id'];
$trip_id = $_GET['trip_id'] ?? 0;

if (!$trip_id) {
    header("Location: app.php");
    exit;
}

// Fetch trip details
$stmt = $pdo->prepare("
    SELECT t.*, b.bus_name, b.bus_number, b.total_seats, r.origin, r.destination 
    FROM trips t
    JOIN buses b ON t.bus_id = b.id
    JOIN routes r ON t.route_id = r.id
    WHERE t.id = ?
");
$stmt->execute([$trip_id]);
$trip = $stmt->fetch();

if (!$trip) {
    die("Trip not found.");
}

$error = '';
$success = '';

// Handle booking submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_seats_str = $_POST['selected_seats'] ?? '';
    if (empty($selected_seats_str)) {
        $error = "No seats selected.";
    }
    else {
        $selected_seats = explode(',', $selected_seats_str);

        $pdo->beginTransaction();
        try {
            // Check if seats are already booked
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE trip_id = ? AND seat_number = ? AND status IN ('pending', 'paid') FOR UPDATE");
            $insertStmt = $pdo->prepare("INSERT INTO bookings (user_id, trip_id, seat_number, status) VALUES (?, ?, ?, 'pending')");

            $alreadyBooked = false;
            foreach ($selected_seats as $seat) {
                $checkStmt->execute([$trip_id, $seat]);
                if ($checkStmt->fetchColumn() > 0) {
                    $alreadyBooked = true;
                    break;
                }
            }

            if ($alreadyBooked) {
                $pdo->rollBack();
                $error = "One or more selected seats were already booked. Please try again.";
            }
            else {
                $booking_ids = [];
                $total_amount = count($selected_seats) * $trip['price'];
                // Book the seats
                foreach ($selected_seats as $seat) {
                    $insertStmt->execute([$user_id, $trip_id, $seat]);
                    $booking_ids[] = $pdo->lastInsertId();
                }
                $pdo->commit();

                $_SESSION['pending_bookings'] = $booking_ids;
                $_SESSION['pending_amount'] = $total_amount;
                $_SESSION['pending_trip'] = $trip_id;

                header("Location: payment.php");
                exit;
            }
        }
        catch (Exception $e) {
            $pdo->rollBack();
            $error = "An error occurred while booking. Please try again.";
        }
    }
}

// Fetch all seats for the structural loop
$total_seats = (int)$trip['total_seats'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Seats - Bus Ticket System</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/style.css">
    <script>
        (function(){var t=localStorage.getItem("mobus_theme")||"dark";
        document.documentElement.setAttribute("data-theme",t);})();
    </script>
</head>

<body>
    <div class="header">
        <h2>Bus Ticket System - App</h2>
        <div class="nav-links">
            <a href="app.php" class="active">Search</a>
            <a href="tickets.php" >My Tickets</a>
        </div>
        <div>
            Hi,
            <?= htmlspecialchars($_SESSION['name'])?> |
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL?>/logout.php" class="nav-logout">Logout</a>
        </div>
    </div>
    <div class="content">

        <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error)?>
        </div>
        <?php
endif; ?>
        <?php if ($success): ?>
        <div class="success">
            <?= htmlspecialchars($success)?>
        </div>
        <?php
endif; ?>

        <div class="trip-info">
            <h3>
                <?= htmlspecialchars($trip['origin'])?> &rarr;
                <?= htmlspecialchars($trip['destination'])?>
            </h3>
            <p><strong>Bus:</strong>
                <?= htmlspecialchars($trip['bus_name'])?> (
                <?= htmlspecialchars($trip['bus_number'])?>)
            </p>
            <p><strong>Date & Time:</strong>
                <?= $trip['travel_date']?> at
                <?= substr($trip['departure_time'], 0, 5)?>
            </p>
            <p><strong>Price per seat:</strong> $
                <?= number_format($trip['price'], 2)?>
            </p>
        </div>

        <div class="bus-layout">
            <h4>Select Your Seats</h4>

            <div class="legend">
                <div class="legend-item">
                    <div class="color-box" style="background: white;"></div> Available
                </div>
                <div class="legend-item">
                    <div class="color-box" style="background: #28a745; border-color: #28a745;"></div> Selected
                </div>
                <div class="legend-item">
                    <div class="color-box" style="background: #dc3545; border-color: #dc3545;"></div> Booked
                </div>
            </div>

            <div class="seats-grid" id="seatsGrid">
                <?php for ($i = 1; $i <= $total_seats; $i++): ?>
                <div class="seat" data-seat="<?= $i?>">
                    <?= $i?>
                </div>
                <?php
endfor; ?>
            </div>

            <div class="booking-panel">
                <p>Selected Seats: <span id="selectedCount">0</span></p>
                <p>Total Amount: $<span id="totalAmount">0.00</span></p>
                <form method="POST" id="bookingForm" onsubmit="return confirmBooking()">
                    <input type="hidden" name="selected_seats" id="selectedSeatsInput" value="">
                    <?php if (!$success): ?>
                    <button type="submit" id="bookBtn" disabled>Book Selected Seats</button>
                    <?php
endif; ?>
                </form>
            </div>
        </div>
    </div>

    <script>
        const tripId = <?= $trip_id?>;
        const pricePerSeat = <?= $trip['price']?>;
        let selectedSeats = [];

        // Fetch booked seats immediately
        fetch(`../api/get_booked_seats.php?trip_id=${tripId}`)
            .then(res => res.json())
            .then(bookedSeats => {
                const seats = document.querySelectorAll('.seat');
                seats.forEach(seatElem => {
                    const seatNum = parseInt(seatElem.getAttribute('data-seat'));
                    if (bookedSeats.includes(seatNum) || bookedSeats.includes(String(seatNum))) {
                        seatElem.classList.add('booked');
                    } else {
                        // Allow clicking
                        seatElem.addEventListener('click', function () {
                            toggleSeat(this, seatNum);
                        });
                    }
                });
            })
            .catch(err => console.error("Could not fetch booked seats", err));

        function toggleSeat(seatElem, seatNum) {
            if (seatElem.classList.contains('booked')) return;

            if (seatElem.classList.contains('selected')) {
                seatElem.classList.remove('selected');
                selectedSeats = selectedSeats.filter(s => s !== seatNum);
            } else {
                seatElem.classList.add('selected');
                selectedSeats.push(seatNum);
            }

            updatePanel();
        }

        function updatePanel() {
            document.getElementById('selectedCount').innerText = selectedSeats.length;
            document.getElementById('totalAmount').innerText = (selectedSeats.length * pricePerSeat).toFixed(2);
            document.getElementById('selectedSeatsInput').value = selectedSeats.join(',');

            const bookBtn = document.getElementById('bookBtn');
            if (bookBtn) {
                bookBtn.disabled = selectedSeats.length === 0;
            }
        }

        function confirmBooking() {
            if (selectedSeats.length === 0) return false;
            return confirm(`Are you sure you want to book ${selectedSeats.length} seats for $${(selectedSeats.length * pricePerSeat).toFixed(2)}?`);
        }
    </script>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js"></script>
</body>

</html>