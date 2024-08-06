<?php
session_start();
include_once 'includes/header.php';
include_once 'includes/nav.php';
include_once 'classes/Database.php';
include_once 'classes/Product.php';
include_once 'classes/Cart.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);
$cart = new Cart();

if (isset($_GET['add'])) {
    $productId = intval($_GET['add']);
    $cart->addToCart($productId, 1);
    header("Location: cart.php");
    exit();
}

if (isset($_GET['subtract'])) {
    $productId = intval($_GET['subtract']);
    $cartItems = $cart->getCartItems();
    if (isset($cartItems[$productId])) {
        if ($cartItems[$productId] > 1) {
            $cart->updateCart($productId, $cartItems[$productId] - 1);
        } else {
            $cart->removeFromCart($productId);
        }
    }
    header("Location: cart.php");
    exit();
}

if (isset($_GET['remove'])) {
    $productId = intval($_GET['remove']);
    $cart->removeFromCart($productId);
    header("Location: cart.php");
    exit();
}
?>

<div class="container">
    <h1>Your Cart</h1>
    <?php
    $cartItems = $cart->getCartItems();
    if (!empty($cartItems)) :
    ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalPrice = 0;
                foreach ($cartItems as $productId => $quantity) {
                    $product->id = $productId;
                    $productDetails = $product->getProductById($productId);
                    if ($productDetails) {
                        $itemTotal = $productDetails['price'] * $quantity;
                        $totalPrice += $itemTotal;
                ?>
                        <tr>
                            <td><?php echo htmlspecialchars($productDetails['name']); ?>
                                <a href="cart.php?subtract=<?php echo $productId; ?>" class="btn btn-warning <?php echo $quantity <= 1 ? 'disabled' : ''; ?>">-</a>
                                <?php echo $quantity; ?>
                                <a href="cart.php?add=<?php echo $productId; ?>" class="btn btn-success <?php echo $quantity >= 10 ? 'disabled' : ''; ?>">+</a>
                            </td>
                            <td><?php echo '$' . number_format($productDetails['price'], 2); ?></td>
                            <td><?php echo '$' . number_format($itemTotal, 2); ?></td>
                            <td><a href="cart.php?remove=<?php echo $productId; ?>" class="btn btn-danger">Remove</a></td>
                        </tr>
                <?php
                    }
                }
                ?>
                <tr>
                    <td colspan="3" align="right">Total Price</td>
                    <td><?php echo '$' . number_format($totalPrice, 2); ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
    <?php else : ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>