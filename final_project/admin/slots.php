<?php
session_start(); // Start session
if (isset($_SESSION['games_data'])) {
    $games_data = $_SESSION['games_data'];
}
date_default_timezone_set('Asia/Kolkata'); // Set the default timezone to Indian time

if (!isset($_GET['game_id'])) {
    die("Game ID is missing.");
}

$game_id = $_GET['game_id'];

// Function to determine time of day (Morning/Afternoon/Evening/Night) based on end time
function getTimePeriod($time) {
    list($start, $end) = explode('-', $time);
    $endPeriod = strtoupper(substr($end, -2)); // Extract AM/PM from the end time
    $endTime = substr($end, 0, -2); // Remove AM/PM from the end time

    // Convert end time to 24-hour format for comparison
    $end24hr = DateTime::createFromFormat('g:iA', $endTime . $endPeriod);
    if (!$end24hr) return 'Unknown';

    $end24hr = $end24hr->format('H:i');

    // Determine time of day based on end time
    if ($end24hr >= '06:00' && $end24hr < '12:00') {
        return 'Morning';
    } elseif ($end24hr >= '12:00' && $end24hr < '17:00') {
        return 'Afternoon';
    } elseif ($end24hr >= '17:00' && $end24hr < '20:00') {
        return 'Evening';
    } else {
        return 'Night';
    }
}

// Function to fetch slot data from the API
function fetchSlotData($game_id, $date) {
    // Fetch all slots from the first API (slots_data.php)
    $slots_api_url = "http://192.168.0.130/final_project/final_project/Api's/filter_time.php";
    $slots_json_data = json_encode(['id' => $game_id, "date" => $date]);

    $slots_options = [
        'http' => [
            'header' => "Content-type: application/json\r\n",
            'method' => 'POST',
            'content' => $slots_json_data
        ]
    ];
    $slots_context = stream_context_create($slots_options);
    $slots_response = file_get_contents($slots_api_url, false, $slots_context);

    if ($slots_response === FALSE) {
        return ["error" => "Error fetching slots data."];
    }

    $slots_data = json_decode($slots_response, true);

    // Fetch booked slots from the second API (book_slots.php)
    $booked_slots_api_url = "http://192.168.0.130/final_project/final_project/Api's/book_slots.php";
    $booked_json_data = json_encode(['game_id' => $game_id, 'date' => $date]);

    $booked_options = [
        'http' => [
            'header' => "Content-type: application/json\r\n",
            'method' => 'POST',
            'content' => $booked_json_data
        ]
    ];
    $booked_context = stream_context_create($booked_options);
    $booked_response = file_get_contents($booked_slots_api_url, false, $booked_context);

    if ($booked_response === FALSE) {
        return ["error" => "Error fetching booked slots data."];
    }

    $booked_slots_data = json_decode($booked_response, true);

    // Extract booked slots from the response
    $booked_slots = array_column($booked_slots_data['booked_slots'], 'slot');

    // Compare and remove booked slots from available slots
    $available_slots = [];
    foreach ($slots_data['slots'] as $index => $slot) {
        if (!in_array($slot, $booked_slots)) {
            $available_slots[] = [
                'time' => $slot,
                'filter' => $slots_data['filter'][$index],
                'period' => strtoupper(substr($slot, -2)), // AM/PM
                'time_of_day' => getTimePeriod($slot) // Morning/Afternoon/Evening/Night
            ];
        }
    }

    return [
        'name' => $slots_data['name'],
        'available_slots' => $available_slots,
        'booked_slots' => $booked_slots_data['booked_slots']
    ];
}

// Get the selected date (default is today)
$selected_date = isset($_GET['date']) ? $_GET['date'] : date("Y/m/d");

// Fetch slot data for the selected date
$slots_data = fetchSlotData($game_id, $selected_date);

if (isset($slots_data['error'])) {
    die($slots_data['error']);
}

$name = $slots_data['name'];
$available_slots = $slots_data['available_slots'];
$booked_slots = $slots_data['booked_slots'];

// Get the selected filters (default is All)
$selected_filter = isset($_GET['filter']) ? $_GET['filter'] : '30min';
$selected_period = isset($_GET['period']) ? $_GET['period'] : 'All';
$selected_time_of_day = isset($_GET['time_of_day']) ? $_GET['time_of_day'] : 'All';

// Filter slots based on the selected filters
$filtered_slots = array_filter($available_slots, function ($slot) use ($selected_filter, $selected_period, $selected_time_of_day) {
    $filter_match = ($selected_filter === 'All' || $slot['filter'] === $selected_filter);
    $period_match = ($selected_period === 'All' || $slot['period'] === $selected_period);
    $time_of_day_match = ($selected_time_of_day === 'All' || $slot['time_of_day'] === $selected_time_of_day);
    return $filter_match && $period_match && $time_of_day_match;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Slots</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/view_slots.css">
</head>
<body>
    <?php include "navbar.php" ?>

<!-- Loader elements -->
<div id="loaderContainer" class="loader-container"></div>
<div id="loader" class="loader"></div>
<div id="loaderMessage" class="loader-message">Processing, please wait...</div>

<!-- Popup for Messages -->
<div id="messageOverlay"></div>
<div id="messagePopup">
    <p id="messageText"></p>
    <button id="closeMessagePopup">OK</button>
</div>
<div class="body_css">
    <h1><?php echo strtoupper($name); ?> </h1>
    <!-- Add this right after <body> -->
    <div class="date-picker">
        <?php
        $dates = [
            [
                'label' => '<small>' . substr(date("l"),0,3) . '</small><br><span class="date-large">' . date("j") . '</span><br><small>' . date("M") . '</small>', // Today
                'date' => date("Y/m/d")
            ],
            [
                'label' => '<small>' . substr(date("l", strtotime("+1 day")),0,3) . '</small><br><span class="date-large">' . date("j", strtotime("+1 day")) . '</span><br><small>' . date("M", strtotime("+1 day")) . '</small>', // Tomorrow
                'date' => date("Y/m/d", strtotime("+1 day"))
            ],
            [
                'label' => '<small>' . substr(date("l", strtotime("+2 days")),0,3) . '</small><br><span class="date-large">' . date("j", strtotime("+2 days")) . '</span><br><small>' . date("M", strtotime("+2 days")) . '</small>', // Day After
                'date' => date("Y/m/d", strtotime("+2 days"))
            ]
        ];

        foreach ($dates as $date) {
            $is_active = ($date['date'] === $selected_date) ? 'active' : '';
            echo '
            <a href="slots.php?game_id=' . $game_id . '&date=' . $date['date'] . '&filter=' . $selected_filter . '&period=' . $selected_period . '&time_of_day=' . $selected_time_of_day . '">
                <button class="' . $is_active . '">' . $date['label'] . '</button>
            </a>';
        }
        ?>
    </div>
    <div class="filter-buttons">
        <!-- Duration Filter -->
         <div class="durationfltr">
        <?php $filters = ['30min','1hr']; ?>
        <?php foreach ($filters as $filter): ?>
            <a href="slots.php?game_id=<?= $game_id ?>&date=<?= $selected_date ?>&filter=<?= $filter ?>&period=<?= $selected_period ?>&time_of_day=<?= $selected_time_of_day ?>">
                <button class="<?= ($filter === $selected_filter) ? 'active' : '' ?>"><?= $filter ?></button>
            </a>
        <?php endforeach; ?>
        </div>

        <!-- Time of Day Filter -->
         <div class="mrngfltr">
        <?php $times_of_day = ['All','Morning','Afternoon','Evening','Night']; ?>
        <?php foreach ($times_of_day as $time):?>
            <a href="slots.php?game_id=<?= $game_id ?>&date=<?= $selected_date ?>&filter=<?= $selected_filter ?>&period=<?= $selected_period ?>&time_of_day=<?= $time ?>">
                <button class="<?= ($time === $selected_time_of_day) ? 'active' : '' ?>"><?= $time ?></button>
            </a>
        <?php endforeach;?>
        </div>
    </div>
    <div class="slots-container">
        <?php
        if (!empty($filtered_slots)) {
            foreach ($filtered_slots as $slot) {
                echo '
                <div class="slot-card available" data-slot="' . $slot['time'] . '" data-filter="' . $slot['filter'] . '">
                    <p>' . $slot['time'] . '</p>
                </div>';
            }
        } else {
            echo "<p>No slots available.</p>";
        }
        ?>
    </div>

    <!-- Booked Slots Details Section -->
    <div class="booked-details">
        <h2>Booking Details</h2>
        <table class="booked-table">
    <thead>
        <tr>
            <th>Action</th> <!-- New column for cancellation -->
            <th>Booking ID</th>
            <th>Username</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Slot</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($booked_slots)) {
            foreach ($booked_slots as $booking) {
                echo '
                <tr>
                <td>
                    <button class="cancel-btn" 
                            data-book-id="' . $booking['id'] . '" 
                            data-phone-no="' . $booking['phone_no'] . '" 
                            data-slot="' . $booking['slot'] . '" 
                            data-date="' . $selected_date . '">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
                    <td>#' . htmlspecialchars($booking['id']) . '</td>
                    <td>' . htmlspecialchars($booking['username']) . '</td>
                    <td>' . htmlspecialchars($booking['phone_no']) . '</td>
                    <td>' . htmlspecialchars($booking['email']) . '</td>
                    <td>' . htmlspecialchars($booking['slot']) . '</td>
                </tr>';
            }
        } else {
            echo '<tr><td colspan="5">No slots booked for this date.</td></tr>';
        }
        ?>
    </tbody>
</table>
    </div>

    <!-- Popup for Booking -->
    <div id="overlay"></div>
    <div id="popup">
        <h3>Book Slot</h3>
        <input type="text" id="phoneNumber" placeholder="Enter Phone Number" maxlength="10">
        <span id="phone_status"></span>
        <button id="submitBooking" disabled>Submit</button>
        <button id="closePopup">Close</button>
    </div>

    <!-- Booked Slots Cards for Mobile -->
    <div class="booked-cards">
        <?php
        if (!empty($booked_slots)) {
            foreach ($booked_slots as $booking) {
                echo '
                <div class="booked-card">
                    <p><strong>Username:</strong> ' . htmlspecialchars($booking['username']) . '</p>
                    <p><strong>Phone:</strong> ' . htmlspecialchars($booking['phone_no']) . '</p>
                    <p><strong>Email:</strong> ' . htmlspecialchars($booking['email']) . '</p>
                    <p><strong>Slot:</strong> ' . htmlspecialchars($booking['slot']) . '</p>
                    <button class="cancel-btn" 
                            data-book-id="' . $booking['id'] . '" 
                            data-phone-no="' . $booking['phone_no'] . '" 
                            data-slot="' . $booking['slot'] . '" 
                            data-date="' . $selected_date . '">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>';
            }
        } else {
            echo '<p>No slots booked for this date.</p>';
        }
        ?>
    </div>
</div>
<script>
// Helper functions for loader
function showLoader(message = 'Processing, please wait...') {
    document.body.classList.add('loader-active');
    document.getElementById('loaderContainer').style.display = 'flex';
    document.getElementById('loader').style.display = 'block';
    document.getElementById('loaderMessage').textContent = message;
    document.getElementById('loaderMessage').style.display = 'block';
}

function hideLoader() {
    document.body.classList.remove('loader-active');
    document.getElementById('loaderContainer').style.display = 'none';
    document.getElementById('loader').style.display = 'none';
    document.getElementById('loaderMessage').style.display = 'none';
}

// JavaScript for handling the popup and API submission
const overlay = document.getElementById('overlay');
const popup = document.getElementById('popup');
const phoneInput = document.getElementById('phoneNumber');
const submitButton = document.getElementById('submitBooking');
const closePopupButton = document.getElementById('closePopup');
let selectedSlot = null;
document.getElementById("popup").style.justifyContent = "center";

// Show popup when a slot is clicked
document.querySelectorAll('.slot-card.available').forEach(slot => {
    slot.addEventListener('click', () => {
        selectedSlot = slot.getAttribute('data-slot');
        popup.style.display = 'block';
        overlay.style.display = 'block';
    });
});

// Close popup
closePopupButton.addEventListener('click', () => {
    popup.style.display = 'none';
    overlay.style.display = 'none';
});

// Validate phone number in real-time
phoneInput.addEventListener('input', () => {
    const phone = phoneInput.value;
    if (phone.length === 10 && /^\d+$/.test(phone)) {
        submitButton.disabled = false;
    } else {
        submitButton.disabled = true;
    }
});

// Function to show the message popup with conditional buttons
function showMessage(message, isUserNotRegistered = false) {
    const messagePopup = document.getElementById('messagePopup');
    const messageText = document.getElementById('messageText');
    const messageOverlay = document.getElementById('messageOverlay');
    
    // Clear previous content
    messageText.innerText = message;
    
    // Remove all buttons except the first one (which is the default OK button)
    const buttons = messagePopup.querySelectorAll('button');
    for (let i = 1; i < buttons.length; i++) {
        buttons[i].remove();
    }
    
    // Get or create the close button
    let closeButton = messagePopup.querySelector('button');
    if (!closeButton) {
        closeButton = document.createElement('button');
        messagePopup.appendChild(closeButton);
    }
    
    // Configure the close button
    closeButton.innerText = isUserNotRegistered ? 'Ok' : 'Ok';
    closeButton.onclick = () => {
        messagePopup.style.display = 'none';
        messageOverlay.style.display = 'none';
    };
    
    // Reset button styles
    closeButton.style.margin = '0 5px';
    closeButton.style.backgroundColor = '#4A5BE6';
    
    // Add "Add New User" button if needed
    if (isUserNotRegistered) {
        const addUserButton = document.createElement('button');
        addUserButton.innerText = 'Add New User';
        addUserButton.style.backgroundColor = '#4A5BE6';
        addUserButton.style.margin = '0 5px';
        addUserButton.onclick = () => {
            window.location.href = 'user_management/user_form.php';
        };
        closeButton.insertAdjacentElement('afterend', addUserButton);
    }
    
    // Set text color based on success/error
    messageText.style.color = (message.includes('Failed') || message.includes('error') || message.includes('Error')) 
        ? 'red' 
        : 'green';
    
    // Show the popup
    messagePopup.style.display = 'block';
    messageOverlay.style.display = 'block';
}

// Submit booking with confirmation
submitButton.addEventListener('click', async () => {
    const phone = phoneInput.value.trim();
    if (!phone || phone.length !== 10 || !/^\d+$/.test(phone)) {
        showMessage('Please enter a valid 10-digit phone number');
        return;
    }

    // Show confirmation dialog
    const confirmBooking = confirm(`Are you sure you want to book this slot?\n\nSlot: ${selectedSlot}\nPhone: ${phone}`);
    if (!confirmBooking) return;

    const gameId = <?php echo $game_id; ?>;
    const date = "<?php echo $selected_date; ?>";
    const slot = selectedSlot;
    const selectedFilter = "<?php echo $selected_filter; ?>";
    let price = 0.0;

    showLoader('Booking your slot...');
    submitButton.disabled = true;

    try {
        // Fetch all game data from game_data.php
        const gameDataResponse = await fetch(`http://192.168.0.130/final_project/final_project/Api's/game_data.php`);
        const gameData = await gameDataResponse.json();
        
        if (Array.isArray(gameData)) {
            const selectedGame = gameData.find(game => game.id == gameId);

            if (selectedGame) {
                if (selectedFilter === '1hr') {
                    price = parseFloat(selectedGame.hour);
                } else {
                    price = parseFloat(selectedGame.half_hour);
                }
            } else {
                hideLoader();
                submitButton.disabled = false;
                showMessage('Game not found for the selected game ID.');
                return;
            }
        } else {
            hideLoader();
            submitButton.disabled = false;
            showMessage('Failed to retrieve game data.');
            return;
        }

        // Prepare booking data
        const data = {
            game_id: gameId,
            date: date,
            slot: slot,
            phone_no: phone,
            price: price.toFixed(2)
        };

        // Send booking request
        const response = await fetch("http://192.168.0.130/final_project/final_project/Api's/book_game_admin.php", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });

        const rawResponse = await response.text();
        console.log('Raw Response:', rawResponse);

        try {
            const result = JSON.parse(rawResponse);
            if (result.success) {
                hideLoader();
                showMessage('Slot booked successfully!');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                hideLoader();
                submitButton.disabled = false;
                if (result.message === "Phone number is not registered") {
                    showMessage('Failed to book slot: ' + result.message, true);
                } else {
                    showMessage('Failed to book slot: ' + result.message);
                }
            }
        } catch (error) {
            hideLoader();
            submitButton.disabled = false;
            console.error('Error parsing JSON:', error);
            showMessage('An error occurred while booking the slot.');
        }

    } catch (error) {
        hideLoader();
        submitButton.disabled = false;
        showMessage('An error occurred while booking the slot.');
    }
});

// Update cancellation handler
document.querySelectorAll('.cancel-btn').forEach(button => {
    button.addEventListener('click', async () => {
        const bookId = button.getAttribute('data-book-id');
        const phoneNo = button.getAttribute('data-phone-no');
        const slot = button.getAttribute('data-slot');
        const date = button.getAttribute('data-date');
        
        // Confirm cancellation with the user
        const confirmCancel = confirm('Are you sure you want to cancel this booking?');
        if (!confirmCancel) return;

        showLoader('Cancelling booking...');
        button.disabled = true;

        try {
            // Prepare cancellation data
            const data = {
                auth: 'admin',
                phone_no: phoneNo,
                date: date,
                slot: slot,
                book_id: bookId
            };
            
            // Send cancellation request
            const response = await fetch("http://192.168.0.130/final_project/final_project/Api's/slot_cancle.php", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                hideLoader();
                showMessage('Booking cancelled successfully!');
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                hideLoader();
                button.disabled = false;
                showMessage('Failed to cancel booking: ' + result.message);
            }
        } catch (error) {
            hideLoader();
            button.disabled = false;
            showMessage('An error occurred while cancelling the booking.');
        }
    });
});
</script>
</body>
</html>