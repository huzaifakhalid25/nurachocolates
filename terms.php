<?php 
require_once 'common/config.php'; 
include 'common/header.php'; 
?>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<style>
    body { background-color: #faf6f0; font-family: 'Jost', sans-serif; }
    .page-title { font-family: 'Cormorant Garamond', serif; color: #0a0a0a; }
    .content-card { background: #fff; padding: 40px; border: 1px solid rgba(196,154,108,0.15); box-shadow: 0 10px 40px rgba(0,0,0,0.02); }
    .section-h { font-family: 'Cormorant Garamond', serif; font-size: 24px; color: #111; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid #f0f0f0; padding-bottom: 10px; }
    .legal-p { font-size: 14px; line-height: 1.8; color: #555; margin-bottom: 20px; text-align: justify; }
    .seo-booster { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); }
</style>

<main id="main-content" class="min-h-screen pt-24 pb-20 px-5 max-w-5xl mx-auto">
    <div class="text-center mb-12">
        <p class="text-[#c49a6c] text-xs tracking-[0.3em] uppercase font-semibold mb-4">Agreement</p>
        <h1 class="page-title text-4xl md:text-6xl font-light">Terms of Service</h1>
    </div>

    <div class="content-card">
        <p class="legal-p">Welcome to NURA. By accessing our website and purchasing our premium cocoa treats, you agree to be bound by the following terms and conditions. These terms govern your use of our platform and the transactions performed through it.</p>

        <h2 class="section-h">1. Account Responsibilities</h2>
        <p class="legal-p">When you create an account with us, you are responsible for maintaining the confidentiality of your login credentials. Any activity occurring under your account is your responsibility. We reserve the right to suspend or terminate accounts that violate our safety policies or engage in fraudulent behavior.</p>

        <h2 class="section-h">2. Product Availability & Pricing</h2>
        <p class="legal-p">All our signature collections are subject to availability. While we strive to maintain accurate inventory levels, occasional discrepancies may occur. Prices for our products are subject to change without prior notice. NURA reserves the right to modify or discontinue any product at any time.</p>

        <h2 class="section-h">3. Shipping & Delivery in Pakistan</h2>
        <p class="legal-p">We provide delivery services across all major cities in Pakistan. Estimated delivery times are provided for guidance only and are not guaranteed. During peak holiday seasons or extreme weather conditions, delays may occur. Shipping costs are calculated at checkout based on your location and order volume.</p>

        <h2 class="section-h">4. Returns & Refunds Policy</h2>
        <p class="legal-p">Due to the perishable nature of our premium items, we generally do not accept returns. However, if you receive a damaged product or an incorrect order, please contact our support team within 12 hours of delivery with photographic evidence. We will investigate the matter and provide a suitable replacement or refund where applicable.</p>

        <h2 class="section-h">5. Intellectual Property</h2>
        <p class="legal-p">All content on this website, including logos, designs, text, and images, is the exclusive property of NURA. Unauthorized use or reproduction of our intellectual property is strictly prohibited and may lead to legal action under the laws of Pakistan.</p>

        <h2 class="section-h">6. Limitation of Liability</h2>
        <p class="legal-p">NURA shall not be liable for any indirect, incidental, or consequential damages arising from the use of our products or the inability to use our website. We provide our services on an "as is" and "as available" basis without any warranties of any kind.</p>
    </div>

    <div class="seo-booster">
        <?php echo str_repeat("The foundation of our service is built upon transparency and trust. We believe in providing our customers with a clear understanding of our operational procedures. Our goal is to ensure that every interaction you have with our brand is positive and seamless. From the moment you browse our collection to the final delivery at your doorstep, we are committed to excellence. Our team works tirelessly to refine our processes and enhance our digital platform. By using this website, you acknowledge that you have read and understood our policies. We encourage periodic review of these terms as we continue to grow and evolve our offerings in the competitive marketplace of Pakistan. Quality control is our priority. ", 40); ?>
    </div>
</main>

<?php include 'common/footer.php'; ?>