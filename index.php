<?php 
require_once 'common/config.php'; 

$site_content = [];
$content_query = $conn->query("SELECT * FROM site_settings");
if($content_query){
    while($s = $content_query->fetch_assoc()){
        $site_content[$s['setting_key']] = $s['setting_value'];
    }
}
$val = function($key, $default) use ($site_content) {
    return htmlspecialchars_decode($site_content[$key] ?? $default); 
};

include 'common/header.php'; 

// DIAL PRODUCTS QUERY
$dial_products = [];
$check_dial = $conn->query("SHOW COLUMNS FROM products LIKE 'in_dial'");
if($check_dial && $check_dial->num_rows > 0) {
    $dial_query = "SELECT * FROM products WHERE in_dial = 1 ORDER BY id DESC LIMIT 10";
} else {
    $dial_query = "SELECT * FROM products ORDER BY id DESC LIMIT 10";
}

$res = $conn->query($dial_query);
if($res && $res->num_rows > 0){
    while($row = $res->fetch_assoc()){ $dial_products[] = $row; }
}
$total_dial = count($dial_products);
?>

<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">

<style>
/* ACCESSIBILITY FIX: Darker gold values for valid contrast ratio */
:root { --gold: #c49a6c; --gold-dark-text: #7a5631; --gold-light: #e8c99a; --gold-dark: #634320; --black: #0a0a0a; --cream: #faf6f0; --warm: #f2ece4; }
* { box-sizing: border-box; }
body { font-family: 'Jost', sans-serif; background: var(--cream); margin: 0; }
body::after { content: ''; position: fixed; inset: 0; z-index: 9990; pointer-events: none; opacity: 0.025; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E"); background-size: 160px; }

.price-tag::before { content: "Rs. "; }

.mq-wrap { background: var(--black); overflow: hidden; padding: 10px 0; border-bottom: 1px solid #1c1c1c; }
.mq-track { display: flex; width: max-content; animation: mq 40s linear infinite; }
.mq-item { padding: 0 48px; color: rgba(196,154,108,0.9); font-size: 12px; letter-spacing: 0.2em; text-transform: uppercase; font-weight: 400; display: flex; align-items: center; gap: 48px; }
.mq-item::after { content: '✦'; color: rgba(196,154,108,0.5); font-size: 12px; }
@keyframes mq { to { transform: translateX(-50%); } }

/* PERFORMANCE FIX: background-color added to hero so it paints instantly */
.hero { position: relative; height: 65vh; min-height: 400px; max-height: 700px; overflow: hidden; background-color: #111; }
.hero-vid { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.55; z-index: 1; }
.hero-grad { position: absolute; inset: 0; z-index: 2; background: linear-gradient(to bottom, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.2) 35%, rgba(0,0,0,0.6) 65%, rgba(0,0,0,0.97) 100%), radial-gradient(ellipse at 50% 60%, rgba(0,0,0,0.3) 0%, transparent 70%); }
.hero-body { position: absolute; inset: 0; z-index: 3; display: flex; flex-direction: column; align-items: center; text-align: center; justify-content: center; padding: 0 24px; padding-top: 60px; }
.h-content { display: flex; flex-direction: column; align-items: center; will-change: transform; }
.h-tag { color: var(--gold); font-size: 12px; letter-spacing: 0.3em; text-transform: uppercase; font-weight: 500; opacity: 0; animation: up 0.9s cubic-bezier(.22,1,.36,1) 0.3s forwards; text-shadow: 0 1px 8px rgba(0,0,0,0.9); }
.h-title { font-family: 'Cormorant Garamond', serif; font-size: clamp(3.5rem, 13vw, 8rem); font-weight: 400; color: #fff; line-height: 0.9; margin: 14px 0 12px; opacity: 0; animation: up 1s cubic-bezier(.22,1,.36,1) 0.55s forwards; text-shadow: 0 2px 30px rgba(0,0,0,0.95), 0 0 80px rgba(0,0,0,0.7); }
.h-title em { font-style: italic; color: var(--gold-light); display: block; }
.h-sub { color: rgba(255,255,255,0.9); font-size: 13px; letter-spacing: 0.22em; font-weight: 400; opacity: 0; animation: up 0.9s cubic-bezier(.22,1,.36,1) 0.8s forwards; margin-bottom: 36px; background: rgba(0,0,0,0.35); padding: 6px 20px; backdrop-filter: blur(4px); border-radius: 20px; }
.h-cta { display: inline-flex; align-items: center; gap: 14px; border: 1px solid rgba(196,154,108,0.9); color: #fff; padding: 14px 36px; font-size: 12px; letter-spacing: 0.3em; text-transform: uppercase; text-decoration: none; backdrop-filter: blur(10px); background: rgba(196,154,108,0.2); opacity: 0; animation: up 0.9s cubic-bezier(.22,1,.36,1) 1.05s forwards; transition: all 0.4s; border-radius: 30px; font-weight: 600; }
.h-cta:hover { background: var(--gold); color: var(--black); border-color: var(--gold); gap: 22px; }
@keyframes up { from{opacity:0;transform:translateY(28px)} to{opacity:1;transform:translateY(0)} }
.rv { opacity: 0; transform: translateY(32px); transition: opacity 0.8s cubic-bezier(.22,1,.36,1), transform 0.8s cubic-bezier(.22,1,.36,1); }
.rv.on { opacity: 1; transform: translateY(0); }

.trust-bar { background: var(--black); padding: 32px 0; }
.ti { text-align: center; }
.ti-icon { width: 46px; height: 46px; border: 1px solid rgba(196,154,108,0.4); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; margin: 0 auto 10px; background: rgba(196,154,108,0.1); color: var(--gold); }
.ti-v { color: rgba(255,255,255,0.95); font-size: 13px; font-weight: 500; letter-spacing: 0.06em; margin-bottom: 3px; }
.ti-l { color: rgba(255,255,255,0.7); font-size: 12px; letter-spacing: 0.2em; text-transform: uppercase; }

.prod-sec { background: var(--cream); padding: 90px 0; }
.sec-label { color: var(--gold-dark-text); font-size: 12px; letter-spacing: 0.3em; text-transform: uppercase; font-weight: 700; }
.sec-title { font-family: 'Cormorant Garamond', serif; font-size: clamp(2.2rem, 6vw, 4rem); font-weight: 600; color: var(--black); line-height: 1.1; margin-bottom: 15px; }
.gem-div { display: flex; align-items: center; gap: 14px; max-width: 120px; margin: 18px auto 0; }
.gem-div::before,.gem-div::after { content:''; flex:1; height:1px; background:var(--gold); opacity:0.5; }

.luxury-box { background: transparent; display: flex; flex-direction: column; align-items: center; text-align: center; }
.lb-img-wrap { width: 100%; aspect-ratio: 4/5; overflow: hidden; position: relative; margin-bottom: 24px; background: #e8ddd2; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
.lb-img { width: 100%; height: 100%; object-fit: cover; transition: transform 1s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
.luxury-box:hover .lb-img { transform: scale(1.05); }
.lb-title { font-family: 'Cormorant Garamond', serif; font-size: 28px; font-weight: 600; color: #111; margin-bottom: 12px; line-height: 1.2; word-wrap: break-word; overflow-wrap: break-word; }
.lb-desc { font-family: 'Jost', sans-serif; font-size: 15px; color: #333; font-weight: 400; line-height: 1.7; max-width: 90%; margin: 0 auto 20px; word-wrap: break-word; overflow-wrap: break-word; }
.lb-desc p { margin-bottom: 0.5em; }

.lb-btn { display: inline-block; border: 1px solid #111; color: #111; padding: 12px 36px; font-size: 11px; letter-spacing: 0.25em; text-transform: uppercase; text-decoration: none; transition: all 0.4s; border-radius: 30px; font-weight: bold; }
.lb-btn:hover { background: #111; color: #fff; }
.sale-tag { position: absolute; top: 15px; left: 15px; background: #111; color: #fff; font-size: 10px; letter-spacing: 0.2em; text-transform: uppercase; padding: 6px 14px; z-index: 5; border-radius: 20px; }

.bnr { background: var(--black); padding: 110px 24px; text-align: center; position: relative; overflow: hidden; }
.bnr::before { content:''; position:absolute; inset:0; background:radial-gradient(ellipse at 50% 70%,rgba(196,154,108,0.09) 0%,transparent 65%); }
.bnr-bg { font-family:'Cormorant Garamond',serif; font-size:clamp(70px,20vw,210px); font-weight:400; color:rgba(196,154,108,0.04); position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); pointer-events:none; }
.bnr-in { position:relative; z-index:2; }
.bnr-ey { color:var(--gold); font-size:12px; letter-spacing:0.4em; text-transform:uppercase; font-weight:500; margin-bottom:18px; }
.bnr-h { font-family:'Cormorant Garamond',serif; font-size:clamp(2.6rem,8vw,6rem); font-weight:400; color:#fff; margin-bottom:28px; }
.bnr-h em { font-style:italic; color:var(--gold-light); }
.bnr-p { color:rgba(255,255,255,0.8); font-size:14px; font-weight:400; max-width:420px; margin:0 auto 44px; line-height:2; }
.bnr-a { display:inline-flex; align-items:center; gap:12px; border:1px solid rgba(196,154,108,0.7); color:var(--gold); padding:15px 48px; font-size:12px; letter-spacing:0.35em; text-transform:uppercase; text-decoration:none; transition:all 0.4s; border-radius: 30px; font-weight: bold; }
.bnr-a:hover { background:var(--gold); color:var(--black); border-color:var(--gold); gap:20px; }

.ts-sec { background: var(--warm); padding: 90px 0; overflow: hidden; }
.ts-track { display:flex; gap:20px; width:max-content; animation:tsScroll 32s linear infinite; }
.ts-track:hover { animation-play-state:paused; }
@keyframes tsScroll { to { transform:translateX(-50%); } }
.tsc { background:#fff; padding:30px 28px; width:300px; border-left:2px solid var(--gold-dark); box-shadow:0 4px 20px rgba(0,0,0,0.06); border-radius: 12px; }
.tsc-s { color:var(--gold-dark-text); font-size:12px; letter-spacing:4px; margin-bottom:14px; }
.tsc-t { font-family:'Cormorant Garamond',serif; font-size:17px; color:#222; line-height:1.7; font-style:italic; margin-bottom:18px; font-weight: 600; }
.tsc-a { font-size:12px; letter-spacing:0.25em; color:var(--gold-dark); text-transform:uppercase; font-weight:700; }

.dial-sec { background:radial-gradient(ellipse at 50% 25%,#1a1006 0%,#060606 100%); min-height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; overflow:hidden; padding:80px 20px; position:relative; margin-bottom: -2px !important; }
.dial-bg-t { position:absolute; font-family:'Cormorant Garamond',serif; font-size:clamp(100px,26vw,280px); font-weight:400; color:rgba(196,154,108,0.025); top:50%; left:50%; transform:translate(-50%,-50%); pointer-events:none; }
.dial-ring { position:absolute; bottom:-200px; width:800px; height:800px; border-radius:50%; border:1px solid rgba(196,154,108,0.14); transform:rotateX(75deg); z-index:0; animation:spin 50s linear infinite; }
.dial-ring::before { content:''; position:absolute; inset:70px; border-radius:50%; border:1px dashed rgba(196,154,108,0.06); }
@keyframes spin { 100%{transform:rotateX(75deg) rotateZ(360deg);} }
.scene { width:268px; height:400px; perspective:1400px; z-index:10; margin-top:40px; }
.rotor { width:100%; height:100%; position:relative; transform-style:preserve-3d; transition:transform 0.7s cubic-bezier(.22,1,.36,1); }
.di { position:absolute; width:268px; height:400px; left:0; top:0; background:rgba(10,8,4,0.97); border:1px solid rgba(196,154,108,0.2); border-radius:16px; display:flex; flex-direction:column; padding:16px; opacity:0; visibility:hidden; transform-style:preserve-3d; transition:opacity 0.5s, transform 0.7s cubic-bezier(.22,1,.36,1); }
.di.on { opacity:1; visibility:visible; border-color:rgba(196,154,108,0.5); box-shadow:0 0 80px rgba(196,154,108,0.1); pointer-events:auto; }

.d-ctrl { display:flex; gap:18px; margin-top:44px; z-index:20; align-items:center; padding-bottom: 20px; }
.d-btn { background:transparent; border:1px solid rgba(196,154,108,0.4); color:var(--gold); width:50px; height:50px; border-radius:50%; font-size:18px; cursor:pointer; transition:all 0.3s; display:flex; align-items:center; justify-content:center; }
.d-btn:hover { background:var(--gold); color:var(--black); transform:scale(1.1); }
.d-num { color:rgba(255,255,255,0.8); font-size:12px; letter-spacing:0.2em; font-weight:500; min-width:64px; text-align:center; }
footer, .footer-wrap { margin-top: 0 !important; border-top: none !important; }
</style>

<main id="main-content">
<section class="hero">
    <video class="hero-vid" autoplay loop muted playsinline>
        <source src="Nura_1 (1).webm" type="video/webm">
    </video>
    <div class="hero-grad"></div>
    <div class="hero-body">
        <div class="h-content" id="h-content">
            <p class="h-tag"><?php echo $val('hero_tag', 'Handcrafted in Pakistan &middot; Est. 2024'); ?></p>
            <h1 class="h-title"><?php echo $val('hero_title', 'Taste<em>The Luxury</em>'); ?></h1>
            <p class="h-sub"><?php echo $val('hero_sub', 'Single-origin cacao &nbsp;·&nbsp; Artisan crafted &nbsp;·&nbsp; Luxury gifting'); ?></p>
            <a href="products" class="h-cta"><?php echo $val('hero_cta', 'Explore Collection &nbsp;→'); ?></a>
        </div>
    </div>
</section>

<div class="mq-wrap">
    <div class="mq-track">
        <?php 
        $mq_text = $val('marquee_text', 'Free delivery above PKR 2000, Premium artisan treats, Gift wrapping available');
        $mq_array = explode(',', $mq_text);
        for($j=0; $j<4; $j++){
            foreach($mq_array as $m): 
                if(trim($m) != ''):
        ?>
        <span class="mq-item"><?php echo trim($m); ?></span>
        <?php 
                endif;
            endforeach; 
        }
        ?>
    </div>
</div>

<div class="trust-bar">
    <div class="max-w-4xl mx-auto px-5 grid grid-cols-2 md:grid-cols-4 gap-5">
        <?php foreach([['🍫','Premium Cacao','Single Origin'],['🎁','Gift Ready','Luxury Packaging'],['🚚','Fast Delivery','Across Pakistan'],['✦','100% Authentic','Artisan Made']] as $i=>$t): ?>
        <div class="ti rv" style="transition-delay:<?php echo $i*70; ?>ms">
            <div class="ti-icon" aria-hidden="true"><?php echo $t[0]; ?></div>
            <div class="ti-v"><?php echo $t[1]; ?></div>
            <div class="ti-l"><?php echo $t[2]; ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<section class="prod-sec" id="prod-sec">
    <div class="max-w-6xl mx-auto px-5">
        <div class="text-center mb-20 rv">
            <p class="sec-label mb-4"><?php echo $val('prod_label', 'Featured Selection'); ?></p>
            <h2 class="sec-title"><?php echo $val('prod_title', 'The Signature Collection'); ?></h2>
            <div class="gem-div"><span style="color:var(--gold-dark-text);font-size:12px" aria-hidden="true">✦</span></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-y-16 gap-x-8">
            <?php
            $check_feat = $conn->query("SHOW COLUMNS FROM products LIKE 'is_featured'");
            if($check_feat && $check_feat->num_rows > 0) {
                $sql = "SELECT * FROM products WHERE is_featured = 1 ORDER BY id DESC LIMIT 4";
                $result = $conn->query($sql);
                if($result && $result->num_rows < 4) {
                    $sql = "SELECT * FROM products ORDER BY is_featured DESC, id DESC LIMIT 4";
                    $result = $conn->query($sql);
                }
            } else {
                $sql = "SELECT * FROM products ORDER BY id DESC LIMIT 4";
                $result = $conn->query($sql);
            }
            
            if($result && $result->num_rows > 0){
                $d = 0;
                while($row = $result->fetch_assoc()){
                    $pid = $row['id'];
                    $pn  = str_replace(['','â€™',''], "'", stripslashes($row['name']));
                    $img = str_replace('../', '', $row['image_url']);
                    
                    $desc_to_show = isset($row['description']) && !empty(trim($row['description'])) 
                                  ? stripslashes($row['description']) 
                                  : 'An exquisite artisan creation, meticulously handcrafted.';
                                  
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $pn)));
                    $d += 100; if($d > 400) $d = 100;
            ?>
            <div class="rv" style="transition-delay:<?php echo $d; ?>ms;">
                <div class="luxury-box">
                    <a href="item/<?php echo $slug; ?>" class="block w-full" aria-label="View <?php echo htmlspecialchars($pn); ?>">
                        <div class="lb-img-wrap">
                            <?php if($row['is_sale'] ?? false): ?><div class="sale-tag">Sale</div><?php endif; ?>
                            <img src="<?php echo htmlspecialchars($img); ?>" width="400" height="500" loading="lazy" alt="<?php echo htmlspecialchars($pn); ?>" class="lb-img" onerror="this.style.display='none';">
                        </div>
                    </a>
                    <h3 class="lb-title"><?php echo htmlspecialchars($pn); ?></h3>
                    
                    <div class="lb-desc">
                        <?php echo nl2br(htmlspecialchars($desc_to_show)); ?>
                    </div>
                    
                    <a href="item/<?php echo $slug; ?>" class="lb-btn" aria-label="Discover more about <?php echo htmlspecialchars($pn); ?>">Discover More</a>
                </div>
            </div>
            <?php } } else { ?>
                <div class='col-span-full text-center py-20'><p style='font-family:Cormorant Garamond,serif;font-size:1.4rem;color:#555;'>Curating our collection...</p></div>
            <?php } ?>
        </div>
        <div class="text-center mt-20 rv">
            <a href="products" class="inline-block border-b border-black pb-1 text-xs tracking-widest uppercase text-gray-900 font-bold hover:text-[#8a6340] hover:border-[#8a6340] transition duration-300">View The Complete Collection</a>
        </div>
    </div>
</section>

<section class="bnr rv">
    <div class="bnr-bg"><?php echo $val('banner_bg_text', 'NURA'); ?></div>
    <div class="bnr-in">
        <p class="bnr-ey"><?php echo $val('banner_ey', 'The Nura Promise'); ?></p>
        <h2 class="bnr-h"><?php echo $val('banner_h', 'Crafted for<br><em>Moments</em>'); ?></h2>
        <p class="bnr-p"><?php echo $val('banner_p', 'Every piece of chocolate tells a story — from the cacao farm to your hands.'); ?></p>
        <a href="products" class="bnr-a"><?php echo $val('banner_a', 'Shop Now &rarr;'); ?></a>
    </div>
</section>

<section class="ts-sec">
    <div class="max-w-7xl mx-auto px-4 mb-12 rv text-center">
        <p class="sec-label mb-2">What Our Customers Say</p>
        <h2 class="sec-title">Loved by Thousands</h2>
    </div>
    <div style="overflow:hidden">
        <div class="ts-track">
            <?php 
            $reviews = [
                [$val('rev1_txt', 'The most exquisite chocolate I have tasted.'), $val('rev1_name', 'Aisha K.')],
                [$val('rev2_txt', 'Gifted these to my clients, amazing quality.'), $val('rev2_name', 'Hamza R.')],
                [$val('rev3_txt', 'Absolutely divine. Will order again.'), $val('rev3_name', 'Sara M.')]
            ];
            foreach(array_merge($reviews, $reviews, $reviews) as $t): ?>
            <div class="tsc">
                <div class="tsc-s" aria-hidden="true">★★★★★</div>
                <p class="tsc-t">"<?php echo $t[0]; ?>"</p>
                <p class="tsc-a"><?php echo $t[1]; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="dial-sec" id="dial-sec">
    <div class="dial-bg-t">NURA</div>
    <div class="dial-ring"></div>
    <div class="text-center z-20 mb-0 rv">
        <p class="sec-label mb-3" style="color:var(--gold)"><?php echo $val('dial_label', 'Interactive Experience'); ?></p>
        <h2 style="font-family:'Cormorant Garamond',serif;font-size:clamp(2rem,6vw,4.2rem);font-weight:300;color:#fff;line-height:1.05"><?php echo $val('dial_heading', 'The Signature <em>Dial</em>'); ?></h2>
        <div class="gem-div" style="margin-top:12px;"><span style="color:var(--gold);font-size:12px" aria-hidden="true">✦</span></div>
    </div>

    <div class="scene" id="scene">
        <div class="rotor" id="rotor">
            <?php if($total_dial > 0): foreach($dial_products as $idx => $row):
                $pid = $row['id']; 
                $pn  = str_replace(['','â€™',''], "'", stripslashes($row['name']));
                $pp  = number_format($row['price'], 2); 
                $img = str_replace('../', '', $row['image_url']);
                
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $pn)));
            ?>
            <div class="di <?php echo $idx===0?'on':''; ?>">
                <a href="item/<?php echo $slug; ?>" style="display:block;height:52%;overflow:hidden;margin-bottom:14px;border:1px solid rgba(196,154,108,0.15); border-radius: 8px;" aria-label="View <?php echo htmlspecialchars($pn); ?>">
                    <img src="<?php echo htmlspecialchars($img); ?>" width="268" height="208" loading="lazy" alt="<?php echo htmlspecialchars($pn); ?>" style="width:100%;height:100%;object-fit:cover;">
                </a>
                <div style="flex:1;display:flex;flex-direction:column;justify-content:space-between;text-align:center;">
                    <div>
                        <h3 style="font-family:'Cormorant Garamond',serif;color:#fff;font-size:18px;margin-bottom:5px;font-weight:300;"><?php echo htmlspecialchars($pn); ?></h3>
                        <p style="color:var(--gold);font-size:11px;letter-spacing:0.2em;margin-bottom:12px;" class="price-tag"><?php echo $pp; ?></p>
                    </div>
                    <div class="abc" style="opacity:<?php echo $idx===0?'1':'0'; ?>;pointer-events:<?php echo $idx===0?'auto':'none'; ?>;">
                        <a href="item/<?php echo $slug; ?>" style="display:block;border:1px solid rgba(196,154,108,0.35);color:var(--gold);padding:6px;font-size:12px;letter-spacing:0.1em;text-transform:uppercase;text-decoration:none;margin-bottom:6px; border-radius: 30px;">View Details</a>
                        <button onclick="insertToCart(<?php echo $pid; ?>,'<?php echo addslashes($pn); ?>',<?php echo $row['price']; ?>,'<?php echo addslashes($img); ?>',event)" style="width:100%;background:var(--gold);color:#000;border:none;padding:8px;font-size:12px;letter-spacing:0.1em;text-transform:uppercase;font-weight:bold;cursor:pointer; border-radius: 30px;" aria-label="Add <?php echo htmlspecialchars($pn); ?> to Cart">Add Item</button>
                    </div>
                </div>
            </div>
            <?php endforeach; else: ?>
                <div style="color:rgba(255,255,255,0.6);font-size:12px;letter-spacing:0.2em;text-align:center;margin-top:80px;">Add products from admin panel</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="d-ctrl">
        <button class="d-btn" id="dp" aria-label="Previous Item">&larr;</button>
        <div class="d-num" id="dnum" aria-live="polite">01 / <?php echo str_pad($total_dial,2,'0',STR_PAD_LEFT); ?></div>
        <button class="d-btn" id="dn" aria-label="Next Item">&rarr;</button>
    </div>
</section>

</main>

<script>
function insertToCart(id,name,price,image,event){
    event.preventDefault(); event.stopPropagation();
    const btn=event.currentTarget;
    btn.innerHTML = 'Adding...';
    fetch('cart_action.php',{method:'POST',headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`action=add&product_id=${id}&product_name=${encodeURIComponent(name)}&product_price=${price}&product_image=${encodeURIComponent(image)}&quantity=1&bundle_qty=1`})
    .then(r=>r.json()).then(d=>{
        if(d.status==='success'){
            if(typeof window.loadCart==='function') window.loadCart();
            setTimeout(()=>{ btn.innerHTML='Added'; if(typeof toggleCart==='function') toggleCart(); },900);
        }
    });
}

document.addEventListener('DOMContentLoaded',()=>{
    const obs = new IntersectionObserver(entries=>{
        entries.forEach(e=>{ 
            if(e.isIntersecting) e.target.classList.add('on'); 
        });
    },{threshold:0.08});
    document.querySelectorAll('.rv').forEach(el=>obs.observe(el));

    const hContent = document.getElementById('h-content');
    window.addEventListener('scroll', ()=>{ 
        if(hContent) hContent.style.transform = `translateY(${window.scrollY * 0.15}px)`;
    }, {passive:true});

    const rotor = document.getElementById('rotor');
    const items = [...document.querySelectorAll('.di')];
    const n = items.length; if(!n) return;
    const theta = 360/n;
    const iw = window.innerWidth < 640 ? 230 : 268;
    const R = Math.round((iw/2)/Math.tan(Math.PI/n)) + 80;

    items.forEach((item,i)=>{ item.style.transform = `rotateY(${theta*i}deg) translateZ(${R}px) scale(${i===0?1:0.62})`; });
    rotor.style.transform = `translateZ(${-R}px) rotateY(0deg)`;

    let cur=0, ang=0, busy=false;

    function go(dir){
        if(busy) return; busy=true;
        dir==='n' ? (cur++, ang-=theta) : (cur--, ang+=theta);
        if(cur<0) cur=n-1; if(cur>=n) cur=0;
        rotor.style.transform = `translateZ(${-R}px) rotateY(${ang}deg)`;
        
        items.forEach((item,i)=>{
            const active = i===cur;
            item.style.transform = `rotateY(${theta*i}deg) translateZ(${R}px) scale(${active?1:0.62})`;
            item.classList.toggle('on', active);
            const bc = item.querySelector('.abc');
            if(bc){ bc.style.opacity=active?'1':'0'; bc.style.pointerEvents=active?'auto':'none'; }
        });
        document.getElementById('dnum').innerText = String(cur+1).padStart(2,'0')+' / '+String(n).padStart(2,'0');
        setTimeout(()=>{busy=false;}, 700);
    }
    document.getElementById('dn')?.addEventListener('click',()=>go('n'));
    document.getElementById('dp')?.addEventListener('click',()=>go('p'));
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js" defer></script>

<?php include 'common/footer.php'; ?>