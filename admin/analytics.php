<?php 
require_once '../common/config.php'; 

// Fetch Revenue
$total_revenue = 0; $total_orders = 0;
$check_orders = $conn->query("SHOW TABLES LIKE 'orders'");
if($check_orders && $check_orders->num_rows > 0) {
    $total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders")->fetch_assoc()['total'] ?? 0;
    $total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'] ?? 0;
}

// Chart Data (Last 10 Days)
$chart_labels = []; $chart_data = [];
for ($i = 9; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('M d', strtotime($date)); 
    if ($check_orders && $check_orders->num_rows > 0) {
        $day_total = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE DATE(created_at) = '$date'")->fetch_assoc()['total'];
        $chart_data[] = $day_total ? $day_total : 0;
    } else { $chart_data[] = 0; }
}

$active_tab = 'analytics';
include 'common/header.php'; 
?>

<div class="flex justify-between items-end mb-4">
    <h1 class="text-2xl font-bold text-[#202223]">Analytics</h1>
    <select class="border border-gray-300 rounded-md px-3 py-1.5 bg-white text-sm"><option>Last 10 Days</option></select>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="md:col-span-2 shopify-card p-6">
        <h3 class="text-sm font-medium text-gray-600 mb-2">Total Sales (PKR)</h3>
        <div class="text-3xl font-bold mb-6">Rs. <?php echo number_format($total_revenue, 2); ?></div>
        <div class="h-64 w-full">
            <canvas id="analyticsChart"></canvas>
        </div>
    </div>
    
    <div class="space-y-6">
        <div class="shopify-card p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2 border-b pb-2 border-dashed">Total Orders</h3>
            <div class="text-3xl font-bold text-[#008060]"><?php echo $total_orders; ?></div>
        </div>
        <div class="shopify-card p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-2 border-b pb-2 border-dashed">Top Selling Location</h3>
            <div class="text-xl font-bold">Karachi</div>
            <p class="text-xs text-gray-500 mt-1">Based on recent order history</p>
        </div>
    </div>
</div>

<script>
    const ctxA = document.getElementById('analyticsChart').getContext('2d');
    new Chart(ctxA, {
        type: 'bar', // Using Bar chart for Shopify Analytics look
        data: {
            labels: <?php echo json_encode($chart_labels); ?>,
            datasets: [{ label: 'Sales', data: <?php echo json_encode($chart_data); ?>, backgroundColor: '#008060', borderRadius: 4 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
</script>

<?php include 'common/footer.php'; ?>