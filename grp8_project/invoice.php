<?php
session_start();
require('fpdf184/fpdf.php');
include_once 'classes/Database.php';
include_once 'classes/Product.php';
include_once 'classes/Order.php';

// Redirect to login if not logged in
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
    // Page header
    function Header()
    {
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Title
        $this->Cell(0, 10, 'Invoice', 0, 1, 'C');
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

    // Table with adaptive row height
    function FancyTable($header, $data)
    {
        // Colors, line width and bold font for header
        $this->SetFillColor(169, 169, 169);
        $this->SetTextColor(0);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.3);
        $this->SetFont('Arial', 'B', 12);

        // Header
        $w = array(70, 30, 30, 40); // Column widths
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
        $this->Ln();

        // Color and font restoration for data
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 12);

        // Data
        $fill = false;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, $row[1], 'LR', 0, 'C', $fill);
            $this->Cell($w[2], 6, $row[2], 'LR', 0, 'R', $fill);
            $this->Cell($w[3], 6, $row[3], 'LR', 0, 'R', $fill);
            $this->Ln();
            $fill = !$fill; // Alternate row color
        }

        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

// Fetch user details from the database
$database = new Database();
$db = $database->getConnection();

$userId = $_SESSION['user_id'];
$query = "SELECT username, email FROM users WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $userId);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$userName = $user['username'];
$userEmail = $user['email'];
$shippingAddress = $_SESSION['shipping_address'];

// Format the shipping address
$shippingAddressFormatted = sprintf(
    "%s\n%s\n%s %s\nCanada",
    $shippingAddress['street_address'],
    $shippingAddress['city'],
    $shippingAddress['state'],
    $shippingAddress['postal_code']
);


// Create new PDF instance
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// User details and current date
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, "Name: $userName", 0, 1);
$pdf->Cell(0, 10, "Email: $userEmail", 0, 1);
$pdf->Cell(0, 10, "Shipping Address:", 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(0, 10, $shippingAddressFormatted); // Use MultiCell to handle line breaks
$pdf->Cell(0, 10, "Date: " . date('Y-m-d'), 0, 1);
$pdf->Ln(10);

// Order details
$pdf->Cell(0, 10, 'Thank you for your purchase! Here are the details of your order:', 0, 1);
$pdf->Ln(10);

// Table header
$header = array('Product', 'Quantity', 'Price', 'Total');

// Table data
$data = [];
$totalPrice = 0;
$product = new Product($db);

foreach ($orderItems as $item) {
    $productDetails = $product->getProductById($item['product_id']);
    $itemTotal = $item['price'] * $item['quantity'];
    $totalPrice += $itemTotal;

    $data[] = array(
        htmlspecialchars($productDetails['name']),
        $item['quantity'],
        '$' . number_format($item['price'], 2),
        '$' . number_format($itemTotal, 2)
    );
}

// Draw table
$pdf->FancyTable($header, $data);

// Total price
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(100, 10, 'Total Price', 0, 0, 'R');
$pdf->Cell(40, 10, '$' . number_format($totalPrice, 2), 0, 1, 'R');
$pdf->Ln(20);

// Thank you message
$pdf->SetFont('Arial', 'I', 12);
$pdf->Cell(0, 10, 'Thank you for your business!', 0, 1, 'C');

$pdf->Output('I', 'invoice.pdf');

session_destroy();
