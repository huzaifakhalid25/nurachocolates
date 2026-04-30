<?php
session_start();
require_once 'common/config.php'; 

// Agar cart khali hai ya form direct open kiya hai toh wapas bhej do
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

// 1. DATABASE TABLES AUTO-CREATE (Agar nahi banay huay toh khud ban jayenge)
$conn->query("CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(255),
    address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$conn->query("CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    qty INT NOT NULL
)");

// 2. GET FORM DATA
$full_name = $conn->real_escape_string($_POST['full_name']);
$phone = $conn->real_escape_string($_POST['phone']);
$email = $conn->real_escape_string($_POST['email']);
$address = $conn->real_escape_string($_POST['address']);
$city = $conn->real_escape_string($_POST['city']);
$payment_method = $conn->real_escape_string($_POST['payment_method']);

// 3. CALCULATE TOTAL AMOUNT
$total_amount = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_amount += ($item['price'] * $item['qty']);
}

// 4. INSERT INTO ORDERS TABLE
$insert_order = "INSERT INTO orders (full_name, phone, email, address, city, total_amount, payment_method) 
                 VALUES ('$full_name', '$phone', '$email', '$address', '$city', '$total_amount', '$payment_method')";

if ($conn->query($insert_order) === TRUE) {
    $order_id = $conn->insert_id; // Naye banne wale order ki ID

    // 5. INSERT ITEMS INTO ORDER_ITEMS TABLE
    foreach ($_SESSION['cart'] as $id => $item) {
        $p_id = (int)$id;
        $p_name = $conn->real_escape_string($item['name']);
        $p_price = (float)$item['price'];
        $p_qty = (int)$item['qty'];

        $conn->query("INSERT INTO order_items (order_id, product_id, product_name, price, qty) 
                      VALUES ('$order_id', '$p_id', '$p_name', '$p_price', '$p_qty')");
    }

    // 6. CLEAR THE CART AFTER SUCCESSFUL ORDER
    unset($_SESSION['cart']);

    // 7. SHOW SUCCESS PAGE (Luxury Theme)
    include 'common/header.php';
    ?>
    
    <div class="min-h-screen bg-[#faf9f6] flex items-center justify-center pt-20 px-4">
        <div class="max-w-md w-full bg-white p-8 md:p-10 rounded-3xl shadow-xl text-center border border-gray-100">
            <div class="w-20 h-20 bg-[#c49a6c]/10 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-[#c49a6c]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h1 class="text-3xl brand-font font-bold text-gray-900 mb-2">Order Confirmed!</h1>
            <p class="text-gray-500 mb-6 text-sm">Thank you for choosing Nura Chocolates. Your luxury experience is on its way.</p>
            
            <div class="bg-gray-50 rounded-xl p-4 mb-8 text-left border border-gray-100">
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Order Number</p>
                <p class="font-bold text-gray-900 mb-3">#NURA-<?php echo 1000 + $order_id; ?></p>
                
                <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Amount</p>
                <p class="font-bold text-[#c49a6c]">Rs. <?php echo number_format($total_amount, 2); ?></p>
            </div>

            <a href="index.php" class="inline-block w-full bg-black text-[#c49a6c] py-4 rounded-full font-bold uppercase tracking-widest hover:bg-[#c49a6c] hover:text-black transition shadow-[0_10px_20px_rgba(0,0,0,0.1)]">
                Return to Home
            </a>
        </div>
    </div>

    <?php 
    include 'common/footer.php'; 
} else {
    // Agar koi error aa jaye toh
    echo "Error: " . $insert_order . "<br>" . $conn->error;
}
?>