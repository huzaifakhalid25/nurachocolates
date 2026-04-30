<?php 
require_once '../common/config.php'; 

// DELETE SUBSCRIBER LOGIC
if(isset($_GET['delete_sub'])) {
    $id = intval($_GET['delete_sub']);
    $conn->query("DELETE FROM subscribers WHERE id = $id");
    header("Location: customers.php");
    exit();
}

// Set Active Tab and Include Header
$active_tab = 'customers';
include 'common/header.php'; 
?>

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-4">
    <h1 class="text-xl md:text-2xl font-bold text-[#202223]">Customers</h1>
    <div class="space-x-2 flex">
        <button class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium shadow-sm hover:bg-gray-50 flex-1 sm:flex-none text-center">Export</button>
        <button class="bg-[#008060] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#006e52] shadow-sm flex-1 sm:flex-none text-center">Add customer</button>
    </div>
</div>

<div class="shopify-card overflow-hidden mb-8">
    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center">
        <h2 class="text-sm font-semibold text-gray-800">Registered Users</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-[#f4f6f8] text-gray-600 text-xs uppercase border-b border-[#e1e3e5]">
                    <th class="px-4 md:px-5 py-3 font-medium">Customer name</th>
                    <th class="px-4 md:px-5 py-3 font-medium">Email</th>
                    <th class="px-4 md:px-5 py-3 font-medium">Location</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-200">
                <?php
                $res = $conn->query("SELECT * FROM users ORDER BY id DESC");
                if($res && $res->num_rows > 0):
                    while($u = $res->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 cursor-pointer transition-colors">
                            <td class="px-4 md:px-5 py-4 font-semibold text-[#006fbb] hover:underline"><?php echo $u['full_name']; ?></td>
                            <td class="px-4 md:px-5 py-4 text-gray-600"><?php echo $u['email']; ?></td>
                            <td class="px-4 md:px-5 py-4 text-gray-500">Pakistan</td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="3" class="p-8 text-center text-gray-500">No registered customers found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="shopify-card overflow-hidden">
    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center">
        <h2 class="text-sm font-semibold text-gray-800">Email Subscribers</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-[#f4f6f8] text-gray-600 text-xs uppercase border-b border-[#e1e3e5]">
                    <th class="px-4 md:px-5 py-3 font-medium">Email Address</th>
                    <th class="px-4 md:px-5 py-3 font-medium">Status</th>
                    <th class="px-4 md:px-5 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-200">
                <?php
                $res = $conn->query("SELECT * FROM subscribers ORDER BY id DESC");
                if($res && $res->num_rows > 0):
                    while($s = $res->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 md:px-5 py-4 font-medium text-gray-900"><?php echo $s['email']; ?></td>
                            <td class="px-4 md:px-5 py-4">
                                <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">Subscribed</span>
                            </td>
                            <td class="px-4 md:px-5 py-4 text-right">
                                <a href="?delete_sub=<?php echo $s['id']; ?>" onclick="return confirm('Remove this subscriber?')" class="text-red-600 hover:underline font-medium text-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr><td colspan="3" class="p-8 text-center text-gray-500">No subscribers found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'common/footer.php'; ?>