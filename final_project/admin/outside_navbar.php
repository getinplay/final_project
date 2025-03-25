<nav>
    <div class="logo">
        <img src="../uploads/gip_logo.png" alt="logo" width="auto" height="50px">
    </div>
    <div class="left">
        <ul class="ul-box">
            <li class="nav-item">
                <a class="nav-link" href="../admin_home.php" id="gameManagementLink">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../game_management.php" id="gameManagementLink">Game Management</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../user_management.php" id="userManagementLink">User Management</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../terms_condition.php" id="termsLink">Terms & Condition</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../games_slots.php" id="viewSlotsLink">Book Game</a>
            </li>
        </ul>
    </div>
    <div class="profile" onclick="toggleProfilePopup()">
        <img src="../uploads/bravo.jpeg" alt="">
        <div class="profile-popup">
            <a href="../admin_profile.php"><i class="fa-solid fa-user"></i> Profile</a>
            <a href="../admin_history.php"><i class="fa-solid fa-calendar-days"></i> Bookings</a>
            <a href="../admin_login.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
   
</nav>
<nav class="mobile-nav">
    <div class="mobile-menu">
        <i class="fa-solid fa-bars"></i>
    </div>
    <div class="logo">
        <img src="../uploads/gip_logo.png" alt="logo" width="50px" height="50px">
    </div>
    
    <div class="profile" onclick="toggleProfilePopup()">
        <img src="../uploads/bravo.jpeg" alt="">
        <div class="profile-popup">
            <a href="../admin_profile.php"><i class="fa-solid fa-user"></i> Profile</a>
            <a href="../admin_history.php"><i class="fa-solid fa-calendar-days"></i> Bookings</a>
            <a href="../admin_login.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </div>
    </div>
</nav>
<div class="mobile-box">
        <ul class="m-ul-box">
            <li class="nav-item">
                <a class="m-nav-link" href="../admin_home.php" id="gameManagementLink">Home</a>
            </li>
            <li class="nav-item">
                <a class="m-nav-link" href="../game_management.php" id="gameManagementLink">Game Management</a>
            </li>
            <li class="nav-item">
                <a class="m-nav-link" href="../user_management.php" id="userManagementLink">User Management</a>
            </li>
            <li class="nav-item">
                <a class="m-nav-link" href="../terms_condition.php" id="termsLink">Terms and Conditions</a>
            </li>
            <li class="nav-item">
                <a class="m-nav-link" href="../games_slots.php" id="viewSlotsLink">Book Game</a>
            </li>
          
        </ul>
    </div>
    <script>
        // Function to toggle the profile popup for both desktop and mobile
const profileIcons = document.querySelectorAll('.profile');
const profilePopups = document.querySelectorAll('.profile-popup');

profileIcons.forEach((profileIcon) => {
  profileIcon.addEventListener('click', function (event) {
    event.stopPropagation(); // Prevent the event from bubbling up
    profileIcon.classList.toggle('show');
  });
});

// Close the popup if the user clicks outside the profile
document.addEventListener('click', function (event) {
  profileIcons.forEach((profileIcon) => {
    if (!profileIcon.contains(event.target)) {
      profileIcon.classList.remove('show');
    }
  });
});
        // Toggle the mobile box visibility with transition
        document.querySelector('.mobile-menu').addEventListener('click', function() {
            var mobileBox = document.querySelector('.mobile-box');
            mobileBox.classList.toggle('show'); // Toggle the 'show' class to display the menu
        });
        window.addEventListener('resize', function() {
    var mobileBox = document.querySelector('.mobile-box');
    if (window.innerWidth >= 768) {
        mobileBox.classList.remove('show'); // Hide the mobile menu when resizing to desktop
    }
});
    </script>