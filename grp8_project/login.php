<?php
session_start();
include_once 'includes/header.php';
include_once 'includes/nav.php';
include_once 'classes/Database.php';
include_once 'classes/User.php';
include_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $user = new User($db);
    $user->username = sanitizeInput($_POST['username']);
    $user->password = sanitizeInput($_POST['password']);

    // Check if the user is an admin
    $adminUsernames = ['dollyadmin', 'nidhiadmin', 'feniladmin'];
    $adminPassword = '1234';

    if (in_array($user->username, $adminUsernames) && $user->password == $adminPassword) {
        $_SESSION['logged_in'] = true;
        $_SESSION['is_admin'] = true; // Set admin flag
        header("Location: admin/index.php");
        exit();
    }

    // Regular user login
    if ($user->login()) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user->id;
        $_SESSION['username'] = $user->username;
        $_SESSION['is_admin'] = $user->is_admin;

        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid login credentials.";
    }
}
?>

<div class="container">
    <h2>Login</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Login</button>
    </form>
</div>

<?php include_once 'includes/footer.php'; ?>
