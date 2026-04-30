<?php require_once '../common/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live View - NURA Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f1f2f4; }
        .sidebar { background-color: #ebebeb; }
        .sidebar-item { padding: 6px 12px; border-radius: 6px; margin-bottom: 2px; color: #303236; display: flex; align-items: center; font-size: 14px; font-weight: 500; }
        .sidebar-item:hover { background-color: #e4e5e7; }
        .sidebar-item.active { background-color: #e4e5e7; font-weight: 600; color: #202223; }
        .sub-menu { padding-left: 32px; font-size: 13px; color: #5c5f62; display: flex; flex-direction: column; gap: 6px; margin-top: 4px; margin-bottom: 12px;}
        .sub-menu a:hover { color: #202223; text-decoration: underline; }
        .sub-menu a.active { color: #202223; font-weight: 600; }
        .shopify-card { background: white; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #e1e3e5; }
        
        /* Fake Globe Animation */
        @keyframes spin { 100% { background-position: 200px 0; } }
        .fake-globe {
            background-image: radial-gradient(circle at 30% 40%, #008060 15%, transparent 16%), 
                              radial-gradient(circle at 70% 60%, #008060 10%, transparent 11%), 
                              radial-gradient(circle at 40% 80%, #008060 20%, transparent 21%),
                              radial-gradient(circle at 80% 20%, #008060 12%, transparent 13%);
            background-size: 150px 150px;
            animation: spin 10s linear infinite;
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-[#202223]">

    <aside class="w-[240px] sidebar h-full flex flex-col border-r border-[#d2d5d8] hidden md:flex">
        <div class="px-4 py-4 flex items-center mb-2">
            <div class="w-8 h-8 bg-[#c49a6c] rounded flex items-center justify-center text-white font-bold text-xl mr-2">N</div>
            <span class="font-bold text-base">NURA Store</span>
        </div>
        <nav class="flex-1 px-3 py-2">
            <a href="index.php" class="sidebar-item">Home</a>
            <a href="orders.php" class="sidebar-item">Orders</a>
            <a href="products.php" class="sidebar-item">Products</a>
            <a href="customers.php" class="sidebar-item">Customers</a>
            <a href="analytics.php" class="sidebar-item active">Analytics</a>
            <div class="sub-menu">
                <a href="analytics.php">Reports</a>
                <a href="live_view.php" class="active">Live View</a>
            </div>
            <a href="themes.php" class="sidebar-item">Online Store</a>
        </nav>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-14 border-b border-[#d2d5d8] bg-white flex items-center justify-between px-6 z-10">
            <div class="font-semibold text-gray-700">Live View</div>
            <div class="w-8 h-8 rounded-full bg-[#c49a6c] text-white flex items-center justify-center font-bold text-sm">A</div>
        </header>

        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-6xl mx-auto space-y-4">
                <div class="flex justify-between items-end mb-2">
                    <h1 class="text-2xl font-bold text-[#202223]">Live View</h1>
                </div>

                <div class="shopify-card bg-[#f4f6f8] flex h-[600px] overflow-hidden relative shadow-sm border border-gray-200">
                    <div class="w-1/3 bg-white border-r border-gray-200 p-6 z-10 flex flex-col shadow-lg">
                        <div class="grid grid-cols-2 gap-4 mb-8">
                            <div>
                                <p class="text-xs text-gray-500 font-medium mb-1">Visitors right now</p>
                                <p class="text-3xl font-bold text-gray-900">23</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium mb-1">Total sales</p>
                                <p class="text-xl font-bold text-gray-900">PKR 0.00</p>
                            </div>
                        </div>
                        
                        <h3 class="text-sm font-bold border-b pb-2 mb-4">Customer behavior</h3>
                        <div class="flex justify-between text-sm mb-2 text-gray-600"><span>Active carts</span><span class="font-bold text-black">4</span></div>
                        <div class="flex justify-between text-sm mb-2 text-gray-600"><span>Checking out</span><span class="font-bold text-black">1</span></div>
                        <div class="flex justify-between text-sm mb-8 text-gray-600"><span>Purchased</span><span class="font-bold text-black">0</span></div>

                        <h3 class="text-sm font-bold border-b pb-2 mb-4">Sessions by location</h3>
                        <div class="flex justify-between text-sm mb-1"><span class="flex items-center gap-2"><span class="w-2 h-2 bg-blue-500 rounded-full"></span> Pakistan</span><span>21</span></div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mb-4"><div class="bg-blue-500 h-1.5 rounded-full" style="width: 90%"></div></div>
                    </div>
                    
                    <div class="flex-1 flex items-center justify-center relative bg-[#eef1f5]">
                        <div class="w-[450px] h-[450px] rounded-full bg-gradient-to-br from-blue-100 to-blue-200 relative shadow-[inset_-20px_-20px_50px_rgba(0,0,0,0.1)] overflow-hidden border border-blue-200">
                            <div class="absolute inset-0 opacity-30 fake-globe"></div>
                            <div class="absolute top-[45%] left-[65%] w-3 h-3 bg-blue-600 rounded-full border-2 border-white shadow-[0_0_15px_rgba(37,99,235,1)] animate-pulse"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</body>
</html>