<?php 
require_once '../common/config.php'; 

// Check if orders table exists
$check_orders = $conn->query("SHOW TABLES LIKE 'orders'");
$has_orders_table = ($check_orders && $check_orders->num_rows > 0);

// Set Active Tab and Include Header
$active_tab = 'orders';
include 'common/header.php'; 
?>

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-4">
    <h1 class="text-xl md:text-2xl font-bold text-[#202223]">Orders</h1>
    <div class="space-x-2 flex">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium shadow-sm hover:bg-gray-50 flex-1 sm:flex-none text-center">Export</button>
        <button class="bg-[#008060] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#006e52] shadow-sm flex-1 sm:flex-none text-center">Create order</button>
    </div>
</div>

<div class="shopify-card overflow-hidden">
    <div class="p-3 md:p-4 border-b border-gray-200 flex gap-4 bg-gray-50 overflow-x-auto whitespace-nowrap custom-scrollbar">
        <button class="text-sm font-semibold border-b-2 border-black pb-1">All</button>
        <button class="text-sm text-gray-500 hover:text-black">Unfulfilled</button>
        <button class="text-sm text-gray-500 hover:text-black">Unpaid</button>
        <button class="text-sm text-gray-500 hover:text-black">Archived</button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
                <tr class="bg-[#f4f6f8] text-gray-600 text-xs uppercase border-b border-[#e1e3e5]">
                    <th class="px-4 md:px-5 py-3 font-medium">Order</th>
                    <th class="px-4 md:px-5 py-3 font-medium">Date</th>
                    <th class="px-4 md:px-5 py-3 font-medium">Customer</th>
                    <th class="px-4 md:px-5 py-3 font-medium">Payment</th>
                    <th class="px-4 md:px-5 py-3 font-medium">Fulfillment</th>
                    <th class="px-4 md:px-5 py-3 font-medium text-right">Total</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-200">
                <?php 
                if($has_orders_table) {
                    $res = $conn->query("SELECT * FROM orders ORDER BY id DESC");
                    if($res && $res->num_rows > 0) {
                        while($o = $res->fetch_assoc()) {
                            $date = isset($o['created_at']) ? date('M d, Y', strtotime($o['created_at'])) : 'Today';
                            echo "<tr class='hover:bg-gray-50 cursor-pointer transition-colors'>
                                <td class='px-4 md:px-5 py-4 font-semibold text-gray-900'>#NURA-".(1000+$o['id'])."</td>
                                <td class='px-4 md:px-5 py-4 text-gray-500'>".$date."</td>
                                <td class='px-4 md:px-5 py-4 font-medium text-gray-900'>".$o['full_name']."</td>
                                <td class='px-4 md:px-5 py-4'><span class='bg-yellow-100 text-yellow-800 text-[10px] font-bold px-2 py-1 rounded-full'>".$o['payment_method']."</span></td>
                                <td class='px-4 md:px-5 py-4'><span class='bg-gray-200 text-gray-800 text-[10px] font-bold px-2 py-1 rounded-full'>Unfulfilled</span></td>
                                <td class='px-4 md:px-5 py-4 text-right font-medium'>Rs. ".number_format($o['total_amount'],2)."</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='p-8 text-center text-gray-500'>No orders found. Waiting for your first sale!</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='p-8 text-center text-gray-500'>Order system ready. Waiting for the first order to be placed.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'common/footer.php'; ?>