<?php
session_start();
include_once 'includes/header.php';
include_once 'includes/nav.php';
include_once 'classes/Database.php';
include_once 'classes/Product.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

// Add product to cart
if (isset($_GET['add'])) {
    $productId = intval($_GET['add']);
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]++;
    } else {
        $_SESSION['cart'][$productId] = 1;
    }
    header("Location: cart.php");
    exit();
}

// Remove product from cart
if (isset($_GET['remove'])) {
    $productId = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
    header("Location: cart.php");
    exit();
}

?>

<div class="container">
    <h1>Your Cart</h1>
    <?php if (!empty($_SESSION['cart'])): ?>
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
                foreach ($_SESSION['cart'] as $productId => $quantity) {
                    $product->id = $productId;
                    $productDetails = $product->getProductById(); // Using getProductById to fetch product details
                    if ($productDetails) {
                        $itemTotal = $productDetails['price'] * $quantity;
                        $totalPrice += $itemTotal;
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($productDetails['name']); ?></td>
                            <td><?php echo $quantity; ?></td>
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
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>

<?php include_once 'includes/footer.php'; ?>
