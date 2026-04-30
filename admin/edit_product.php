<?php 
require_once '../common/config.php'; 

// ID check
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

// SAFELY CREATE COLUMNS
$conn->query("SHOW COLUMNS FROM products LIKE 'long_description'")->num_rows == 0 ? $conn->query("ALTER TABLE products ADD COLUMN long_description TEXT AFTER description") : null;
$conn->query("SHOW COLUMNS FROM products LIKE 'extra_images'")->num_rows == 0 ? $conn->query("ALTER TABLE products ADD COLUMN extra_images TEXT AFTER image_url") : null;

// Pehle product ka data uthao taake purani images track ho sakein
$res = $conn->query("SELECT * FROM products WHERE id = $id");
$p = $res->fetch_assoc();
if (!$p) { die("Product not found."); }

// Save Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $price = $_POST['price'];
    $category = $conn->real_escape_string($_POST['category']);
    
    $description = $conn->real_escape_string($_POST['description']);
    $long_description = isset($_POST['long_description']) ? $conn->real_escape_string($_POST['long_description']) : '';
    
    $is_sale = isset($_POST['is_sale']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $in_dial = isset($_POST['in_dial']) ? 1 : 0;

    $img_sql = "";
    
    // Update Main Image
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $file_name = time() . '_main_' . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $file_name)) {
            $db_path = "uploads/" . $file_name;
            $img_sql .= ", image_url = '$db_path'";
        }
    }
    
    // ===============================================
    // UPDATE EXPLICIT GALLERY IMAGES (Slot 1 to 4)
    // ===============================================
    $existing_gallery = json_decode($p['extra_images'], true) ?: [];
    // Ensure we have 4 slots mapped
    $existing_gallery = array_pad($existing_gallery, 4, "");

    for ($i = 0; $i < 4; $i++) {
        // Agar user ne remove par check lagaya hai
        if (isset($_POST['remove_gal'][$i]) && $_POST['remove_gal'][$i] == '1') {
            $existing_gallery[$i] = "";
        }
        
        // Agar naya image is slot mein upload hua hai
        if (isset($_FILES['gallery_file']['name'][$i]) && $_FILES['gallery_file']['error'][$i] == 0) {
            $ext = strtolower(pathinfo($_FILES['gallery_file']['name'][$i], PATHINFO_EXTENSION));
            if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $upload_dir = '../uploads/';
                if(!is_dir($upload_dir)) { mkdir($upload_dir, 0777, true); }
                $file_name = time() . '_gal_' . $i . '_' . uniqid() . '.' . $ext;
                if(move_uploaded_file($_FILES['gallery_file']['tmp_name'][$i], $upload_dir . $file_name)) {
                    $existing_gallery[$i] = 'uploads/' . $file_name;
                }
            }
        }
    }

    // Khali slots hata kar clean JSON array save karein
    $final_gallery = array_values(array_filter($existing_gallery, function($v) { return $v !== ""; }));
    $gallery_json = json_encode($final_gallery);
    $img_sql .= ", extra_images = '$gallery_json'";

    $sql = "UPDATE products SET 
            name = '$name', 
            price = '$price', 
            category = '$category', 
            description = '$description', 
            long_description = '$long_description',
            is_sale = $is_sale,
            is_featured = $is_featured,
            in_dial = $in_dial 
            $img_sql 
            WHERE id = $id";
    
    if ($conn->query($sql)) {
        echo "<script>alert('Product Updated Successfully!'); window.location.href='index.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

$active_tab = 'products';
include 'common/header.php'; 
?>

<div class="max-w-4xl mx-auto pb-32">
    <div class="flex items-center gap-4 mb-6">
        <a href="index.php" class="text-gray-500 hover:text-black transition">← Back to Dashboard</a>
        <h1 class="text-2xl font-bold text-[#202223]">Edit: <?php echo htmlspecialchars($p['name']); ?></h1>
    </div>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($p['name']); ?>" required class="w-full border border-gray-300 rounded p-2 outline-none focus:border-[#008060] focus:ring-1 focus:ring-[#008060]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price (PKR)</label>
                    <input type="number" name="price" value="<?php echo $p['price']; ?>" required class="w-full border border-gray-300 rounded p-2 outline-none focus:border-[#008060] focus:ring-1 focus:ring-[#008060]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" class="w-full border border-gray-300 rounded p-2 outline-none focus:border-[#008060] focus:ring-1 focus:ring-[#008060]">
                        <option value="Artisan" <?php if($p['category']=='Artisan') echo 'selected'; ?>>Artisan</option>
                        <option value="Signature" <?php if($p['category']=='Signature') echo 'selected'; ?>>Signature</option>
                    </select>
                </div>
                <div class="space-y-3 mt-1">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="is_featured" <?php if(isset($p['is_featured']) && $p['is_featured']) echo 'checked'; ?> class="w-4 h-4 text-[#008060]"> Show in Featured (Lals Style)
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="in_dial" <?php if(!isset($p['in_dial']) || $p['in_dial']) echo 'checked'; ?> class="w-4 h-4 text-[#008060]"> Show in 3D Dial
                    </label>
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="is_sale" <?php if(isset($p['is_sale']) && $p['is_sale']) echo 'checked'; ?> class="w-4 h-4 text-[#008060]"> Show 'Sale' Tag
                    </label>
                </div>
            </div>
            
            <div class="mt-8 border-t border-gray-200 pt-6">
                <h3 class="font-bold text-gray-800 mb-4 text-lg">Descriptions</h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Index page description</label>
                    <textarea name="description" rows="6" class="w-full border border-gray-300 rounded p-2 outline-none focus:border-[#008060] focus:ring-1 focus:ring-[#008060]"><?php echo htmlspecialchars($p['description']); ?></textarea>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Detailed Description (For Product Page)</label>
                    <p class="text-[10px] text-gray-500 mb-2">Use this for full details, ingredients, etc. Line breaks (Enters) will be shown exactly as you type.</p>
                    <textarea name="long_description" rows="8" class="w-full border border-gray-300 rounded p-2 outline-none focus:border-[#008060] focus:ring-1 focus:ring-[#008060]"><?php echo htmlspecialchars($p['long_description'] ?? ''); ?></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="font-bold mb-4 text-gray-800 text-lg">Product Images</h3>
            
            <div class="space-y-4">
                
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <label class="block text-sm font-bold text-gray-700 mb-3">1. Main Image (Required)</label>
                    <div class="flex items-center gap-4">
                        <img src="../<?php echo str_replace('../', '', $p['image_url']); ?>" class="w-16 h-16 object-cover border border-gray-300 rounded shadow-sm bg-white flex-shrink-0">
                        <div class="flex-1">
                            <input type="file" name="image" class="w-full border border-gray-300 rounded p-1.5 text-sm bg-white" accept="image/*">
                            <p class="text-[10px] text-gray-500 mt-1">Leave empty to keep current main image.</p>
                        </div>
                    </div>
                </div>

                <hr class="border-gray-200 my-4">
                <h4 class="text-sm font-bold text-gray-800 mb-3">Additional Gallery Images</h4>

                <?php 
                $gallery = json_decode($p['extra_images'], true) ?: [];
                for($i=0; $i<4; $i++): 
                    $has_img = isset($gallery[$i]) && $gallery[$i] !== "";
                    $img_src = $has_img ? "../" . str_replace('../', '', $gallery[$i]) : "";
                ?>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-center mb-3">
                        <label class="block text-sm font-bold text-gray-700">Gallery Image <?php echo $i+1; ?></label>
                        <?php if($has_img): ?>
                        <label class="flex items-center gap-1 text-xs text-red-500 font-bold cursor-pointer hover:bg-red-50 px-2 py-1 rounded">
                            <input type="checkbox" name="remove_gal[<?php echo $i; ?>]" value="1" class="w-3 h-3 accent-red-500"> Remove Image
                        </label>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-4">
                        <?php if($has_img): ?>
                            <img src="<?php echo $img_src; ?>" class="w-16 h-16 object-cover border border-gray-300 rounded shadow-sm bg-white flex-shrink-0">
                        <?php else: ?>
                            <div class="w-16 h-16 border-2 border-dashed border-gray-300 rounded bg-gray-100 flex items-center justify-center text-gray-400 text-[10px] flex-shrink-0 font-bold">Empty</div>
                        <?php endif; ?>
                        
                        <div class="flex-1">
                            <input type="file" name="gallery_file[<?php echo $i; ?>]" class="w-full border border-gray-300 rounded p-1.5 text-sm bg-white" accept="image/*">
                            <p class="text-[10px] text-gray-500 mt-1"><?php echo $has_img ? 'Upload new file to replace.' : 'Upload file to add image.'; ?></p>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>

            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4">
            <a href="index.php" class="px-6 py-3 border border-gray-300 rounded font-bold text-gray-700 hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" name="update_product" class="px-8 py-3 bg-[#008060] text-white rounded font-bold hover:bg-[#006e52] shadow-lg transition">Save Changes</button>
        </div>
    </form>
</div>

<?php echo '</div></div></main></body></html>'; ?>