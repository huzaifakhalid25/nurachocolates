<?php 
require_once '../common/config.php'; 

// ================== SAFE AUTO-CREATE COLUMNS ==================
$col_check = $conn->query("SHOW COLUMNS FROM products LIKE 'description'");
if($col_check && $col_check->num_rows === 0) {
    $conn->query("ALTER TABLE products ADD COLUMN description TEXT AFTER category");
}
$col_long = $conn->query("SHOW COLUMNS FROM products LIKE 'long_description'");
if($col_long && $col_long->num_rows === 0) {
    $conn->query("ALTER TABLE products ADD COLUMN long_description TEXT AFTER description");
}
$col_extra = $conn->query("SHOW COLUMNS FROM products LIKE 'extra_images'");
if($col_extra && $col_extra->num_rows === 0) {
    $conn->query("ALTER TABLE products ADD COLUMN extra_images TEXT AFTER image_url");
}
// Naye discount columns
$col_disc = $conn->query("SHOW COLUMNS FROM products LIKE 'b2_disc'");
if($col_disc && $col_disc->num_rows === 0) {
    $conn->query("ALTER TABLE products ADD COLUMN b2_disc INT DEFAULT 5 AFTER price");
    $conn->query("ALTER TABLE products ADD COLUMN b3_disc INT DEFAULT 7 AFTER b2_disc");
    $conn->query("ALTER TABLE products ADD COLUMN b4_disc INT DEFAULT 10 AFTER b3_disc");
}

// ================== 1. DELETE PRODUCT ==================
if(isset($_GET['delete_product'])) {
    $id = intval($_GET['delete_product']);
    $conn->query("DELETE FROM products WHERE id = $id");
    header("Location: products.php");
    exit();
}

// ================== MULTIPLE UPLOAD LOGIC ==================
function uploadMultiple($file_array) {
    $paths = [];
    if(isset($file_array) && !empty($file_array['name'][0])) {
        $total = count($file_array['name']);
        for($i = 0; $i < $total; $i++) {
            if($file_array['error'][$i] == 0) {
                $ext = pathinfo($file_array['name'][$i], PATHINFO_EXTENSION);
                if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'])) {
                    $upload_dir = '../assets/uploads/';
                    if(!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
                    $new_name = uniqid('gallery_') . '.' . $ext;
                    if(move_uploaded_file($file_array['tmp_name'][$i], $upload_dir . $new_name)) {
                        $paths[] = 'assets/uploads/' . $new_name;
                    }
                }
            }
        }
    }
    return $paths;
}

// ================== 2. ADD PRODUCT ==================
if(isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $old_price = !empty($_POST['old_price']) ? floatval($_POST['old_price']) : "NULL";
    
    // Naye Discounts
    $b2 = intval($_POST['b2_disc']);
    $b3 = intval($_POST['b3_disc']);
    $b4 = intval($_POST['b4_disc']);

    $category = $_POST['category'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $long_description = mysqli_real_escape_string($conn, $_POST['long_description']);
    $is_sale = isset($_POST['is_sale']) ? 1 : 0;
    
    $img_path = "";
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_name = uniqid('main_') . '.' . $ext;
        if(move_uploaded_file($_FILES['image']['tmp_name'], '../assets/uploads/' . $new_name)) {
            $img_path = 'assets/uploads/' . $new_name;
        }
    }

    $gallery_paths = uploadMultiple($_FILES['gallery']);
    $gallery_json = json_encode($gallery_paths);

    $sql = "INSERT INTO products (name, price, old_price, b2_disc, b3_disc, b4_disc, category, description, long_description, image_url, extra_images, is_sale) 
            VALUES ('$name', '$price', $old_price, $b2, $b3, $b4, '$category', '$description', '$long_description', '$img_path', '$gallery_json', '$is_sale')";
    $conn->query($sql);
    header("Location: products.php");
    exit();
}

// ================== 3. EDIT PRODUCT ==================
if(isset($_POST['edit_product'])) {
    $id = intval($_POST['edit_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $old_price = !empty($_POST['old_price']) ? floatval($_POST['old_price']) : "NULL";
    
    $b2 = intval($_POST['b2_disc']);
    $b3 = intval($_POST['b3_disc']);
    $b4 = intval($_POST['b4_disc']);

    $category = $_POST['category'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $long_description = mysqli_real_escape_string($conn, $_POST['long_description']);
    $is_sale = isset($_POST['is_sale']) ? 1 : 0;
    
    $img_query = ""; 
    
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $new_name = uniqid('main_') . '.' . $ext;
        if(move_uploaded_file($_FILES['image']['tmp_name'], '../assets/uploads/' . $new_name)) {
            $img_path = 'assets/uploads/' . $new_name;
            $img_query .= ", image_url='$img_path'";
        }
    }

    if(isset($_FILES['gallery']) && !empty($_FILES['gallery']['name'][0])) {
        $gallery_paths = uploadMultiple($_FILES['gallery']);
        $gallery_json = json_encode($gallery_paths);
        $img_query .= ", extra_images='$gallery_json'";
    }

    $sql = "UPDATE products SET name='$name', price='$price', old_price=$old_price, b2_disc=$b2, b3_disc=$b3, b4_disc=$b4, category='$category', description='$description', long_description='$long_description', is_sale='$is_sale' $img_query WHERE id=$id";
    $conn->query($sql);
    header("Location: products.php");
    exit();
}

$active_tab = 'products';
include 'common/header.php'; 
?>

<div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4 mb-4">
    <h1 class="text-xl md:text-2xl font-bold text-[#202223]">Products</h1>
    <button onclick="openAddModal()" class="bg-[#008060] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#006e52] transition shadow-sm w-full sm:w-auto">
        Add product
    </button>
</div>

<div class="shopify-card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-[#f4f6f8] text-gray-600 text-xs uppercase border-b border-[#e1e3e5]">
                    <th class="px-4 py-3 font-medium">Product</th>
                    <th class="px-4 py-3 font-medium">Category</th>
                    <th class="px-4 py-3 font-medium">Price</th>
                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-200">
                <?php
                $res = $conn->query("SELECT * FROM products ORDER BY id DESC");
                if($res && $res->num_rows > 0):
                    while($p = $res->fetch_assoc()): 
                        $img_src = (strpos($p['image_url'], 'http') === 0) ? $p['image_url'] : '../' . $p['image_url'];
                        $js_name = addslashes($p['name']);
                        $js_old_price = $p['old_price'] ? $p['old_price'] : '';
                        $b2 = $p['b2_disc'] ?? 5;
                        $b3 = $p['b3_disc'] ?? 7;
                        $b4 = $p['b4_disc'] ?? 10;
                ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 flex items-center gap-3">
                        <div class="w-10 h-10 border border-gray-200 rounded flex items-center justify-center bg-white overflow-hidden flex-shrink-0">
                            <img src="<?php echo $img_src; ?>" class="w-full h-full object-cover">
                        </div>
                        <span class="font-semibold text-[#006fbb] line-clamp-1"><?php echo stripslashes($p['name']); ?></span>
                    </td>
                    <td class="px-4 py-3 text-gray-500"><?php echo $p['category']; ?></td>
                    <td class="px-4 py-3 font-medium">Rs. <?php echo number_format($p['price'], 2); ?></td>
                    <td class="px-4 py-3 text-right space-x-3">
                        <div id="desc_<?php echo $p['id']; ?>" class="hidden"><?php echo htmlspecialchars($p['description'] ?? ''); ?></div>
                        <div id="long_desc_<?php echo $p['id']; ?>" class="hidden"><?php echo htmlspecialchars($p['long_description'] ?? ''); ?></div>
                        
                        <button onclick="openEditModal(<?php echo $p['id']; ?>, '<?php echo $js_name; ?>', <?php echo $p['price']; ?>, '<?php echo $js_old_price; ?>', <?php echo $b2; ?>, <?php echo $b3; ?>, <?php echo $b4; ?>, '<?php echo $p['category']; ?>', <?php echo $p['is_sale']; ?>)" class="text-gray-500 hover:text-black font-medium">Edit</button>
                        <a href="?delete_product=<?php echo $p['id']; ?>" onclick="return confirm('Delete this product?')" class="text-red-500 hover:underline font-medium">Delete</a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                    <tr><td colspan="4" class="p-8 text-center text-gray-500">No products found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="productModal" class="fixed inset-0 bg-black/50 z-[100] flex items-center justify-center hidden p-4 backdrop-blur-sm">
    <div class="bg-white w-full max-w-xl rounded-xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="px-6 py-4 border-b border-[#e1e3e5] bg-[#f4f6f8] flex justify-between items-center flex-shrink-0">
            <h3 class="text-lg font-bold text-[#202223]" id="modalTitle">Add product</h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-700 text-2xl leading-none">&times;</button>
        </div>
        <div class="p-6 overflow-y-auto flex-1 custom-scrollbar">
            <form method="POST" enctype="multipart/form-data" id="productForm" class="space-y-5">
                <input type="hidden" name="edit_id" id="edit_id" value="">

                <div>
                    <label class="block text-sm font-medium text-gray-800 mb-1">Product Title</label>
                    <input type="text" name="name" id="p_name" class="w-full border border-gray-300 rounded-md p-2.5 text-sm focus:ring-1 focus:ring-[#008060] outline-none" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-800 mb-1">Price (Buy 1)</label>
                        <input type="number" name="price" id="p_price" class="w-full border border-gray-300 rounded-md p-2.5 text-sm focus:ring-1 focus:ring-[#008060] outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-800 mb-1">Compare at price</label>
                        <input type="number" name="old_price" id="p_old" class="w-full border border-gray-300 rounded-md p-2.5 text-sm focus:ring-1 focus:ring-[#008060] outline-none">
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-bold text-gray-800 mb-3">Bundle Discounts (%)</h4>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Buy 2 Off %</label>
                            <input type="number" name="b2_disc" id="p_b2" value="5" class="w-full border border-gray-300 rounded-md p-2 text-sm outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Buy 3 Off %</label>
                            <input type="number" name="b3_disc" id="p_b3" value="7" class="w-full border border-gray-300 rounded-md p-2 text-sm outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Buy 4 Off %</label>
                            <input type="number" name="b4_disc" id="p_b4" value="10" class="w-full border border-gray-300 rounded-md p-2 text-sm outline-none">
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 mb-1">Short Description (Front Page)</label>
                    <p class="text-[10px] text-gray-500 mb-1">Max 2-3 lines for grid display.</p>
                    <textarea name="description" id="p_desc" rows="2" class="w-full border border-gray-300 rounded-md p-2.5 text-sm focus:ring-1 focus:ring-[#008060] outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 mb-1">Detailed Description (Product Page)</label>
                    <p class="text-[10px] text-gray-500 mb-1">Press Enter for new lines. Used for full details.</p>
                    <textarea name="long_description" id="p_long_desc" rows="5" class="w-full border border-gray-300 rounded-md p-2.5 text-sm focus:ring-1 focus:ring-[#008060] outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-800 mb-1">Category</label>
                    <select name="category" id="p_cat" class="w-full border border-gray-300 rounded-md p-2.5 text-sm focus:ring-1 focus:ring-[#008060] outline-none">
                        <option value="Artisan">Artisan</option>
                        <option value="Signature">Signature</option>
                        <option value="Limited Edition">Limited Edition</option>
                    </select>
                </div>
                
                <div class="mt-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="is_sale" id="p_sale" class="rounded border-gray-300 text-[#008060] focus:ring-[#008060] w-4 h-4">
                        <span class="ml-2 text-sm text-gray-800">Show "Sale" badge</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <label class="block text-sm font-medium text-gray-800 mb-2">1. Main Thumbnail (Required)</label>
                    <input type="file" name="image" id="main-file" class="w-full text-sm border border-gray-300 p-2 rounded-md bg-white mb-4" accept="image/*">
                    
                    <label class="block text-sm font-medium text-gray-800 mb-2">2. Additional Gallery Images</label>
                    <input type="file" name="gallery[]" multiple id="gallery-files" class="w-full text-sm border border-gray-300 p-2 rounded-md bg-white" accept="image/*">
                </div>
            </form>
        </div>

        <div class="px-6 py-4 border-t border-[#e1e3e5] bg-[#f4f6f8] flex justify-end gap-3 flex-shrink-0">
            <button type="button" onclick="closeModal()" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm">Discard</button>
            <button type="submit" form="productForm" name="add_product" id="submitBtn" class="bg-[#008060] text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-[#006e52] shadow-sm">Save</button>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
</style>

<script>
    const modal = document.getElementById('productModal');
    const submitBtn = document.getElementById('submitBtn');
    const modalTitle = document.getElementById('modalTitle');
    const fileInput = document.getElementById('main-file');

    function openAddModal() {
        document.getElementById('edit_id').value = "";
        document.getElementById('productForm').reset();
        document.getElementById('p_desc').value = "";
        document.getElementById('p_long_desc').value = "";
        
        document.getElementById('p_b2').value = "5";
        document.getElementById('p_b3').value = "7";
        document.getElementById('p_b4').value = "10";

        modalTitle.innerText = "Add product";
        submitBtn.innerText = "Save";
        submitBtn.name = "add_product";
        fileInput.required = true;
        modal.classList.remove('hidden');
    }

    function openEditModal(id, name, price, old_price, b2, b3, b4, category, is_sale) {
        document.getElementById('edit_id').value = id;
        document.getElementById('p_name').value = name;
        document.getElementById('p_price').value = price;
        document.getElementById('p_old').value = old_price;
        
        document.getElementById('p_b2').value = b2;
        document.getElementById('p_b3').value = b3;
        document.getElementById('p_b4').value = b4;

        document.getElementById('p_cat').value = category;
        document.getElementById('p_sale').checked = is_sale === 1;
        
        // Fetching descriptions from hidden divs
        document.getElementById('p_desc').value = document.getElementById('desc_' + id).innerText;
        document.getElementById('p_long_desc').value = document.getElementById('long_desc_' + id).innerText;

        modalTitle.innerText = "Edit product";
        submitBtn.innerText = "Save changes";
        submitBtn.name = "edit_product"; 
        fileInput.required = false;
        modal.classList.remove('hidden');
    }

    function closeModal() { modal.classList.add('hidden'); }
</script>

<?php include 'common/footer.php'; ?>