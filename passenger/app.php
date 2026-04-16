<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('passenger');

// Step 1: Fetch unique origins and destinations
$resRoutes = mysqli_query($conn, "SELECT DISTINCT origin, destination FROM routes ORDER BY origin ASC");
$origins = [];
$destinations = [];

while ($row = mysqli_fetch_assoc($resRoutes)) {
    $origins[] = $row['origin'];
    $destinations[] = $row['destination'];
}
$origins = array_unique($origins);
$destinations = array_unique($destinations);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Buses - Mobus</title>
    
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
            <a href="app.php" class="active">Find a Bus</a>
            <a href="tickets.php">My Tickets</a>
        </div>
        <div>
            Hi, <?= htmlspecialchars($_SESSION['name'])?> 
            <span class="nav-divider"></span>
            <a href="<?= BASE_URL?>/logout.php" class="nav-logout">Logout</a>
        </div>
    </div>

    <div class="content">
        <div class="u-card">
            <h3>Where are you traveling to?</h3>

            <form class="search-box" id="searchForm">
                <div class="form-group">
                    <label>From (Origin)</label>
                    <select name="origin" id="origin" required>
                        <option value="">-- Select Origin --</option>
                        <?php foreach ($origins as $origin): ?>
                            <option value="<?= htmlspecialchars($origin)?>"><?= htmlspecialchars($origin)?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>To (Destination)</label>
                    <select name="destination" id="destination" required>
                        <option value="">-- Select Destination --</option>
                        <?php foreach ($destinations as $destination): ?>
                            <option value="<?= htmlspecialchars($destination)?>"><?= htmlspecialchars($destination)?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Travel Date</label>
                    <input type="date" name="travel_date" id="travel_date" min="<?= date('Y-m-d') ?>" required>
                </div>

                <button type="submit">Search Available Buses</button>
            </form>
        </div>

        <div class="results" id="resultsContainer">
            <p style="text-align: center; color: #777;">Results will appear here...</p>
        </div>
    </div>

    <script>
        /**
         * Search Handler (Client Side)
         */
        document.getElementById('searchForm').addEventListener('submit', function (e) {
            e.preventDefault();
            
            const origin = document.getElementById('origin').value;
            const destination = document.getElementById('destination').value;
            const travelDate = document.getElementById('travel_date').value;
            const resultsContainer = document.getElementById('resultsContainer');

            resultsContainer.innerHTML = '<p style="text-align: center;">Searching for the best trips...</p>';

            fetch(`../api/search_buses.php?origin=${encodeURIComponent(origin)}&destination=${encodeURIComponent(destination)}&travel_date=${encodeURIComponent(travelDate)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        resultsContainer.innerHTML = `<p style="color: red; text-align: center;">${data.error}</p>`;
                        return;
                    }
                    
                    if (data.length === 0) {
                        resultsContainer.innerHTML = '<p style="text-align: center;">No buses found for this date. Try another date.</p>';
                        return;
                    }

                    resultsContainer.innerHTML = '';
                    data.forEach(trip => {
                        resultsContainer.innerHTML += `
                            <div class="trip-card">
                                <div class="trip-details">
                                    <h4>${trip['Departure Time'].substring(0, 5)} - ${trip['Bus Name']}</h4>
                                    <p><strong>Capacity:</strong> ${trip['Remaining Seats']} seats left out of ${trip['Total Seats']}</p>
                                    <p><strong>Fare:</strong> UGX ${parseFloat(trip['Price']).toLocaleString()}</p>
                                </div>
                                <div class="trip-action">
                                    <a href="book.php?trip_id=${trip['Trip ID']}" class="btn-book">Book Now</a>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(err => {
                    resultsContainer.innerHTML = '<p style="color: red; text-align: center;">Connection error. Please try again.</p>';
                });
        });
    </script>
    <script src="<?= BASE_URL ?>/js/mobus-theme.js?v=2.0"></script>
</body>

</html>

<?php
/**
 * --- DOCUMENTATION SECTION ---
 * 
 * 1. DYNAMIC DROPDOWNS:
 * We fetch all existing origins and destinations from the 'routes' table. 
 * array_unique() helps us make sure each location only appears once in the list.
 * 
 * 2. AJAX (FETCH API):
 * Instead of reloading the page when searching, we use JavaScript 'fetch'. 
 * This calls the file 'search_buses.php' in the background and gets the results in JSON format.
 * 
 * 3. CLIENT-SIDE RENDERING:
 * Once the data is received, the JavaScript loop goes through each trip and 
 * generates the HTML "cards" you see on the screen.
 * 
 * 4. DATE RESTRICTION:
 * The "min" attribute in the date input is set to today's date using PHP date('Y-m-d'). 
 * This prevents users from selecting a travel date in the past.
 */
?>