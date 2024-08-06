<?php
session_start();
include_once 'includes/header.php';
include_once 'includes/nav.php';
include_once 'classes/Database.php';
include_once 'classes/User.php';
include_once 'classes/Cart.php';
include_once 'classes/Order.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$cart = new Cart();
$order = new Order($db);
$errorMessages = [];

$user_id = $_SESSION['user_id'];
$cartItems = array();
$total = 0;

// List of Canadian province codes
$provinces = [
    'AB' => 'Alberta',
    'BC' => 'British Columbia',
    'MB' => 'Manitoba',
    'NB' => 'New Brunswick',
    'NL' => 'Newfoundland and Labrador',
    'NS' => 'Nova Scotia',
    'NT' => 'Northwest Territories',
    'NU' => 'Nunavut',
    'ON' => 'Ontario',
    'PE' => 'Prince Edward Island',
    'QC' => 'Quebec',
    'SK' => 'Saskatchewan',
    'YT' => 'Yukon'
];

// Fetch product details from session cart
foreach ($cart->getCartItems() as $productId => $quantity) {
    $product = new Product($db);
    $product->id = $productId;
    $productDetails = $product->getProductById($productId);

    if ($productDetails) {
        $cartItems[] = array(
            'product_id' => $productId,
            'name' => $productDetails['name'],
            'price' => $productDetails['price'],
            'quantity' => $quantity,
        );
        $total += $productDetails['price'] * $quantity;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate the input data
    $streetAddress = filter_input(INPUT_POST, 'street_address', FILTER_SANITIZE_STRING);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
    $postalCode = filter_input(INPUT_POST, 'postal_code', FILTER_SANITIZE_STRING);
    $state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $cardName = filter_input(INPUT_POST, 'card_name', FILTER_SANITIZE_STRING);
    $cardNumber = filter_input(INPUT_POST, 'card_number', FILTER_SANITIZE_STRING);
    $cardExpiry = filter_input(INPUT_POST, 'card_expiry', FILTER_SANITIZE_STRING);
    $cardCVV = filter_input(INPUT_POST, 'card_cvv', FILTER_SANITIZE_STRING);

    // Store order
    $orderId = $order->createOrder($user_id, $total, $address);

    if ($orderId) {
        // Store order items
        foreach ($cartItems as $item) {
            $order->addOrderItem($orderId, $item['product_id'], $item['quantity'], $item['price']);
        }

        // Clear cart
        $cart->clearCart();

        // Store order data in session and local storage
        $_SESSION['order'] = $orderId;
        echo "<script>localStorage.setItem('order', JSON.stringify({id: $orderId, total: $total}));</script>";


    // Validate each field
    if (empty($streetAddress)) {
        $errorMessages['street_address'] = "Street address is required.";
    }

    if (empty($city)) {
        $errorMessages['city'] = "City is required.";
    }

    // Validate Canadian postal code format
    if (empty($postalCode) || !preg_match('/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/', $postalCode)) {
        $errorMessages['postal_code'] = "Please enter a valid Canadian postal code (e.g., A1A 1A1).";
    }

    // Validate if the state is in the list of Canadian provinces
    if (empty($state) || !array_key_exists($state, $provinces)) {
        $errorMessages['state'] = "Please select a valid Canadian province.";
    }

    if (empty($phone) || !preg_match('/^\d{10,15}$/', $phone)) {
        $errorMessages['phone'] = "Please enter a valid phone number (10-15 digits).";
    }

    if (!$email) {
        $errorMessages['email'] = "Please enter a valid email address.";
    }

    if (empty($cardName)) {
        $errorMessages['card_name'] = "Cardholder's name is required.";
    }

    if (empty($cardNumber) || !preg_match('/^\d{16}$/', $cardNumber)) {
        $errorMessages['card_number'] = "Please enter a valid 16-digit card number.";
    }

    if (empty($cardExpiry) || !preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $cardExpiry)) {
        $errorMessages['card_expiry'] = "Please enter a valid expiry date (MM/YY).";
    }

    if (empty($cardCVV) || !preg_match('/^\d{3}$/', $cardCVV)) {
        $errorMessages['card_cvv'] = "Please enter a valid CVV (3 digits).";
    }

    if (empty($errorMessages)) {
        // Save the shipping address to the session
        $_SESSION['shipping_address'] = [
            'street_address' => $streetAddress,
            'city' => $city,
            'postal_code' => $postalCode,
            'state' => $state
        ];

        // Process the order (e.g., save to the database or handle payment)
        header("Location: invoice.php");
        exit();
    }
}}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4">Checkout</h2>
    <form method="POST" action="">
        <h3>Contact Details</h3>
        <div class="form-group">
            <label for="street_address">Street Address:</label>
            <input type="text" class="form-control" id="street_address" name="street_address" required value="<?php echo isset($_POST['street_address']) ? htmlspecialchars($_POST['street_address']) : ''; ?>">
            <?php if (isset($errorMessages['street_address'])) : ?>
                <small class="text-danger"><?php echo $errorMessages['street_address']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="city">City:</label>
            <input type="text" class="form-control" id="city" name="city" required value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
            <?php if (isset($errorMessages['city'])) : ?>
                <small class="text-danger"><?php echo $errorMessages['city']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="postal_code">Postal Code:</label>
            <input type="text" class="form-control" id="postal_code" name="postal_code" required value="<?php echo isset($_POST['postal_code']) ? htmlspecialchars($_POST['postal_code']) : ''; ?>">
            <?php if (isset($errorMessages['postal_code'])) : ?>
                <small class="text-danger"><?php echo $errorMessages['postal_code']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="state">Province:</label>
            <select class="form-control" id="state" name="state" required>
                <option value="">Select a province</option>
                <?php foreach ($provinces as $code => $name) : ?>
                    <option value="<?php echo $code; ?>" <?php echo (isset($_POST['state']) && $_POST['state'] === $code) ? 'selected' : ''; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errorMessages['state'])) : ?>
                <small class="text-danger"><?php echo $errorMessages['state']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number:</label>
            <input type="text" class="form-control" id="phone" name="phone" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            <?php if (isset($errorMessages['phone'])) : ?>
                <small class="text-danger"><?php echo $errorMessages['phone']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <?php if (isset($errorMessages['email'])) : ?>
                <small class="text-danger"><?php echo $errorMessages['email']; ?></small>
            <?php endif; ?>
        </div>

        <h3>Card Details</h3>
        <div class="form-group">
            <label for="card_name">Cardholder's Name:</label>
            <input type="text" class="form-control" id="card_name" name="card_name" required value="<?php echo isset($_POST['card_name']) ? htmlspecialchars($_POST['card_name']) : ''; ?>">
            <?php if (isset($errorMessages['card_name'])) : ?>
                <small class="text-danger"><?php echo $errorMessages['card_name']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="card_number">Card Number:</label>
            <input type="text" class="form-control" id="card_number" name="card_number" required value="<?php echo isset($_POST['card_number']) ? htmlspecialchars($_POST['card_number']) : ''; ?>">
            <?php if (isset($errorMessages['card_number'])) : ?>
                <small class="text-danger"><?php echo $errorMessages['card_number']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="card_expiry">Expiry Date (MM/YY):</label>
            <input type="text" class="form-control" id="card_expiry" name="card_expiry" required value="<?php echo isset($_POST['card_expiry']) ? htmlspecialchars($_POST['card_expiry']) : ''; ?>">
            <?php if (isset($errorMessages['card_expiry'])) : ?>
                <small class="text-danger"><?php echo $errorMessages['card_expiry']; ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="card_cvv">CVV:</label>
            <input type="text" class="form-control" id="card_cvv" name="card_cvv" required value="<?php echo isset($_POST['card_cvv']) ? htmlspecialchars($_POST['card_cvv']) : ''; ?>">
            <?php if (isset($errorMessages['card_cvv'])) : ?>
                <small class="text-danger"><?php echo $errorMessages['card_cvv']; ?></small>
            <?php endif; ?>
        </div>

        <div class="text-right">
            <button type="submit" class="btn btn-primary">Submit Order</button>
        </div>
    </form>
</div>

<?php include_once 'includes/footer.php'; ?>