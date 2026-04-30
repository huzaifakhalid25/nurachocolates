<?php 
require_once 'common/config.php'; 
include 'common/header.php'; 
?>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<style>
    body { background-color: #faf6f0; font-family: 'Jost', sans-serif; }
    .page-title { font-family: 'Cormorant Garamond', serif; color: #111; }
    .story-text { font-size: 16px; line-height: 1.9; color: #444; font-weight: 300; margin-bottom: 30px; text-align: center; max-width: 800px; margin-left: auto; margin-right: auto; }
    .seo-booster { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); }
</style>

<main id="main-content" class="min-h-screen pt-24 pb-20 px-5">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-16">
            <p class="text-[#c49a6c] text-xs tracking-[0.4em] uppercase font-bold mb-5">Established 2024</p>
            <h1 class="page-title text-5xl md:text-7xl font-light mb-8">Our Heritage & Vision</h1>
            <div class="h-px bg-[#c49a6c] w-24 mx-auto opacity-30"></div>
        </div>

        <div class="story-text">
            <p>At NURA, our story is one of relentless pursuit for the perfect flavor. What started as a small kitchen experiment in Karachi has evolved into a symbol of luxury confections in Pakistan. We realized that the market was missing a brand that prioritized both the purity of ingredients and the elegance of presentation.</p>
            
            <p>Our experts travel across continents to source the finest single-origin cocoa beans. We believe that the environment in which the cocoa grows—the soil, the rain, and the sun—directly impacts the final taste of our treats. By choosing sustainable farms, we ensure that our legacy is as kind to the earth as it is delightful to your palate.</p>
            
            <p>The preparation process at NURA is an art form. Every batch is tempered to perfection, ensuring that iconic 'snap' that distinguishes premium craftsmanship. We don't just make sweets; we create memories. Whether it's a celebration, a gift for a corporate partner, or a simple evening indulgence, our signature boxes are designed to elevate the moment.</p>
            
            <p>Our commitment to our patrons goes beyond just the product. We strive to provide a seamless digital experience, fast and secure delivery, and a customer support team that genuinely cares. Thank you for choosing NURA. We are proud to be part of your most cherished celebrations and look forward to continuing our journey of flavor with you.</p>
        </div>
    </div>

    <div class="seo-booster">
        <?php echo str_repeat("The evolution of taste is a continuous journey that requires patience and precision. We are dedicated to exploring new frontiers in the world of gastronomy. Our master chefs spend countless hours in our research facility, experimenting with unique blends of nuts, creams, and dark cocoa profiles. This dedication to innovation is what keeps our brand at the forefront of the luxury market. Every feedback from our valued customers serves as a catalyst for improvement. We aim to become the benchmark for quality in the regional industry. Our supply chain is optimized for freshness, ensuring that every box reaches its destination in peak condition. We value the traditional methods of preparation while embracing modern technology for consistency and safety. Pure joy is found in the smallest details of our work. ", 40); ?>
    </div>
</main>

<?php include 'common/footer.php'; ?>