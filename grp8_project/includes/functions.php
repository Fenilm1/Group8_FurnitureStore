<?php
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags($input));
}

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}
?>
