<?php

// Database Connection
include('../connect_database.php');

// Check if the form data is sent
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $gameId = isset($_POST['gameId']) && !empty($_POST['gameId']) ? $_POST['gameId'] : null;
    $gameName = $_POST['gameName'];
    $price30 = $_POST['price30'];
    $price60 = $_POST['price60'];
    $slots = $_POST['slots'];
    $filter = $_POST['filter'];

    // Image Upload Logic
    $gameImage = $_FILES['gameImage'];
    $sliderImage = $_FILES['sliderImage'];

    // Validate images and move them to a folder
    $uploadDir = "uploads/";

    // Define a default path in case of empty image
    $defaultGameImagePath = "uploads/rick.jpg";
    $defaultSliderImagePath = "uploads/rick.jpg";

    // Game Image
    if (empty($gameImage['name'])) {
        // Set to default path if no image is uploaded
        $gameImagePath = $defaultGameImagePath;
    } else {
        $gameImageName = $gameImage['name'];
        $gameImageTmpName = $gameImage['tmp_name'];
        $gameImageExt = strtolower(pathinfo($gameImageName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($gameImageExt, $allowedExtensions)) {
            echo "Invalid game image format. Only JPG, JPEG, PNG, GIF allowed.";
            exit;
        }

        // If the image is valid, move it to the upload directory
        $gameImagePath = $uploadDir . basename($gameImageName);
        move_uploaded_file($gameImageTmpName, $gameImagePath);
    }

    // Slider Image
    if (empty($sliderImage['name'])) {
        // Set to default path if no image is uploaded
        $sliderImagePath = $defaultSliderImagePath;
    } else {
        $sliderImageName = $sliderImage['name'];
        $sliderImageTmpName = $sliderImage['tmp_name'];
        $sliderImageExt = strtolower(pathinfo($sliderImageName, PATHINFO_EXTENSION));

        if (!in_array($sliderImageExt, $allowedExtensions)) {
            echo "Invalid slider image format. Only JPG, JPEG, PNG, GIF allowed.";
            exit;
        }

        // If the image is valid, move it to the upload directory
        $sliderImagePath = $uploadDir . basename($sliderImageName);
        move_uploaded_file($sliderImageTmpName, $sliderImagePath);
    }

    // Now you can use $gameImagePath and $sliderImagePath as the paths to your images
    if ($gameId) {
        // Update existing game including filter_value column
        $stmt = $conn->prepare("UPDATE games SET name = ?, hour = ?, half_hour = ?, slider_image = ?, card_image = ?, slots = ?, filter_value = ? WHERE id = ?");
        $stmt->bind_param("sssssssi", $gameName, $price60, $price30, $sliderImagePath, $gameImagePath, $slots, $filter, $gameId); // Bind the filter_value here
        if ($stmt->execute()) {
            // Redirect to user management page on success
            header('Location: ../game_management.php');
            exit();
        } else {
            // Handle error in case of failure
            echo "Error updating game: " . $stmt->error;
            exit();
        }
    }
     else {
        // Insert new game
        $stmt = $conn->prepare("INSERT INTO games (name, hour, half_hour, slider_image, card_image, slots, filter_value) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $gameName, $price60, $price30, $sliderImagePath, $gameImagePath, $slots, $filter); // Bind the filter_value here
        if ($stmt->execute()) {
            // Redirect to user management page on success
            header('Location: ../game_management.php');
            exit();
        } else {
            // Handle error in case of failure
            echo "Error adding game: " . $stmt->error;
            exit();
        }
    }

    $stmt->close();
    $conn->close();
}
?>