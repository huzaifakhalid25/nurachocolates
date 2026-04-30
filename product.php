<?php
session_start();
require_once 'common/config.php';

// ==========================================
// AUTO-CREATE SLUG COLUMN & UPDATE EXISTING
// ==========================================
$check_slug = $conn->query("SHOW COLUMNS FROM products LIKE 'slug'");
if($check_slug && $check_slug->num_rows == 0) {
    $conn->query("ALTER TABLE products ADD COLUMN slug VARCHAR(255) AFTER name");
    $res = $conn->query("SELECT id, name FROM products");
    while($row = $res->fetch_assoc()) {
        $new_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $row['name'])));
        $conn->query("UPDATE products SET slug = '$new_slug' WHERE id = ".$row['id']);
    }
}

// ==========================================
// FETCH PRODUCT BY SLUG OR ID
// ==========================================
if (isset($_GET['slug']) && !empty($_GET['slug'])) {
    $slug = $_GET['slug'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE slug = ?");
    $stmt->bind_param("s", $slug);
} elseif (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
} else {
    header("Location: index");
    exit;
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) die("Product not found!");

$product = $result->fetch_assoc();
$product_id = $product['id'];
$p_name = stripslashes($product['name']);
$p_price = floatval($product['price']); 

// Actual Price Logic
if (isset($product['actual_price']) && $product['actual_price'] > 0) {
    $actual_unit_price = floatval($product['actual_price']);
} elseif (isset($product['compare_price']) && $product['compare_price'] > 0) {
    $actual_unit_price = floatval($product['compare_price']);
} elseif (isset($product['old_price']) && $product['old_price'] > 0) {
    $actual_unit_price = floatval($product['old_price']);
} else {
    $actual_unit_price = $p_price * 1.25; 
}

// Long description fallback logic
if (isset($product['long_description']) && trim($product['long_description']) !== '') {
    $p_desc = stripslashes($product['long_description']);
} else {
    $p_desc = stripslashes($product['description'] ?? '');
}

$b2_disc = isset($product['b2_disc']) ? intval($product['b2_disc']) : 5;
$b3_disc = isset($product['b3_disc']) ? intval($product['b3_disc']) : 7;
$b4_disc = isset($product['b4_disc']) ? intval($product['b4_disc']) : 10;

function formatImg($img) {
    return (strpos($img, 'http') === 0) ? $img : $img;
}
$main_img = formatImg($product['image_url']);

// Fetch Gallery Images
$gallery_images = [];
if(!empty($product['extra_images'])) {
    $gallery_images = json_decode($product['extra_images'], true) ?: [];
}
// Combine Main Image + Gallery for the Slider
$all_images = array_merge([$product['image_url']], $gallery_images);

// Fix Base Path for URL Rewrite
$base_path = dirname($_SERVER['SCRIPT_NAME']);
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . ($base_path == '/' ? '' : $base_path) . "/";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($p_name); ?> | NURA</title>
    <base href="<?php echo $base_url; ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Montserrat', sans-serif; background-color: #fcfcfc; }
        .bundle-card { border: 1.2px solid #e5e7eb; border-radius: 12px; background: #fff; position: relative; }
        .bundle-radio:checked + .bundle-content { border-color: #000; background: #f9f9f9; border-width: 2px; }
        .radio-dot { width: 20px; height: 20px; border: 1px solid #d1d5db; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .bundle-radio:checked + .bundle-content .radio-dot::after { content: ''; width: 10px; height: 10px; background: #000; border-radius: 50%; }
        .sale-badge { background: #000; color: #fff; padding: 2px 14px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .popular-tag { position: absolute; top: -10px; right: 20px; background: #000; color: #fff; font-size: 10px; padding: 2px 10px; border-radius: 4px; font-weight: bold; z-index: 10; }
        
        .desc-text { word-wrap: break-word; overflow-wrap: break-word; word-break: break-word; hyphens: auto; width: 100%; }
        .desc-text p { margin-bottom: 1em; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        @media (min-width: 1024px) { .sticky-content { position: sticky; top: 20px; } }
    </style>
</head>
<body class="bg-white">

<?php include 'common/header.php'; ?>

<main class="max-w-7xl mx-auto px-4 md:px-8 py-6 md:py-12 overflow-hidden">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
        
        <div class="w-full">
            <div class="w-full aspect-square bg-gray-50 rounded-2xl overflow-hidden mb-4 border border-gray-100 shadow-sm">
                <img id="main-product-img" src="<?php echo $main_img; ?>" width="600" height="600" class="w-full h-full object-cover transition-opacity duration-300">
            </div>
            
            <?php if(count($all_images) > 1): ?>
            <div class="flex gap-3 overflow-x-auto pb-2 no-scrollbar">
                <?php foreach($all_images as $idx => $img_path): 
                    $thumb = formatImg($img_path);
                ?>
                <button onclick="changeImage('<?php echo $thumb; ?>', this)" class="thumb-btn flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 <?php echo $idx===0 ? 'border-black' : 'border-transparent'; ?> hover:border-gray-400 transition-all">
                    <img src="<?php echo $thumb; ?>" class="w-full h-full object-cover pointer-events-none" width="80" height="80">
                </button>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="sticky-content">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mb-1">NURA</p>
            <h1 class="text-3xl md:text-4xl font-bold mb-4 break-words" style="font-family: 'Playfair Display', serif;"><?php echo htmlspecialchars($p_name); ?></h1>
            
            <div class="flex items-center gap-3 mb-8">
                <span class="text-gray-400 line-through text-xs md:text-sm">Rs.<?php echo number_format($actual_unit_price, 0); ?> PKR</span>
                <span class="text-lg md:text-2xl font-bold">Rs.<?php echo number_format($p_price, 0); ?> PKR</span>
                <span class="sale-badge uppercase">Sale</span>
            </div>

            <div class="grid grid-cols-3 gap-2 py-4 border-y border-gray-100 mb-10 text-center">
                <div class="flex flex-col items-center">
                    <svg class="w-8 h-8 mb-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" stroke-width="1"/></svg>
                    <span class="text-[9px] font-bold uppercase text-gray-500 leading-tight">Loved by<br>Customers</span>
                </div>
                <div class="flex flex-col items-center">
                    <svg class="w-8 h-8 mb-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20.24 12.24a6 6 0 00-8.49-8.49L5 10.5V19h8.5z" stroke-width="1"/></svg>
                    <span class="text-[9px] font-bold uppercase text-gray-500 leading-tight">Premium<br>Ingredients</span>
                </div>
                <div class="flex flex-col items-center">
                    <svg class="w-8 h-8 mb-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674z" stroke-width="1"/></svg>
                    <span class="text-[9px] font-bold uppercase text-gray-500 leading-tight">Rich &<br>Indulgent Taste</span>
                </div>
            </div>

            <h2 class="font-bold text-lg mb-6">Buy more, save more</h2>

            <div class="space-y-4 mb-10">
                <label class="block relative cursor-pointer">
                    <input type="radio" name="bundle" value="1" class="sr-only bundle-radio" checked onchange="up(1, 0)">
                    <div class="bundle-content bundle-card p-5 flex justify-between items-center">
                        <div class="flex items-center gap-3"><div class="radio-dot"></div><span class="font-bold text-sm">Buy 1 <span class="bg-gray-400 text-white text-[9px] px-2 py-0.5 rounded ml-2 uppercase">Full price</span></span></div>
                        <span class="text-sm font-medium">Rs.<?php echo number_format($p_price, 0); ?></span>
                    </div>
                </label>

                <label class="block relative cursor-pointer">
                    <div class="popular-tag">Popular</div>
                    <input type="radio" name="bundle" value="2" class="sr-only bundle-radio" onchange="up(2, <?php echo $b2_disc; ?>)">
                    <div class="bundle-content bundle-card p-5 flex justify-between items-center">
                        <div class="flex items-center gap-3"><div class="radio-dot"></div><span class="font-bold text-sm">Buy 2 <span class="bg-gray-400 text-white text-[9px] px-2 py-0.5 rounded ml-2 uppercase">Save <?php echo $b2_disc; ?>%</span></span></div>
                        <div class="text-right">
                            <span class="block text-[10px] text-gray-400 line-through">Rs.<?php echo number_format($actual_unit_price * 2, 0); ?></span>
                            <span class="block font-bold text-sm">Rs.<?php echo number_format(($p_price * 2) * (1 - $b2_disc/100), 0); ?></span>
                        </div>
                    </div>
                </label>

                <label class="block relative cursor-pointer">
                    <input type="radio" name="bundle" value="3" class="sr-only bundle-radio" onchange="up(3, <?php echo $b3_disc; ?>)">
                    <div class="bundle-content bundle-card p-5 flex justify-between items-center">
                        <div class="flex items-center gap-3"><div class="radio-dot"></div><span class="font-bold text-sm">Buy 3 <span class="bg-gray-400 text-white text-[9px] px-2 py-0.5 rounded ml-2 uppercase">Save <?php echo $b3_disc; ?>%</span></span></div>
                        <div class="text-right">
                            <span class="block text-[10px] text-gray-400 line-through">Rs.<?php echo number_format($actual_unit_price * 3, 0); ?></span>
                            <span class="block font-bold text-sm">Rs.<?php echo number_format(($p_price * 3) * (1 - $b3_disc/100), 0); ?></span>
                        </div>
                    </div>
                </label>

                <label class="block relative cursor-pointer">
                    <input type="radio" name="bundle" value="4" class="sr-only bundle-radio" onchange="up(4, <?php echo $b4_disc; ?>)">
                    <div class="bundle-content bundle-card p-5">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3"><div class="radio-dot"></div><span class="font-bold text-sm">Buy 4+ <span class="bg-gray-400 text-white text-[9px] px-2 py-0.5 rounded ml-2 uppercase">Save <?php echo $b4_disc; ?>%</span></span></div>
                            <div class="text-right">
                                <span id="b4-old" class="block text-[10px] text-gray-400 line-through">Rs.<?php echo number_format($actual_unit_price * 4, 0); ?></span>
                                <span id="b4-new" class="block font-bold text-sm">Rs.<?php echo number_format(($p_price * 4) * (1 - $b4_disc/100), 0); ?></span>
                            </div>
                        </div>
                        <div id="qty-box" class="hidden justify-between items-center pt-4 border-t mt-4">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Quantity</span>
                            <div class="flex items-center gap-4">
                                <button onclick="ch(-1, event)" class="w-8 h-8 border rounded-full flex items-center justify-center font-bold">-</button>
                                <span id="qv" class="font-bold">4</span>
                                <button onclick="ch(1, event)" class="w-8 h-8 border rounded-full flex items-center justify-center font-bold">+</button>
                            </div>
                        </div>
                    </div>
                </label>
            </div>

            <div class="bg-gray-50 p-6 rounded-2xl mb-10">
                <div class="flex flex-col mb-6">
                    <span id="save-txt" class="text-[10px] font-bold text-gray-400 uppercase mb-1 invisible transition-opacity duration-300">✨ YOU SAVE Rs.0</span>
                    <div class="flex justify-between items-center">
                        <span class="text-xl font-bold uppercase tracking-tight">Total</span>
                        <span id="final-total" class="text-2xl font-bold">Rs.<?php echo number_format($p_price, 0); ?></span>
                    </div>
                </div>
                <button id="add-btn" onclick="add()" class="w-full bg-black text-white py-5 rounded-full font-bold uppercase text-[11px] tracking-[0.2em] active:scale-95 transition">Add to cart</button>
            </div>
            
            <div class="mt-8 text-sm text-gray-600 leading-relaxed italic desc-text break-words">
                <?php echo nl2br(htmlspecialchars($p_desc)); ?>
            </div>
        </div>
    </div>
</main>

<script>
    // Gallery Logic
    function changeImage(src, btn) {
        document.getElementById('main-product-img').src = src;
        document.querySelectorAll('.thumb-btn').forEach(b => {
            b.classList.remove('border-black');
            b.classList.add('border-transparent');
        });
        btn.classList.remove('border-transparent');
        btn.classList.add('border-black');
    }

    // Pricing & Cart Logic
    const baseSale = <?php echo $p_price; ?>;         
    const baseActual = <?php echo $actual_unit_price; ?>; 
    let sQty = 1, sDisc = 0;

    window.onload = function() { calc(); if(typeof loadCart==='function') loadCart(); };

    function up(q, d) {
        sDisc = d;
        if(q === 4) {
            document.getElementById('qty-box').classList.replace('hidden', 'flex');
            sQty = parseInt(document.getElementById('qv').innerText);
        } else {
            document.getElementById('qty-box').classList.replace('flex', 'hidden');
            sQty = q;
        }
        calc();
    }

    function ch(v, e) {
        e.preventDefault();
        let n = parseInt(document.getElementById('qv').innerText) + v;
        if(n < 4) n = 4;
        document.getElementById('qv').innerText = n;
        sQty = n; calc();
    }

    function calc() {
        let saleTotal = baseSale * sQty;
        let discTotal = Math.round(saleTotal * (1 - sDisc/100)); 
        let actualTotal = baseActual * sQty; 
        let saveAmount = Math.round(actualTotal - discTotal); 
        
        let saveTxtEl = document.getElementById('save-txt');
        if (saveAmount > 0) {
            saveTxtEl.innerText = '✨ YOU SAVE Rs.' + saveAmount.toLocaleString('en-US');
            saveTxtEl.classList.remove('invisible');
        } else {
            saveTxtEl.classList.add('invisible');
        }

        document.getElementById('final-total').innerText = 'Rs.' + discTotal.toLocaleString('en-US');
        
        if(document.getElementById('qty-box').classList.contains('flex')) {
            document.getElementById('b4-old').innerText = 'Rs.' + Math.round(actualTotal).toLocaleString('en-US');
            document.getElementById('b4-new').innerText = 'Rs.' + discTotal.toLocaleString('en-US');
        }
    }

    function add() {
        let unitPrice = Math.round(baseSale * (1 - sDisc/100));
        let btn = document.getElementById('add-btn');
        
        btn.innerText = 'ADDING...';
        btn.disabled = true;

        fetch('cart_action.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'add', 
                product_id: <?php echo $product_id; ?>,
                product_name: "<?php echo addslashes($p_name); ?>", 
                product_price: unitPrice,
                product_image: "<?php echo $main_img; ?>", 
                quantity: sQty,
                bundle_qty: sQty
            })
        })
        .then(r => r.json())
        .then(d => { 
            if(d.status === 'success') {
                btn.innerText = 'ADDED! ✅';
                btn.classList.add('bg-green-600');
                
                if(typeof window.loadCart==='function') window.loadCart(); 
                
                if(typeof toggleCart === "function") {
                    setTimeout(() => toggleCart(), 500); 
                }

                setTimeout(() => { 
                    btn.innerText = 'ADD TO CART'; 
                    btn.classList.remove('bg-green-600');
                    btn.disabled = false;
                }, 2000);
            }
        });
    }
</script>
</body>
</html>