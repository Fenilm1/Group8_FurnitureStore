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
    $user->email = sanitizeInput($_POST['email']);
    $user->is_admin = false;

    if ($user->create()) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Registration failed.";
    }
}
?>

<div class="container">
    <h2>Register</h2>
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
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>

<?php include_once 'includes/footer.php'; ?>
