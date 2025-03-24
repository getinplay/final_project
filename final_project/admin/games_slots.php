<?php
// Fetch game data from the API

$api_url = "http://192.168.0.130/final_project/final_project/Api's/game_data.php";
$response = file_get_contents($api_url);
$games = json_decode($response, true);

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
    <link rel="stylesheet" href="css/game_slot.css">
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