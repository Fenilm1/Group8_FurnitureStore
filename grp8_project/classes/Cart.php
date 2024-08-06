<?php
class Cart
{
    // Add product to cart session and local storage
    public function addToCart($productId, $quantity)
    {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }

        $this->updateLocalStorage();
    }

    // Remove product from cart session and local storage
    public function removeFromCart($productId)
    {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
        }

        $this->updateLocalStorage();
    }

    // Update product quantity in cart session and local storage
    public function updateCart($productId, $quantity)
    {
        if ($quantity > 0) {
            $_SESSION['cart'][$productId] = $quantity;
        } else {
            $this->removeFromCart($productId);
        }

        $this->updateLocalStorage();
    }

    // Get cart items from session
    public function getCartItems()
    {
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    }

    // Clear cart session and local storage
    public function clearCart()
    {
        unset($_SESSION['cart']);
        $this->updateLocalStorage();
    }

    // Update local storage with cart data
    private function updateLocalStorage()
    {
        echo "<script>
            localStorage.setItem('cart', JSON.stringify(" . json_encode($_SESSION['cart']) . "));
        </script>";
    }
}
