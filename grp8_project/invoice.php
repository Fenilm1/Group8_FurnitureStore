<?php
session_start();
require('fpdf184/fpdf.php');
include_once 'classes/Database.php';
include_once 'classes/Order.php';
include_once 'classes/Product.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$order = new Order($db);
$product = new Product($db);

$orderId = $_SESSION['order'];

$orderDetails = $order->getOrderById($orderId);
$orderItems = $order->getOrderItems($orderId);

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, 'Invoice', 0, 1, 'C');
        $this->Ln(10);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

$pdf->Cell(0, 10, 'Thank you for your purchase! Here are the details of your order:', 0, 1);
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Product', 1);
$pdf->Cell(40, 10, 'Quantity', 1);
$pdf->Cell(40, 10, 'Price', 1);
$pdf->Cell(40, 10, 'Total', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);

$totalPrice = 0;

foreach ($orderItems as $item) {
    $productDetails = $product->getProductById($item['product_id']);
    $itemTotal = $item['price'] * $item['quantity'];
    $totalPrice += $itemTotal;

    $pdf->Cell(40, 10, htmlspecialchars($productDetails['name']), 1);
    $pdf->Cell(40, 10, $item['quantity'], 1);
    $pdf->Cell(40, 10, '$' . number_format($item['price'], 2), 1);
    $pdf->Cell(40, 10, '$' . number_format($itemTotal, 2), 1);
    $pdf->Ln();
}

$pdf->Cell(120, 10, 'Total Price', 1);
$pdf->Cell(40, 10, '$' . number_format($totalPrice, 2), 1);

$pdf->Output('I', 'invoice.pdf');

session_destroy();
