<?php
// Fetch game data from the API
session_start(); 

$api_url = "http://192.168.0.130/final_project/final_project/Api's/game_data.php";
$response = file_get_contents($api_url);
$games = json_decode($response, true);

// Store data in session
$_SESSION['games_data'] = $games;

// Debug: Check stored data

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Games Page</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="css/navbar.css">
    <style>
:root {
    --primary-red: #4A5BE6;
    --dark-red: rgb(21, 41, 190);
    --light-bg: rgb(255, 255, 255);
    --light-grey: rgba(255, 255, 255, 0.87);
}

body {
    background: var(--light-bg);
    min-height: 100vh;
    margin: 0;
    padding: 0;
}

.container {
    margin-top: 40px;
    padding: 2rem; /* Reduced padding for smaller screens */
}

.games-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem; /* Reduced gap for smaller screens */
    padding: 1rem;
}

.game-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    position: relative;
    border: none;
}

.game-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 15px rgba(37, 87, 224, 0.2);
}

.card-image {
    height: 200px; /* Reduced height for smaller screens */
    position: relative;
    overflow: hidden;
}

.card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.game-card:hover .card-image img {
    transform: scale(1.05);
}

.card-content {
    padding: 1rem; /* Reduced padding for smaller screens */
    position: relative;
}

.game-title {
    color: #2d2d2d;
    font-weight: bold;
    margin-top: 10px;
    font-size: 1.5rem; /* Reduced font size for smaller screens */
    text-align: center;
    text-decoration: none !important;
}

.price-tag {
    text-align: center;
    color: #2d2d2d;
    padding: 0.5rem 1rem;
    margin: 0.5rem 0;
}

.price-tag i,
span {
    font-size: 1.1rem; /* Reduced font size for smaller screens */
}

.time-option {
    display: flex;
    justify-content: space-between;
    margin: 0.8rem 0;
}

.popular-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--primary-red);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.8rem;
    font-weight: 600;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

/* Search Bar Styles */
.search-container {
    display: grid;
    background-color: rgba(235, 231, 231, 0.87);
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    transition: box-shadow 0.3s;
    color: #6B7280;
    padding: 0.55rem;
    margin-bottom: 1.25rem;
    border: 1px solid #D1D5DB;
    background-color: #F3F4F6;
    border-radius: 0.75rem;
}

.search-container:focus-within {
    box-shadow: 0 3px 8px rgb(150, 150, 150);
}

.search-container div {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
}

.search-bar {
    width: 100%;
    background-color: transparent;
    border: none;
    font-size: 1rem; /* Reduced font size for smaller screens */
    color: gray;
}

.search-bar:focus {
    outline: none;
}

.search-icon {
    color: gray;
    cursor: pointer;
    font-size: 1rem; /* Reduced font size for smaller screens */
}

.clear-icon {
    color: gray;
    float: right;
    cursor: pointer;
    font-size: 1rem; /* Reduced font size for smaller screens */
}

/* Media Queries for Responsiveness */

/* Tablets (768px and below) */
@media (max-width: 768px) {
    .container {
        padding: 1.5rem; /* Adjusted padding for tablets */
    }

    .games-container {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Adjusted grid for tablets */
        gap: 1rem;
    }

    .card-image {
        height: 180px; /* Adjusted height for tablets */
    }

    .game-title {
        font-size: 1.4rem; /* Adjusted font size for tablets */
    }

    .price-tag i,
    span {
        font-size: 1rem; /* Adjusted font size for tablets */
    }

    .search-bar {
        font-size: 0.9rem; /* Adjusted font size for tablets */
    }

    .search-icon,
    .clear-icon {
        font-size: 0.9rem; /* Adjusted font size for tablets */
    }
}

/* Mobile Devices (480px and below) */
@media (max-width: 480px) {
    .container {
        padding: 1rem; /* Adjusted padding for mobile */
    }

    .games-container {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Adjusted grid for mobile */
        gap: 0.8rem;
    }

    .card-image {
        height: 150px; /* Adjusted height for mobile */
    }

    .game-title {
        font-size: 1.2rem; /* Adjusted font size for mobile */
    }

    .price-tag i,
    span {
        font-size: 0.9rem; /* Adjusted font size for mobile */
    }

    .search-bar {
        font-size: 0.8rem; /* Adjusted font size for mobile */
    }

    .search-icon,
    .clear-icon {
        font-size: 0.8rem; /* Adjusted font size for mobile */
    }

    .popular-badge {
        padding: 0.4rem 0.8rem; /* Adjusted padding for mobile */
        font-size: 0.7rem; /* Adjusted font size for mobile */
    }
}

/* Small Mobile Devices (360px and below) */
@media (max-width: 360px) {
    .games-container {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); /* Adjusted grid for small mobile */
        gap: 0.5rem;
    }

    .card-image {
        height: 120px; /* Adjusted height for small mobile */
    }

    .game-title {
        font-size: 1rem; /* Adjusted font size for small mobile */
    }

    .price-tag i,
    span {
        font-size: 0.8rem; /* Adjusted font size for small mobile */
    }

    .search-bar {
    width: auto;
    background-color: transparent;
    border: none;
    font-size: 1rem; /* Reduced font size for smaller screens */
    color: gray;
}

    .search-icon,
    .clear-icon {
        font-size: 0.7rem; /* Adjusted font size for small mobile */
    }

    .popular-badge {
        padding: 0.3rem 0.6rem; /* Adjusted padding for small mobile */
        font-size: 0.6rem; /* Adjusted font size for small mobile */
    }
}
    </style>
</head>

<body>
    <?php include "navbar.php"; ?>

    <div class="container">
        <h1 class="text-center  display-4 fw-bold" style="color: var(--primary-red);">
            <i class="fas fa-gamepad me-3"></i>Games
        </h1>

        <!-- Search Bar -->
        <div class="games-container">
            <div class="search-container">
                <div style="position: relative; width: 100%;">
                    
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchInput" class="search-bar" placeholder="Search games...">
                  
                    <i class="fas fa-times clear-icon" id="clearSearch"></i>
                </div>
            </div>
        </div>


        <div class="games-container" id="gamesContainer">
            <?php if (!empty($games)): ?>
                <?php foreach ($games as $game): ?>
                    <a href="slots.php?game_id=<?= $game['id'] ?>" class="game-card text-decoration-none">
                        <div class="card-image">
                            <img src="http://192.168.0.130/final_project/final_project/admin/<?= $game['card_image'] ?>"
                                alt="<?= $game['name'] ?>">
                        </div>
                        <div class="card-content">
                            <h3 class="game-title"><?= strtoupper($game['name']) ?></h3>
                            <div class="d-flex flex-column">
                                <div class="price-tag">
                                    <!-- <i class="fas fa-coins"></i> -->
                                    <span>₹<?= $game['half_hour'] ?> /30mins</span><br>
                                    <!-- </div>
                                <div class="price-tag"> -->
                                    <!-- <i class="fas fa-clock"></i> -->
                                    <span>₹<?= $game['hour'] ?> /1hr</span>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h3 class="text-muted">No games available at the moment</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Live Search Functionality
            $('#searchInput').on('input', function () {
        const searchTerm = $(this).val().toLowerCase();
        const $games = $('.game-card');
        let gamesFound = false;

        $games.each(function () {
            const gameName = $(this).find('.game-title').text().toLowerCase();
            if (gameName.includes(searchTerm)) {
                $(this).show();
                gamesFound = true; // At least one match found
            } else {
                $(this).hide();
            }
        });

        if (!gamesFound) {
            if ($('#noGamesMessage').length === 0) { // Avoid duplicate messages
                $('<div id="noGamesMessage" class="no-games-message" style="text-align:center">No games found.</div>').appendTo('#gamesContainer');
            }
        } else {
            $('#noGamesMessage').remove(); // Remove the message if matches are found
        }
    });

            // Clear Search Input
            $('#clearSearch').on('click', function () {
                $('#searchInput').val('').trigger('input');
                $(this).hide();
            });
        });
    </script>
</body>

</html>