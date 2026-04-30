<?php require_once '../common/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings - NURA Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f2f4; }
        .sidebar { background-color: #ebebeb; }
        .sidebar-item { padding: 6px 12px; border-radius: 6px; margin-bottom: 2px; color: #303236; display: flex; align-items: center; font-size: 14px; font-weight: 500; }
        .sidebar-item:hover { background-color: #e4e5e7; }
        .sidebar-item.active { background-color: #e4e5e7; font-weight: 600; color: #202223; }
        .shopify-card { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #e1e3e5; }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-[#202223]">

    <aside class="w-[240px] sidebar h-full flex flex-col border-r border-[#d2d5d8] hidden md:flex">
        <div class="px-4 py-4 flex items-center mb-2">
            <div class="w-8 h-8 bg-[#c49a6c] rounded flex items-center justify-center text-white font-bold text-xl mr-2">N</div>
            <span class="font-bold text-base">NURA Store</span>
        </div>
        <nav class="flex-1 px-3 py-2 overflow-y-auto">
            <a href="index.php" class="sidebar-item">Home</a>
            <a href="orders.php" class="sidebar-item">Orders</a>
            <a href="products.php" class="sidebar-item">Products</a>
            <a href="customers.php" class="sidebar-item">Customers</a>
        </nav>
        <div class="px-3 pb-4 border-t border-[#d2d5d8] pt-2">
            <a href="settings.php" class="sidebar-item active">
                <svg class="w-[18px] h-[18px] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Settings
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-[#f4f6f8]">
        <header class="h-14 border-b border-[#d2d5d8] bg-white flex items-center justify-between px-6 z-10">
            <div class="font-semibold text-gray-700">Settings</div>
            <a href="index.php" class="text-sm font-medium text-gray-500 hover:text-black">Close</a>
        </header>

        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-4xl mx-auto flex gap-8">
                <div class="w-64 flex flex-col gap-1 text-sm font-medium text-gray-600">
                    <a href="#" class="px-3 py-2 bg-gray-200 text-black rounded-md">Store details</a>
                    <a href="#" class="px-3 py-2 hover:bg-gray-200 rounded-md">Plan</a>
                    <a href="#" class="px-3 py-2 hover:bg-gray-200 rounded-md">Billing</a>
                    <a href="#" class="px-3 py-2 hover:bg-gray-200 rounded-md">Users and permissions</a>
                    <a href="#" class="px-3 py-2 hover:bg-gray-200 rounded-md">Payments</a>
                    <a href="#" class="px-3 py-2 hover:bg-gray-200 rounded-md">Checkout</a>
                </div>

                <div class="flex-1 space-y-6">
                    <div>
                        <h2 class="text-xl font-bold mb-1">Store details</h2>
                        <p class="text-sm text-gray-500 mb-4">View and update your store details and contact information.</p>
                    </div>

                    <div class="shopify-card p-6">
                        <h3 class="font-bold mb-4">Basic information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Store name</label>
                                <input type="text" value="NURA Store" class="w-full border border-gray-300 rounded-md p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#008060]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Store industry</label>
                                <select class="w-full border border-gray-300 rounded-md p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#008060]">
                                    <option>Food & Drink</option>
                                    <option>Retail</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="shopify-card p-6">
                        <h3 class="font-bold mb-4">Contact information</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Sender email</label>
                                <input type="email" value="admin@nurachocolates.com" class="w-full border border-gray-300 rounded-md p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#008060]">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Store phone</label>
                                <input type="text" value="+92 300 1234567" class="w-full border border-gray-300 rounded-md p-2 text-sm focus:outline-none focus:ring-1 focus:ring-[#008060]">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button class="bg-[#008060] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#006e52]">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>