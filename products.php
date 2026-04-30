<?php
require_once 'common/config.php';
include 'common/header.php';

// Get category filter from URL if it exists
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';

// Build SQL Query
if ($category_filter) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? ORDER BY id DESC");
    $stmt->bind_param("s", $category_filter);
} else {
    $stmt = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop All | NURA Chocolates</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .brand-serif { font-family: 'Cormorant Garamond', serif; }
        .brand-sans { font-family: 'Jost', sans-serif; }
        .luxury-card { transition: all 0.4s ease; }
        .luxury-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.08); }
        .product-img-wrap { overflow: hidden; position: relative; aspect-ratio: 1/1; background: #f2ece4; }
        .product-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.8s ease; }
        .luxury-card:hover .product-img { transform: scale(1.08); }
        
        .add-btn { position: absolute; bottom: 0; left: 0; width: 100%; background: #c49a6c; color: #000; padding: 12px; text-align: center; text-transform: uppercase; letter-spacing: 0.2em; font-size: 11px; font-weight: 600; transform: translateY(100%); transition: transform 0.3s ease; border: none; cursor: pointer; }
        .luxury-card:hover .add-btn { transform: translateY(0); }
        @media (max-width: 768px) {
            .add-btn { transform: translateY(0); }
        }
        .filter-link.active { border-bottom: 2px solid #c49a6c; color: #000; font-weight: 500; }
    </style>
</head>
<body class="bg-[#faf9f6] brand-sans text-gray-800">

<section class="py-16 px-5 text-center bg-white border-b border-gray-100">
    <p class="text-[#c49a6c] text-[10px] tracking-[0.4em] uppercase mb-3">Our Collection</p>
    <h1 class="brand-serif text-4xl md:text-5xl font-light mb-6">
        <?php echo $category_filter ? htmlspecialchars($category_filter) . " <em>Selection</em>" : "All <em>Products</em>"; ?>
    </h1>
    
    <div class="flex flex-wrap justify-center gap-6 text-xs md:text-sm tracking-[0.1em] uppercase mt-8">
        <a href="products" class="pb-1 filter-link <?php echo $category_filter === '' ? 'active' : 'text-gray-400 hover:text-black transition'; ?>">View All</a>
        <a href="products?category=Artisan" class="pb-1 filter-link <?php echo $category_filter === 'Artisan' ? 'active' : 'text-gray-400 hover:text-black transition'; ?>">Artisan</a>
        <a href="products?category=Signature" class="pb-1 filter-link <?php echo $category_filter === 'Signature' ? 'active' : 'text-gray-400 hover:text-black transition'; ?>">Signature</a>
    </div>
</section>

<section class="max-w-7xl mx-auto px-4 py-16 min-h-[50vh]">
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $pid = $row['id'];
                $pname = stripslashes($row['name']);
                $pprice = number_format($row['price']);
                $pimg = str_replace('../', '', $row['image_url']);
                
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $pname)));
        ?>
            <div class="luxury-card bg-white border border-[#e8ddd2] flex flex-col h-full relative group">
                <a href="item/<?php echo $slug; ?>" class="block flex-1 flex flex-col" aria-label="View <?php echo htmlspecialchars($pname); ?>">
                    
                    <div class="product-img-wrap">
                        <?php if($row['is_sale'] ?? false): ?>
                            <span class="absolute top-2 left-2 z-10 bg-black text-white text-[9px] uppercase tracking-widest py-1 px-3">Sale</span>
                        <?php endif; ?>
                        
                        <img src="<?php echo htmlspecialchars($pimg); ?>" alt="<?php echo htmlspecialchars($pname); ?>" class="product-img" loading="lazy" width="300" height="300" onerror="this.style.display='none'">
                        
                        <button class="add-btn" aria-label="Add to cart" onclick="event.preventDefault(); addToCart(<?php echo $pid; ?>, '<?php echo addslashes($pname); ?>', <?php echo $row['price']; ?>, '<?php echo addslashes($pimg); ?>', event)">
                            Add to Cart
                        </button>
                    </div>

                    <div class="p-4 text-center flex flex-col flex-1 justify-between">
                        <h2 class="brand-serif text-lg leading-tight text-gray-900 mb-2"><?php echo htmlspecialchars($pname); ?></h2>
                        <p class="text-[#c49a6c] text-sm tracking-wide">Rs. <?php echo $pprice; ?></p>
                    </div>
                </a>
            </div>
        <?php 
            }
        } else {
            echo "<div class='col-span-full text-center py-20'><p class='brand-serif text-2xl text-gray-400'>No products found in this category.</p></div>";
        }
        ?>
    </div>
</section>

<script>
function addToCart(id,name,price,image,event){
    event.preventDefault(); event.stopPropagation();
    const btn=event.currentTarget, orig=btn.innerHTML;
    btn.innerHTML='Adding...';
    fetch('cart_action.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`action=add&product_id=${id}&product_name=${encodeURIComponent(name)}&product_price=${price}&product_image=${encodeURIComponent(image)}&quantity=1`})
    .then(r=>r.json()).then(d=>{
        if(d.status==='success'){
            btn.innerHTML='Added ✓'; 
            if(typeof window.loadCart==='function') window.loadCart();
            setTimeout(()=>{ btn.innerHTML=orig; if(typeof toggleCart==='function') toggleCart(); },900);
        }
    });
}
</script>

<?php include 'common/footer.php'; ?>
</body>
</html>