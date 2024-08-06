<?php
session_start();
include_once 'includes/header.php';
include_once 'includes/nav.php';
include_once 'classes/Database.php';
include_once 'classes/Product.php';
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

$user_id = $_SESSION['user_id'];
$cartItems = array();
$total = 0;

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
    $address = htmlspecialchars(strip_tags($_POST['address']));

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

        // Redirect to invoice
        header("Location: invoice.php");
        exit();
    }
}
?>

<div class="container">
    <h2>Checkout</h2>
    <form method="POST" action="">
        <div class="form-group">
            <label for="address">Shipping Address:</label>
            <textarea class="form-control" id="address" name="address" required></textarea>
        </div>
        <h3>Order Summary</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo '$' . number_format($item['price'], 2); ?></td>
                        <td><?php echo '$' . number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Submit Order</button>
    </form>
</div>

<?php include_once 'includes/footer.php'; ?>