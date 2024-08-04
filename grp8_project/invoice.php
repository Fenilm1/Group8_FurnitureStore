<?php
session_start();
require('fpdf184/fpdf.php'); // Ensure this path is correct
include_once 'classes/Database.php';
include_once 'classes/Product.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

class PDF extends FPDF
{
    // Page header
    function Header()
    {
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Move to the right
        $this->Cell(80);
        // Title
        $this->Cell(30, 10, 'Invoice', 0, 1, 'C');
        // Line break
        $this->Ln(10);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

// Create new PDF instance
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
$database = new Database();
$db = $database->getConnection();
$product = new Product($db);

foreach ($_SESSION['cart'] as $productId => $quantity) {
    $product->id = $productId;
    $productDetails = $product->getProductById($productId);
    $itemTotal = $productDetails['price'] * $quantity;
    $totalPrice += $itemTotal;

    $pdf->Cell(40, 10, htmlspecialchars($productDetails['name']), 1);
    $pdf->Cell(40, 10, $quantity, 1);
    $pdf->Cell(40, 10, '$' . number_format($productDetails['price'], 2), 1);
    $pdf->Cell(40, 10, '$' . number_format($itemTotal, 2), 1);
    $pdf->Ln();
}

$pdf->Cell(120, 10, 'Total Price', 1);
$pdf->Cell(40, 10, '$' . number_format($totalPrice, 2), 1);

$pdf->Output('I', 'invoice.pdf'); // Output to the browser

session_destroy();
