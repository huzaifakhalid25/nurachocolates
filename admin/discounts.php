<?php 
require_once '../common/config.php'; 

// 1. Table Auto-Create (Agar pehle se nahi hai)
$conn->query("CREATE TABLE IF NOT EXISTS discounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    type VARCHAR(20) NOT NULL, 
    value DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// 2. Add Discount Logic
if(isset($_POST['add_discount'])) {
    $code = strtoupper(mysqli_real_escape_string($conn, $_POST['code']));
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $value = $_POST['value'];
    
    // Check if code already exists
    $check = $conn->query("SELECT * FROM discounts WHERE code = '$code'");
    if($check->num_rows == 0) {
        $conn->query("INSERT INTO discounts (code, type, value) VALUES ('$code', '$type', '$value')");
    }
    header("Location: discounts.php");
    exit();
}

// 3. Delete Discount Logic
if(isset($_GET['del_discount'])) {
    $id = intval($_GET['del_discount']);
    $conn->query("DELETE FROM discounts WHERE id = $id");
    header("Location: discounts.php");
    exit();
}

$active_tab = 'discounts';
include 'common/header.php'; 
?>

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-6">
    <h1 class="text-xl md:text-2xl font-bold text-[#202223]">Discounts</h1>
    <button onclick="document.getElementById('discountModal').classList.remove('hidden')" class="bg-[#008060] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#006e52] shadow-sm w-full sm:w-auto text-center">
        Create discount
    </button>
</div>

<div class="shopify-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-[#f4f6f8] text-gray-600 text-xs uppercase border-b border-[#e1e3e5]">
                    <th class="px-5 py-3 font-medium">Discount Code</th>
                    <th class="px-5 py-3 font-medium">Status</th>
                    <th class="px-5 py-3 font-medium">Type & Value</th>
                    <th class="px-5 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-200">
                <?php
                $res = $conn->query("SELECT * FROM discounts ORDER BY id DESC");
                if($res && $res->num_rows > 0):
                    while($d = $res->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-4 font-bold text-[#006fbb] tracking-widest text-base"><?php echo $d['code']; ?></td>
                        <td class="px-5 py-4">
                            <span class="bg-green-100 text-green-800 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase"><?php echo $d['status']; ?></span>
                        </td>
                        <td class="px-5 py-4 text-gray-600 font-medium">
                            <?php echo $d['value']; ?><?php echo $d['type'] == 'Percentage' ? '%' : ' Rs'; ?> off entire order
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="?del_discount=<?php echo $d['id']; ?>" onclick="return confirm('Permanently delete this promo code?')" class="text-red-500 hover:underline font-medium">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="4" class="p-8 text-center text-gray-500">No discount codes created yet. Start by creating your first promo code!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="discountModal" class="fixed inset-0 modal-backdrop z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-md rounded-xl shadow-2xl overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-bold text-[#202223]">Create discount code</h3>
            <button onclick="document.getElementById('discountModal').classList.add('hidden')" class="text-gray-400 hover:text-black transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-6">
            <form method="POST" id="discountForm" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-800 mb-1">Discount code</label>
                    <input type="text" name="code" placeholder="e.g. WELCOME20" class="w-full border border-gray-300 rounded-md p-2.5 text-sm uppercase focus:ring-1 focus:ring-[#008060] focus:border-[#008060] outline-none" required>
                    <p class="text-xs text-gray-500 mt-1">Customers will enter this discount code at checkout.</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-800 mb-1">Discount Type</label>
                        <select name="type" class="w-full border border-gray-300 rounded-md p-2.5 text-sm bg-white focus:ring-1 focus:ring-[#008060] outline-none">
                            <option value="Percentage">Percentage (%)</option>
                            <option value="Fixed">Fixed Amount (Rs)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-800 mb-1">Discount Value</label>
                        <input type="number" name="value" placeholder="e.g. 15" min="1" class="w-full border border-gray-300 rounded-md p-2.5 text-sm focus:ring-1 focus:ring-[#008060] outline-none" required>
                    </div>
                </div>
            </form>
        </div>

        <div class="px-6 py-4 border-t border-[#e1e3e5] bg-[#f4f6f8] flex justify-end gap-3">
            <button type="button" onclick="document.getElementById('discountModal').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm">Cancel</button>
            <button type="submit" form="discountForm" name="add_discount" class="bg-[#008060] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#006e52] shadow-sm">Save discount</button>
        </div>
    </div>
</div>

<?php include 'common/footer.php'; ?>