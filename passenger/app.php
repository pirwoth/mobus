<?php
require_once '../config/db.php';
require_once '../includes/auth_check.php';

checkRole('passenger');

$routesStmt = $pdo->query("SELECT DISTINCT origin, destination FROM routes ORDER BY origin ASC");
$routes = $routesStmt->fetchAll();
$origins = array_unique(array_column($routes, 'origin'));
$destinations = array_unique(array_column($routes, 'destination'));

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger App - Bus Ticket System</title>
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
            padding: 20px;
            max-width: 900px;
            margin: auto;
        }

        a.logout {
            color: white;
            text-decoration: underline;
        }

        .search-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }

        .form-group {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        select,
        input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            align-self: flex-end;
        }

        button:hover {
            background: #218838;
        }

        .results {
            display: grid;
            gap: 20px;
        }

        .trip-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .trip-details h4 {
            margin: 0 0 10px 0;
            color: #333;
        }

        .trip-details p {
            margin: 5px 0;
            color: #555;
        }

        .btn-book {
            background: #ffc107;
            color: black;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn-book:hover {
            background: #e0a800;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Bus Ticket System - App</h2>
        <div>
            Hi,
            <?= htmlspecialchars($_SESSION['name'])?> |
            <a href="/logout.php" class="logout">Logout</a>
        </div>
    </div>
    <div class="content">
        <h3>Search Standard Trips</h3>

        <form class="search-box" id="searchForm">
            <div class="form-group">
                <label>Origin</label>
                <select name="origin" id="origin" required>
                    <option value="">-- Choose Origin --</option>
                    <?php foreach ($origins as $origin): ?>
                    <option value="<?= htmlspecialchars($origin)?>">
                        <?= htmlspecialchars($origin)?>
                    </option>
                    <?php
endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Destination</label>
                <select name="destination" id="destination" required>
                    <option value="">-- Choose Destination --</option>
                    <?php foreach ($destinations as $destination): ?>
                    <option value="<?= htmlspecialchars($destination)?>">
                        <?= htmlspecialchars($destination)?>
                    </option>
                    <?php
endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Travel Date</label>
                <input type="date" name="travel_date" id="travel_date" required>
            </div>
            <button type="submit">Search</button>
        </form>

        <div class="results" id="resultsContainer">
            <!-- Results populated by JS -->
            <p style="text-align: center; color: #777;">Enter search details to find buses.</p>
        </div>
    </div>

    <script>
        document.getElementById('searchForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const origin = document.getElementById('origin').value;
            const destination = document.getElementById('destination').value;
            const travelDate = document.getElementById('travel_date').value;
            const resultsContainer = document.getElementById('resultsContainer');

            resultsContainer.innerHTML = '<p style="text-align: center;">Loading trips...</p>';

            fetch(`../api/search_buses.php?origin=${encodeURIComponent(origin)}&destination=${encodeURIComponent(destination)}&travel_date=${encodeURIComponent(travelDate)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        resultsContainer.innerHTML = `<p style="color: red; text-align: center;">${data.error}</p>`;
                        return;
                    }
                    if (data.length === 0) {
                        resultsContainer.innerHTML = '<p style="text-align: center;">No buses found for this route and date.</p>';
                        return;
                    }

                    resultsContainer.innerHTML = '';
                    data.forEach(trip => {
                        resultsContainer.innerHTML += `
                            <div class="trip-card">
                                <div class="trip-details">
                                    <h4>${trip['Departure Time'].substring(0, 5)} - ${trip['Bus Name']} (${trip['Bus Number']})</h4>
                                    <p><strong>Available Seats:</strong> ${trip['Available Seats']}</p>
                                    <p><strong>Price:</strong> $${parseFloat(trip['Price']).toFixed(2)}</p>
                                </div>
                                <div>
                                    <a href="book.php?trip_id=${trip['Trip ID']}" class="btn-book">Select Seats</a>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(err => {
                    resultsContainer.innerHTML = '<p style="color: red; text-align: center;">Error fetching trips.</p>';
                });
        });
    </script>
</body>

</html>