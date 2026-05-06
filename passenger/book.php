<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('passenger');

$user_id = $_SESSION['user_id'];
$trip_id = (int)($_GET['trip_id'] ?? 0);

if (!$trip_id) {
    header("Location: app.php");
    exit;
}

// --- 1. Fetch Trip & Bus Details ---
$sql = "SELECT t.*, b.bus_name, b.bus_number, b.total_seats, r.origin, r.destination 
        FROM trips t
        JOIN buses b ON t.bus_id = b.id
        JOIN routes r ON t.route_id = r.id
        WHERE t.id = $trip_id";

$res = mysqli_query($conn, $sql);
$trip = mysqli_fetch_assoc($res);

if (!$trip) {
    die("Error: Trip not found.");
}

$error = '';

/**
 * --- 2. Handle Booking Submission ---
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_seats_str = mysqli_real_escape_string($conn, $_POST['selected_seats'] ?? '');
    
    if (empty($selected_seats_str)) {
        $error = "You must select at least one seat.";
    }
    else {
        $selected_seats = explode(',', $selected_seats_str);

        // Start Transaction
        mysqli_begin_transaction($conn);
        try {
            $alreadyBooked = false;
            foreach ($selected_seats as $seat) {
                // Check if someone else just booked this seat
                $seat = (int)$seat;
                $chk = "SELECT id FROM bookings WHERE trip_id = $trip_id AND seat_number = $seat AND status IN ('pending', 'paid')";
                $chkRes = mysqli_query($conn, $chk);
                
                if (mysqli_num_rows($chkRes) > 0) {
                    $alreadyBooked = true;
                    break;
                }
            }

            if ($alreadyBooked) {
                mysqli_rollback($conn);
                $error = "Sorry, one of the seats you picked was just taken. Please choose another.";
            }
            else {
                $booking_ids = [];
                $total_amount = count($selected_seats) * $trip['price'];
                
                foreach ($selected_seats as $seat) {
                    $seat = (int)$seat;
                    $ins = "INSERT INTO bookings (user_id, trip_id, seat_number, status) VALUES ($user_id, $trip_id, $seat, 'pending')";
                    mysqli_query($conn, $ins);
                    $booking_ids[] = mysqli_insert_id($conn);
                }
                
                mysqli_commit($conn);

                $_SESSION['pending_bookings'] = $booking_ids;
                $_SESSION['pending_amount'] = $total_amount;
                $_SESSION['pending_trip'] = $trip_id;

                header("Location: payment.php");
                exit;
            }
        }
        catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "A system error occurred. Please try again later.";
        }
    }
}

$total_seats = (int)$trip['total_seats'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats - Mobus</title>
    
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
        <h2>Bus Ticket System</h2>
        <div class="nav-links">
            <a href="app.php">Find a Bus</a>
            <a href="tickets.php">My Tickets</a>
        </div>
        <div>
            Hi, <?= htmlspecialchars($_SESSION['name'])?> 
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL?>/logout.php" class="nav-logout">Logout</a>
        </div>
    </div>

    <div class="content">
        <div class="u-card trip-info">
            <h3><?= htmlspecialchars($trip['origin'])?> &rarr; <?= htmlspecialchars($trip['destination'])?></h3>
            <p><strong>Bus:</strong> <?= htmlspecialchars($trip['bus_name'])?> (<?= htmlspecialchars($trip['bus_number'])?>)</p>
            <p><strong>Date:</strong> <?= $trip['travel_date']?> at <?= substr($trip['departure_time'], 0, 5)?></p>
            <p><strong>Price:</strong> UGX <?= number_format($trip['price'], 0)?> per seat</p>
        </div>

        <div class="u-card bus-layout">
            <h4>Pick Your Seats</h4>

            <div class="legend">
                <div class="legend-item"><div class="color-box"></div> Available</div>
                <div class="legend-item"><div class="color-box selected" style="background: var(--success-bg);"></div> Selected</div>
                <div class="legend-item"><div class="color-box booked" style="background: var(--error-bg);"></div> Already Booked</div>
            </div>

            // Container for the interactive bus seating layout
            <div class="seats-grid" id="seatsGrid">
                <?php for ($i = 1; $i <= $total_seats; $i++): ?>
                    <div class="seat" data-seat="<?= $i?>"><?= $i?></div>
                    <?php if ($i % 5 == 2): ?>
                        <div class="seat aisle-space"></div>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>

            <div class="booking-panel">
                <p>Seats Selected: <strong id="selectedCount">0</strong></p>
                <p>Total Cost: <strong style="color: var(--accent);">UGX <span id="totalAmount">0</span></strong></p>
                
                <form method="POST" id="bookingForm">
                    <input type="hidden" name="selected_seats" id="selectedSeatsInput" value="">
                    
                    <?php if ($error): ?>
                        <div class="error" style="margin-bottom: 10px;"><?= htmlspecialchars($error)?></div>
                    <?php endif; ?>

                    <button type="button" id="bookBtn" disabled style="width: 100%;">Confirm Booking</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const tripId = <?= $trip_id?>;
        const pricePerSeat = <?= $trip['price']?>;
        let selectedSeats = [];

        function refreshSeats() {
            // Periodically check for newly booked seats from the database
            fetch(`../api/get_booked_seats.php?trip_id=${tripId}`)
                .then(res => res.json())
                .then(bookedSeats => {
                    const seats = document.querySelectorAll('.seat');
                    seats.forEach(seatElem => {
                        const seatNum = parseInt(seatElem.getAttribute('data-seat'));
                        
                        if (bookedSeats.includes(seatNum) || bookedSeats.includes(String(seatNum))) {
                            seatElem.classList.add('booked');
                            seatElem.classList.remove('selected');
                            selectedSeats = selectedSeats.filter(s => s !== seatNum);
                            updateDisplay();
                        } else {
                            seatElem.classList.remove('booked');
                        }
                    });
                })
                .catch(err => console.error("Polling error", err));
        }

        refreshSeats();
        // Set interval to refresh seat availability every 5 seconds (Live Polling)
        setInterval(refreshSeats, 5000);

        // Use event delegation on the grid to handle clicks on individual seats
        document.getElementById('seatsGrid').addEventListener('click', function(e) {
            const seatElem = e.target.closest('.seat');
            if (!seatElem || seatElem.classList.contains('aisle-space') || seatElem.classList.contains('booked')) return;
            
            const seatNum = parseInt(seatElem.getAttribute('data-seat'));
            
            if (seatElem.classList.contains('selected')) {
                seatElem.classList.remove('selected');
                selectedSeats = selectedSeats.filter(s => s !== seatNum);
            } else {
                seatElem.classList.add('selected');
                selectedSeats.push(seatNum);
            }

            updateDisplay();
        });

        function updateDisplay() {
            document.getElementById('selectedCount').innerText = selectedSeats.length;
            document.getElementById('totalAmount').innerText = (selectedSeats.length * pricePerSeat).toLocaleString();
            document.getElementById('selectedSeatsInput').value = selectedSeats.join(',');

            const bookBtn = document.getElementById('bookBtn');
            bookBtn.disabled = selectedSeats.length === 0;
        }

        document.getElementById('bookBtn').addEventListener('click', function () {
            if (selectedSeats.length > 0) {
                document.getElementById('bookingForm').submit();
            }
        });
    </script>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. DATABASE TRANSACTIONS:
 * When booking multiple seats, we use mysqli_begin_transaction(). This ensures that 
 * either ALL of your seats are booked, or NONE of them are. If one seat is taken 
 * by someone else during the process, we use mysqli_rollback() to cancel everything.
 * 
 * 2. REAL-TIME POLLING:
 * The JavaScript function refreshSeats() runs every 5 seconds. It asks the server 
 * "which seats are taken?" so that you don't try to book a seat that someone else 
 * just grabbed while you were looking at the screen.
 * 
 * 3. FOR UPDATE (Conceptual):
 * In professional systems, we "lock" the rows while checking availability. 
 * Since this is a school project, we use basic Transactions to keep it simple.
 * 
 * 4. SESSION STORAGE:
 * Once the seats are saved in the 'bookings' table as 'pending', we store the 
 * Booking IDs in the session ($_SESSION['pending_bookings']) so the payment page 
 * knows what you are paying for.
 */
?>