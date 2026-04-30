<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (isset($conn)) {
    $conn->query("ALTER TABLE users ADD COLUMN IF NOT EXISTS role VARCHAR(20) DEFAULT 'customer'");
    $conn->query("UPDATE users SET role = 'admin' WHERE email = 'huzaifakhalid3058835@gmail.com'");

    if (isset($_SESSION['user_id'])) {
        $uid = $_SESSION['user_id'];
        $role_check = $conn->query("SELECT role FROM users WHERE id = '$uid'");
        if ($role_check && $role_check->num_rows > 0) {
            $_SESSION['role'] = $role_check->fetch_assoc()['role'];
        }
    }
}

$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) { $cart_count += intval($item['quantity'] ?? $item['qty'] ?? 0); }
}

$is_admin_area = (strpos($_SERVER['REQUEST_URI'], '/admin') !== false);
$base_path = $is_admin_area ? '../' : '';
$current_url = "https://" . $_SERVER['HTTP_HOST'] . explode('?', $_SERVER['REQUEST_URI'], 2)[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NURA | Artisan Handcrafted Luxury Chocolates</title>
    <meta name="description" content="Discover NURA's premium handcrafted artisan chocolates in Pakistan. Exquisite, ethically sourced ingredients blended to perfection for luxury gifting.">
    
    <link rel="canonical" href="<?php echo $current_url; ?>">
    <link rel="icon" type="image/png" href="<?php echo $base_path; ?>favicon.png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Lato', sans-serif; background-color: #faf9f6; overflow-x: hidden; }
        .brand-font { font-family: 'Playfair Display', serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .drawer-overlay { transition: opacity 0.3s ease; display: none; }
        .drawer-overlay.active { display: block; opacity: 1; }
        .drawer-menu { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .sidebar-hidden { transform: translateX(-100%); }
        .cart-hidden { transform: translateX(100%); }
        .body-lock { overflow: hidden !important; height: 100vh !important; }
        .top-bar-text { font-size: 13px !important; }
        #cart-count { font-size: 12px !important; width: 18px; height: 18px; line-height: 18px; }
    </style>
</head>
<body class="text-gray-900">

<div id="sidebar-overlay" class="fixed inset-0 bg-black/60 z-[9999] opacity-0 backdrop-blur-sm drawer-overlay" onclick="toggleSidebar()">
    <div id="sidebar-menu" class="bg-white w-[85%] max-w-[320px] h-full shadow-2xl p-6 relative flex flex-col drawer-menu sidebar-hidden overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center mb-6 border-b border-gray-300 pb-4">
            <img src="<?php echo $base_path; ?>favicon.png" alt="NURA" width="120" height="40" class="h-6 object-contain">
            <button onclick="toggleSidebar()" class="text-4xl text-gray-800 hover:text-black transition" aria-label="Close menu">&times;</button>
        </div>
        <nav class="space-y-6">
            <div>
                <p class="text-xs text-gray-800 font-bold uppercase tracking-widest mb-3">Shop</p>
                <div class="space-y-4">
                    <a href="<?php echo $base_path; ?>index" class="block text-lg font-medium text-gray-900 hover:text-[#8a6340]">Home</a>
                    <a href="<?php echo $base_path; ?>products" class="block text-lg font-medium text-gray-900 hover:text-[#8a6340]">All Products</a>
                </div>
            </div>
            <hr class="border-gray-300">
            <div>
                <p class="text-xs text-gray-800 font-bold uppercase tracking-widest mb-3">Information</p>
                <div class="space-y-4">
                    <a href="<?php echo $base_path; ?>about" class="block text-lg font-medium text-gray-900 hover:text-[#8a6340]">About Us</a>
                    <a href="<?php echo $base_path; ?>contact" class="block text-lg font-medium text-gray-900 hover:text-[#8a6340]">Contact Us</a>
                    <a href="<?php echo $base_path; ?>privacy-policy" class="block text-lg font-medium text-gray-900 hover:text-[#8a6340]">Privacy Policy</a>
                    <a href="<?php echo $base_path; ?>terms" class="block text-lg font-medium text-gray-900 hover:text-[#8a6340]">Terms of Service</a>
                </div>
            </div>
            <hr class="border-gray-300">
            <div>
                <p class="text-xs text-gray-800 font-bold uppercase tracking-widest mb-3">Account</p>
                <div class="space-y-4">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo $base_path; ?>profile" class="block text-lg font-medium text-[#8a6340]">My Account</a>
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="<?php echo $base_path; ?>admin/index.php" class="block text-lg font-bold text-gray-900">Dashboard</a>
                        <?php endif; ?>
                        <a href="<?php echo $base_path; ?>logout" class="block text-lg font-medium text-red-700">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo $base_path; ?>login" class="block text-lg font-medium text-gray-800 hover:text-[#8a6340]">Login</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </div>
</div>

<div id="cart-overlay" class="fixed inset-0 bg-black/60 z-[9999] opacity-0 backdrop-blur-sm drawer-overlay" onclick="toggleCart()">
    <div id="cart-menu" class="bg-white w-[90%] max-w-[400px] h-full shadow-2xl p-6 absolute right-0 flex flex-col drawer-menu cart-hidden" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center mb-6 border-b border-gray-300 pb-4">
            <h2 class="text-2xl brand-font font-bold">Your Cart</h2>
            <button onclick="toggleCart()" class="text-4xl text-gray-800 hover:text-black transition" aria-label="Close cart">&times;</button>
        </div>
        <div id="cart-items-container" class="flex-1 overflow-y-auto no-scrollbar">
            <p class="text-gray-700 text-center mt-10 text-sm">Your cart is currently empty.</p>
        </div>
        <div class="border-t border-gray-300 pt-4 mt-4">
            <div class="flex justify-between font-bold mb-4 text-gray-900">
                <span>Total:</span>
                <span id="cart-drawer-total">PKR 0</span>
            </div>
            <a href="<?php echo $base_path; ?>checkout" class="block text-center w-full bg-black text-white py-4 rounded-full text-sm font-bold uppercase tracking-widest hover:bg-[#8a6340] transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-black">Checkout</a>
        </div>
    </div>
</div>

<div class="bg-white text-gray-900 text-center py-2 top-bar-text font-medium tracking-wide border-b border-gray-300">
    Welcome to our store
</div>

<header class="bg-white text-gray-900 relative z-50">
    <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between border-b border-gray-300 md:border-none">
        <div class="flex items-center gap-4 w-1/3">
            <button onclick="toggleSidebar()" class="text-gray-900 hover:text-black focus:outline-none focus:ring-2 focus:ring-black rounded p-1" aria-label="Open Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
        <div class="w-1/3 flex justify-center">
            <a href="<?php echo $base_path; ?>index" class="block" aria-label="Home">
                <img src="<?php echo $base_path; ?>favicon.png" alt="NURA Logo" width="120" height="40" class="h-8 md:h-10 object-contain">
            </a>
        </div>
        <div class="flex items-center justify-end gap-4 w-1/3">
            
            <div class="relative user-menu-container">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <button onclick="toggleProfileDropdown()" class="text-gray-900 hover:text-[#8a6340] transition flex items-center focus:outline-none focus:ring-2 focus:ring-black rounded p-1" aria-label="User Account Menu">
                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </button>
                    <div id="profileDropdown" class="hidden absolute right-0 mt-3 w-56 bg-[#1a1a1a] rounded-lg shadow-2xl py-2 z-[1000] border border-gray-700 text-left">
                        <div class="px-4 py-3 border-b border-gray-700">
                            <p class="text-xs text-gray-300 uppercase tracking-widest">Signed in as</p>
                            <p class="text-sm font-bold text-[#e8c99a] truncate mt-1"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></p>
                        </div>
                        <a href="<?php echo $base_path; ?>profile" class="block px-4 py-3 text-sm text-white hover:bg-gray-800">My Account</a>
                        <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="<?php echo $base_path; ?>admin/index.php" class="block px-4 py-3 text-sm text-[#e8c99a] font-bold hover:bg-gray-800">Dashboard</a>
                        <?php endif; ?>
                        <div class="border-t border-gray-700 my-1"></div>
                        <a href="<?php echo $base_path; ?>logout" class="block px-4 py-3 text-sm text-red-400 hover:bg-gray-800">Logout</a>
                    </div>
                <?php else: ?>
                    <a href="<?php echo $base_path; ?>login" class="text-gray-900 hover:text-[#8a6340] transition focus:ring-2 focus:ring-black rounded p-1" aria-label="Login">
                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </a>
                <?php endif; ?>
            </div>

            <button onclick="toggleCart()" class="relative text-gray-900 hover:text-[#8a6340] transition focus:outline-none focus:ring-2 focus:ring-black rounded p-1" aria-label="Open Cart">
                <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                <span id="cart-count" class="absolute -top-1 -right-2 bg-black text-white rounded-full flex items-center justify-center font-bold" aria-hidden="true">
                    <?php echo $cart_count; ?>
                </span>
            </button>
        </div>
    </div>
</header>

<script>
    function toggleSidebar() {
        const overlay = document.getElementById('sidebar-overlay');
        const menu = document.getElementById('sidebar-menu');
        document.body.classList.toggle('body-lock');
        if (overlay.style.display === "block") {
            overlay.classList.remove('active'); menu.classList.add('sidebar-hidden');
            setTimeout(() => overlay.style.display = "none", 300);
        } else {
            overlay.style.display = "block";
            setTimeout(() => { overlay.classList.add('active'); menu.classList.remove('sidebar-hidden'); }, 10);
        }
    }

    function toggleCart() {
        const overlay = document.getElementById('cart-overlay');
        const menu = document.getElementById('cart-menu');
        document.body.classList.toggle('body-lock');
        if (overlay.style.display === "block") {
            overlay.classList.remove('active'); menu.classList.add('cart-hidden');
            setTimeout(() => overlay.style.display = "none", 300);
        } else {
            overlay.style.display = "block";
            setTimeout(() => { overlay.classList.add('active'); menu.classList.remove('cart-hidden'); }, 10);
        }
    }

    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        if(dropdown) dropdown.classList.toggle('hidden');
    }

    window.addEventListener('click', function(e) {
        const container = document.querySelector('.user-menu-container');
        if (container && !container.contains(e.target)) {
            const dropdown = document.getElementById('profileDropdown');
            if(dropdown && !dropdown.classList.contains('hidden')) dropdown.classList.add('hidden');
        }
    });

    const cartActionUrl = '<?php echo $base_path; ?>cart_action.php';

    window.loadCart = function() {
        fetch(cartActionUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=get'
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                const container = document.getElementById('cart-items-container');
                if(container) container.innerHTML = data.html;
                
                const cartBadge = document.getElementById('cart-count');
                if(cartBadge) cartBadge.innerText = data.total_items;

                const drawerTotal = document.getElementById('cart-drawer-total');
                if(drawerTotal) drawerTotal.innerText = 'Rs. ' + data.total_price;
            }
        });
    };

    window.updateCartQty = function(cart_key, type) {
        fetch(cartActionUrl, { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
            body: `action=update&cart_key=${cart_key}&type=${type}` 
        })
        .then(res => res.json())
        .then(data => { if(data.status === 'success') window.loadCart(); });
    };

    window.removeFromCart = function(cart_key) {
        fetch(cartActionUrl, { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, 
            body: `action=remove&cart_key=${cart_key}` 
        })
        .then(res => res.json())
        .then(data => { if(data.status === 'success') window.loadCart(); });
    };

    document.addEventListener('DOMContentLoaded', () => {
        window.loadCart();
    });
</script>