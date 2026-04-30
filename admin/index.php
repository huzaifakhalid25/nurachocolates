<?php 
require_once '../common/config.php'; 

// ==========================================
// 1. AUTO-CREATE TABLES & COLUMNS (Data Save Fix)
// ==========================================
$conn->query("CREATE TABLE IF NOT EXISTS site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100),
    setting_value TEXT
)");

// Safely add new columns to products table if they don't exist
$check_col = $conn->query("SHOW COLUMNS FROM products LIKE 'is_featured'");
if($check_col && $check_col->num_rows == 0) {
    $conn->query("ALTER TABLE products ADD COLUMN is_featured TINYINT(1) DEFAULT 0");
}
$check_col2 = $conn->query("SHOW COLUMNS FROM products LIKE 'in_dial'");
if($check_col2 && $check_col2->num_rows == 0) {
    $conn->query("ALTER TABLE products ADD COLUMN in_dial TINYINT(1) DEFAULT 1");
}

// ==========================================
// 2. DASHBOARD STATS LOGIC
// ==========================================
$total_products = 0;
$check_products = $conn->query("SHOW TABLES LIKE 'products'");
if ($check_products && $check_products->num_rows > 0) {
    $total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];
}

$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'] ?? 0;
$total_orders = 0; 
$total_revenue = 0;

$check_orders = $conn->query("SHOW TABLES LIKE 'orders'");
$has_orders_table = ($check_orders && $check_orders->num_rows > 0);

if ($has_orders_table) {
    $total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
    $total_revenue = $conn->query("SELECT SUM(total_amount) as total FROM orders")->fetch_assoc()['total'] ?? 0;
}

$chart_labels = [];
$chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chart_labels[] = date('M d', strtotime($date)); 
    
    if ($has_orders_table) {
        $day_total = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE DATE(created_at) = '$date'")->fetch_assoc()['total'];
        $chart_data[] = $day_total ? $day_total : 0;
    } else {
        $chart_data[] = 0;
    }
}

// ==========================================
// 3. FRONT-PAGE DISPLAY LOGIC (TOGGLES)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_display'])) {
    $p_id = (int)$_POST['product_id'];
    $type = $conn->real_escape_string($_POST['display_type']); 
    $status = (int)$_POST['status']; 
    
    if ($type === 'featured') {
        $conn->query("UPDATE products SET is_featured = $status WHERE id = $p_id");
    } elseif ($type === 'dial') {
        $conn->query("UPDATE products SET in_dial = $status WHERE id = $p_id");
    }
    
    header("Location: index.php");
    exit;
}

// ==========================================
// 4. CONTENT MANAGER LOGIC (TEXT EDIT)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_content'])) {
    foreach ($_POST['content'] as $key => $value) {
        $safe_key = $conn->real_escape_string($key);
        $safe_value = $conn->real_escape_string($value);
        
        $check = $conn->query("SELECT id FROM site_settings WHERE setting_key = '$safe_key'");
        
        if ($check && $check->num_rows > 0) {
            $conn->query("UPDATE site_settings SET setting_value = '$safe_value' WHERE setting_key = '$safe_key'");
        } else {
            $conn->query("INSERT INTO site_settings (setting_key, setting_value) VALUES ('$safe_key', '$safe_value')");
        }
    }
    echo "<script>alert('Website content updated successfully!'); window.location.href='index.php';</script>";
    exit;
}

// Fetch Existing Settings
$site_content = [];
$content_query = $conn->query("SELECT * FROM site_settings");
if ($content_query) {
    while ($row = $content_query->fetch_assoc()) {
        $site_content[$row['setting_key']] = $row['setting_value'];
    }
}

$def = function($key, $default_text) use ($site_content) {
    return htmlspecialchars($site_content[$key] ?? $default_text);
};

// ==========================================
// HEADER INCLUDE
// ==========================================
$active_tab = 'dashboard';
include 'common/header.php'; 
?>

<div class="mb-4">
    <h1 class="text-xl md:text-2xl font-bold text-[#202223] mb-4">Store Overview</h1>
</div>

<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-5 border-l-4 border-l-[#008060]">
        <div class="text-xs md:text-sm text-gray-600 mb-1 md:mb-2 font-medium">Total Sales</div>
        <div class="text-lg md:text-2xl font-bold text-[#008060]">Rs. <?php echo number_format($total_revenue, 2); ?></div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-5">
        <div class="text-xs md:text-sm text-gray-600 mb-1 md:mb-2 font-medium">Total Orders</div>
        <div class="text-lg md:text-2xl font-bold"><?php echo $total_orders; ?></div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-5">
        <div class="text-xs md:text-sm text-gray-600 mb-1 md:mb-2 font-medium">Total Products</div>
        <div class="text-lg md:text-2xl font-bold"><?php echo $total_products; ?></div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 md:p-5">
        <div class="text-xs md:text-sm text-gray-600 mb-1 md:mb-2 font-medium">Customers</div>
        <div class="text-lg md:text-2xl font-bold"><?php echo $total_users; ?></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900">Featured Selection (Lals Style)</h3>
                <p class="text-xs text-gray-500">Choose exactly 4 products to display in the big grid.</p>
            </div>
            <a href="add_product.php" class="bg-[#008060] text-white px-3 py-1 rounded text-xs font-bold hover:bg-[#006e52] transition shadow-sm flex items-center gap-2">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add
            </a>
        </div>
        <div class="p-4">
            <?php
            $featured_count = $conn->query("SELECT COUNT(*) as c FROM products WHERE is_featured = 1")->fetch_assoc()['c'];
            if ($featured_count > 4) {
                echo "<p class='text-red-500 text-xs mb-3 font-bold'>Warning: You have more than 4 products selected. Only the latest 4 will show.</p>";
            } elseif ($featured_count < 4) {
                echo "<p class='text-orange-500 text-xs mb-3 font-bold'>Note: You have selected $featured_count/4 products.</p>";
            }
            ?>
            <div class="overflow-y-auto max-h-[400px] custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <tbody class="divide-y divide-gray-200 text-sm">
                        <?php
                        $all_prods = $conn->query("SELECT id, name, image_url, is_featured FROM products ORDER BY id DESC");
                        if($all_prods && $all_prods->num_rows > 0) {
                            while($p = $all_prods->fetch_assoc()) {
                                $img_path = str_replace('../', '', $p['image_url']);
                                $is_feat = $p['is_featured'];
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="p-2 flex items-center gap-3">
                                        <img src="../<?php echo htmlspecialchars($img_path); ?>" class="w-10 h-10 rounded object-cover border">
                                        <span class="font-medium text-gray-900 truncate max-w-[150px]"><?php echo htmlspecialchars($p['name']); ?></span>
                                    </td>
                                    <td class="p-2 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            
                                            <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="text-[#008060] font-bold text-xs border border-[#008060] px-3 py-1 rounded hover:bg-[#008060] hover:text-white transition">Edit</a>
                                            
                                            <form method="POST" action="">
                                                <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                                <input type="hidden" name="display_type" value="featured">
                                                <?php if($is_feat): ?>
                                                    <input type="hidden" name="status" value="0">
                                                    <button type="submit" name="toggle_display" class="bg-green-100 text-green-800 border border-green-300 text-[10px] font-bold px-3 py-1 rounded-full hover:bg-red-100 hover:text-red-800 transition">Remove</button>
                                                <?php else: ?>
                                                    <input type="hidden" name="status" value="1">
                                                    <button type="submit" name="toggle_display" class="bg-gray-100 text-gray-600 border border-gray-300 text-[10px] font-bold px-3 py-1 rounded-full hover:bg-green-100 hover:text-green-800 transition">Add to Grid</button>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='2' class='py-4 text-center text-gray-500'>No products found. Add products first.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <div>
                <h3 class="font-bold text-gray-900">3D Interactive Dial</h3>
                <p class="text-xs text-gray-500">Select up to 10 products to spin in the 3D Dial.</p>
            </div>
             <a href="add_product.php" class="bg-[#008060] text-white px-3 py-1 rounded text-xs font-bold hover:bg-[#006e52] transition shadow-sm flex items-center gap-2">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add
            </a>
        </div>
        <div class="p-4">
             <?php
            $dial_count = $conn->query("SELECT COUNT(*) as c FROM products WHERE in_dial = 1")->fetch_assoc()['c'];
            if ($dial_count > 10) {
                echo "<p class='text-red-500 text-xs mb-3 font-bold'>Warning: You have selected $dial_count products. Only the latest 10 will spin.</p>";
            } else {
                echo "<p class='text-blue-500 text-xs mb-3 font-bold'>Selected: $dial_count/10 products for the dial.</p>";
            }
            ?>
            <div class="overflow-y-auto max-h-[400px] custom-scrollbar">
                <table class="w-full text-left border-collapse">
                    <tbody class="divide-y divide-gray-200 text-sm">
                        <?php
                        $all_prods->data_seek(0); // Reset result pointer
                        if($all_prods && $all_prods->num_rows > 0) {
                            while($p = $all_prods->fetch_assoc()) {
                                $img_path = str_replace('../', '', $p['image_url']);
                                $in_dial = isset($p['in_dial']) ? $p['in_dial'] : 1; 
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="p-2 flex items-center gap-3">
                                        <img src="../<?php echo htmlspecialchars($img_path); ?>" class="w-10 h-10 rounded object-cover border">
                                        <span class="font-medium text-gray-900 truncate max-w-[150px]"><?php echo htmlspecialchars($p['name']); ?></span>
                                    </td>
                                    <td class="p-2 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            
                                            <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="text-[#008060] font-bold text-xs border border-[#008060] px-3 py-1 rounded hover:bg-[#008060] hover:text-white transition">Edit</a>
                                            
                                            <form method="POST" action="">
                                                <input type="hidden" name="product_id" value="<?php echo $p['id']; ?>">
                                                <input type="hidden" name="display_type" value="dial">
                                                <?php if($in_dial): ?>
                                                    <input type="hidden" name="status" value="0">
                                                    <button type="submit" name="toggle_display" class="bg-blue-100 text-blue-800 border border-blue-300 text-[10px] font-bold px-3 py-1 rounded-full hover:bg-red-100 hover:text-red-800 transition">Remove</button>
                                                <?php else: ?>
                                                    <input type="hidden" name="status" value="1">
                                                    <button type="submit" name="toggle_display" class="bg-gray-100 text-gray-600 border border-gray-300 text-[10px] font-bold px-3 py-1 rounded-full hover:bg-blue-100 hover:text-blue-800 transition">Add to Dial</button>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='2' class='py-4 text-center text-gray-500'>No products found. Add products first.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<div class="mt-8 pt-6 border-t border-gray-200">
    <h2 class="text-xl md:text-2xl font-bold text-[#202223] mb-4">Website Content Manager (Edit Text)</h2>
    <form method="POST" action="">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 border-t-4 border-t-[#008060]">
                <h3 class="font-bold text-lg mb-4">1. Hero Section</h3>
                <label class="block text-sm font-medium mb-1">Top Tagline</label>
                <input type="text" name="content[hero_tag]" value="<?php echo $def('hero_tag', 'Handcrafted in Pakistan · Est. 2024'); ?>" class="w-full border border-gray-300 rounded p-2 mb-3 outline-none focus:border-[#008060]">

                <label class="block text-sm font-medium mb-1">Main Title</label>
                <input type="text" name="content[hero_title]" value="<?php echo $def('hero_title', 'Taste <em>The Luxury</em>'); ?>" class="w-full border border-gray-300 rounded p-2 mb-1 outline-none focus:border-[#008060]">
                <p class="text-xs text-gray-400 mb-3 -mt-1">Use &lt;em&gt;text&lt;/em&gt; to make text gold.</p>

                <label class="block text-sm font-medium mb-1">Subtitle</label>
                <input type="text" name="content[hero_sub]" value="<?php echo $def('hero_sub', 'Single-origin cacao · Artisan crafted · Luxury gifting'); ?>" class="w-full border border-gray-300 rounded p-2 mb-3 outline-none focus:border-[#008060]">

                <label class="block text-sm font-medium mb-1">Button Text</label>
                <input type="text" name="content[hero_cta]" value="<?php echo $def('hero_cta', 'Explore Collection →'); ?>" class="w-full border border-gray-300 rounded p-2 mb-3 outline-none focus:border-[#008060]">
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 border-t-4 border-t-[#008060]">
                <h3 class="font-bold text-lg mb-4">2. Marquee & Products</h3>
                <label class="block text-sm font-medium mb-1">Marquee Scrolling Text (Comma separated)</label>
                <textarea name="content[marquee_text]" rows="2" class="w-full border border-gray-300 rounded p-2 mb-3 outline-none focus:border-[#008060]"><?php echo $def('marquee_text', 'Free delivery above PKR 2000, Premium artisan treats, Gift wrapping available'); ?></textarea>
                
                <label class="block text-sm font-medium mb-1">Products Label (Featured)</label>
                <input type="text" name="content[prod_label]" value="<?php echo $def('prod_label', 'Featured Selection'); ?>" class="w-full border border-gray-300 rounded p-2 mb-3 outline-none focus:border-[#008060]">

                <label class="block text-sm font-medium mb-1">Products Title</label>
                <input type="text" name="content[prod_title]" value="<?php echo $def('prod_title', 'The Signature Collection'); ?>" class="w-full border border-gray-300 rounded p-2 mb-3 outline-none focus:border-[#008060]">
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 border-t-4 border-t-[#008060]">
                <h3 class="font-bold text-lg mb-4">3. Banner & Dial Text</h3>
                <label class="block text-sm font-medium mb-1">Banner Background Giant Faded Text</label>
                <input type="text" name="content[banner_bg_text]" value="<?php echo $def('banner_bg_text', 'NURA'); ?>" class="w-full border border-gray-300 rounded p-2 mb-3 outline-none focus:border-[#008060]">

                <label class="block text-sm font-medium mb-1">Banner Heading</label>
                <input type="text" name="content[banner_h]" value="<?php echo $def('banner_h', 'Crafted for<br><em>Moments</em>'); ?>" class="w-full border border-gray-300 rounded p-2 mb-3 outline-none focus:border-[#008060]">

                <label class="block text-sm font-medium mb-1">Banner Description</label>
                <textarea name="content[banner_p]" rows="2" class="w-full border border-gray-300 rounded p-2 mb-4 outline-none focus:border-[#008060]"><?php echo $def('banner_p', 'Every piece of chocolate tells a story — from the cacao farm to your hands.'); ?></textarea>

                <hr class="mb-4">

                <label class="block text-sm font-medium mb-1">Dial Top Label</label>
                <input type="text" name="content[dial_label]" value="<?php echo $def('dial_label', 'Interactive Experience'); ?>" class="w-full border border-gray-300 rounded p-2 mb-3 outline-none focus:border-[#008060]">

                <label class="block text-sm font-medium mb-1">Dial Main Heading</label>
                <input type="text" name="content[dial_heading]" value="<?php echo $def('dial_heading', 'The Signature <em>Dial</em>'); ?>" class="w-full border border-gray-300 rounded p-2 mb-3 outline-none focus:border-[#008060]">
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 border-t-4 border-t-[#008060]">
                <h3 class="font-bold text-lg mb-4">4. Testimonials</h3>
                
                <div class="mb-3 p-2 bg-gray-50 border rounded-lg">
                    <label class="block text-xs font-bold text-gray-700">Review 1</label>
                    <input type="text" name="content[rev1_txt]" value="<?php echo $def('rev1_txt', 'The most exquisite chocolate I have tasted.'); ?>" class="w-full border rounded p-1 mb-1 text-sm outline-none focus:border-[#008060]">
                    <input type="text" name="content[rev1_name]" value="<?php echo $def('rev1_name', 'Aisha K.'); ?>" class="w-full border rounded p-1 text-sm outline-none focus:border-[#008060]">
                </div>

                <div class="mb-3 p-2 bg-gray-50 border rounded-lg">
                    <label class="block text-xs font-bold text-gray-700">Review 2</label>
                    <input type="text" name="content[rev2_txt]" value="<?php echo $def('rev2_txt', 'Gifted these to my clients, amazing quality.'); ?>" class="w-full border rounded p-1 mb-1 text-sm outline-none focus:border-[#008060]">
                    <input type="text" name="content[rev2_name]" value="<?php echo $def('rev2_name', 'Hamza R.'); ?>" class="w-full border rounded p-1 text-sm outline-none focus:border-[#008060]">
                </div>

                <div class="p-2 bg-gray-50 border rounded-lg">
                    <label class="block text-xs font-bold text-gray-700">Review 3</label>
                    <input type="text" name="content[rev3_txt]" value="<?php echo $def('rev3_txt', 'Absolutely divine. Will order again.'); ?>" class="w-full border rounded p-1 mb-1 text-sm outline-none focus:border-[#008060]">
                    <input type="text" name="content[rev3_name]" value="<?php echo $def('rev3_name', 'Sara M.'); ?>" class="w-full border rounded p-1 text-sm outline-none focus:border-[#008060]">
                </div>
            </div>

        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" name="update_content" class="bg-[#008060] text-white px-8 py-3 rounded font-bold hover:bg-[#006e52] transition-colors shadow-lg">
                Save All Content
            </button>
        </div>
    </form>
</div>

<script>
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Sales (PKR)',
                    data: <?php echo json_encode($chart_data); ?>,
                    borderColor: '#008060',
                    backgroundColor: 'rgba(0, 128, 96, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #ccc; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #aaa; }
</style>

<?php 
// Yeh divs Header mein open thay, unhe yahan band karna lazmi tha
echo '</div></div></main></body></html>'; 
?>