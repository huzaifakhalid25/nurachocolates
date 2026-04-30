<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$active_tab = isset($active_tab) ? $active_tab : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>NURA Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f8; color: #202223; }
        
        /* Sidebar Styling (Shopify Style) */
        .sidebar { background-color: #ebebeb; transition: transform 0.3s ease-in-out; }
        .sidebar-item { padding: 8px 12px; border-radius: 6px; margin-bottom: 2px; color: #303236; display: flex; align-items: center; font-size: 14px; font-weight: 500; transition: background 0.2s; }
        .sidebar-item:hover { background-color: #e4e5e7; }
        .sidebar-item.active { background-color: #e4e5e7; font-weight: 600; color: #202223; }
        .sidebar-icon { width: 20px; height: 20px; color: #5c5f62; margin-right: 12px; }
        .sidebar-item.active .sidebar-icon { color: #202223; }
        
        /* Cards & Modals */
        .shopify-card { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #e1e3e5; }
        .modal-backdrop { backdrop-filter: blur(4px); background-color: rgba(0, 0, 0, 0.5); }
        
        /* Mobile Overlay */
        .overlay { display: none; background: rgba(0,0,0,0.5); position: fixed; inset: 0; z-index: 40; }
        .overlay.active { display: block; }
        
        @media (max-width: 768px) {
            .sidebar { position: fixed; z-index: 50; transform: translateX(-100%); width: 260px; }
            .sidebar.open { transform: translateX(0); }
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <div class="overlay" id="mobile-overlay" onclick="toggleSidebar()"></div>

    <aside class="w-[240px] sidebar h-full flex flex-col border-r border-[#d2d5d8] flex-shrink-0" id="sidebar">
        <div class="px-4 py-4 flex items-center justify-between mb-4">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-[#008060] rounded flex items-center justify-center text-white font-bold text-xl mr-2">N</div>
                <span class="font-bold text-base">NURA Store</span>
            </div>
            <button class="md:hidden text-gray-500 hover:text-black" onclick="toggleSidebar()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <nav class="flex-1 px-3 py-2 overflow-y-auto space-y-1">
            <a href="index.php" class="sidebar-item <?php echo $active_tab == 'dashboard' ? 'active' : ''; ?>">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Home
            </a>
            
            <a href="orders.php" class="sidebar-item <?php echo $active_tab == 'orders' ? 'active' : ''; ?>">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                Orders
            </a>

            <a href="products.php" class="sidebar-item <?php echo $active_tab == 'products' ? 'active' : ''; ?>">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                Products
            </a>

            <a href="customers.php" class="sidebar-item <?php echo $active_tab == 'customers' ? 'active' : ''; ?>">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Customers
            </a>

            <a href="content.php" class="sidebar-item <?php echo $active_tab == 'content' ? 'active' : ''; ?>">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Content
            </a>

            <a href="analytics.php" class="sidebar-item <?php echo $active_tab == 'analytics' ? 'active' : ''; ?>">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Analytics
            </a>

            <a href="discounts.php" class="sidebar-item <?php echo $active_tab == 'discounts' ? 'active' : ''; ?>">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                Discounts
            </a>
        </nav>
        
        <div class="px-3 pb-4 pt-4 border-t border-[#d2d5d8]">
            <a href="../index.php" target="_blank" class="sidebar-item text-gray-600 hover:text-black">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                View Live Store
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden w-full bg-[#f4f6f8]">
        
        <header class="h-14 min-h-[56px] border-b border-[#d2d5d8] bg-white flex items-center justify-between px-4 md:px-6 z-10 flex-shrink-0">
            <div class="flex items-center">
                <button class="md:hidden mr-4 text-gray-600 hover:text-black" onclick="toggleSidebar()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <div class="font-semibold text-gray-700 capitalize hidden md:block">
                    <?php echo str_replace('_', ' ', $active_tab); ?>
                </div>
            </div>
            
            <div class="flex items-center gap-3 relative user-menu-container">
                <div onclick="toggleProfileDropdown()" class="w-8 h-8 rounded-full bg-[#008060] text-white flex items-center justify-center font-bold text-sm cursor-pointer shadow-sm hover:opacity-90 transition">
                    <?php echo isset($_SESSION['user_name']) ? strtoupper(substr($_SESSION['user_name'], 0, 1)) : 'A'; ?>
                </div>

                <div id="profileDropdown" class="hidden absolute right-0 top-10 mt-2 w-56 bg-white rounded-lg shadow-xl py-2 z-[1000] border border-gray-200">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-xs text-gray-500 uppercase tracking-widest">Signed in as Admin</p>
                        <p class="text-sm font-bold text-gray-900 truncate mt-1"><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Administrator'); ?></p>
                    </div>
                    
                    <a href="../profile.php" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition">My Account</a>
                    
                    <a href="../index.php" class="block px-4 py-3 text-sm text-[#008060] font-bold hover:bg-gray-50 transition">🌍 Go to My Website</a>
                    
                    <div class="border-t border-gray-100 my-1"></div>
                    <a href="../logout.php" class="block px-4 py-3 text-sm text-red-500 hover:bg-gray-50 transition">Logout</a>
                </div>
            </div>
            </header>

        <div class="flex-1 overflow-y-auto p-4 md:p-8 w-full">
            <div class="max-w-6xl mx-auto space-y-6">

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-overlay');
        sidebar.classList.toggle('open');
        overlay.classList.toggle('active');
    }

    function toggleProfileDropdown() {
        const dropdown = document.getElementById('profileDropdown');
        dropdown.classList.toggle('hidden');
    }

    window.addEventListener('click', function(e) {
        const container = document.querySelector('.user-menu-container');
        if (container && !container.contains(e.target)) {
            const dropdown = document.getElementById('profileDropdown');
            if (dropdown && !dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        }
    });
</script>