// Your JavaScript code here
function navigateTo(event, url) {
    event.preventDefault();
    window.location.href = url;
}

function toggleNav() {
    var nav = document.getElementById("mySidenav");
    var menuIcon = document.getElementById("menu-icon");
    var navLinks = document.querySelectorAll("#mySidenav a");

    if (nav.classList.contains("open")) {
        // Close the menu
        nav.classList.remove("open");
        menuIcon.innerHTML = "&#9776; Menu";
        navLinks.forEach(link => {
            link.style.opacity = 0;
        });
    } else {
        // Open the menu
        nav.classList.add("open");
        menuIcon.innerHTML = "&times; Close";
        navLinks.forEach(link => {
            link.style.opacity = 1;
        });
    }
}


function toggleProfile() {
    var userInfo = document.getElementById("user-info");

    if (userInfo.style.display === "block") {
        userInfo.style.opacity = "0";
        setTimeout(function () {
            userInfo.style.display = "none";
        }, 500);
    } else {
        userInfo.style.display = "block";
        setTimeout(function () {
            userInfo.style.opacity = "1";
        }, 50);
        // Commented out fetchUserInfo() as it's not defined in your provided code.
        // fetchUserInfo();
    }
}
