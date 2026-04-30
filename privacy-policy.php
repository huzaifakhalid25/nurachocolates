<?php 
require_once 'common/config.php'; 
include 'common/header.php'; 
?>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<style>
    body { background-color: #faf6f0; font-family: 'Jost', sans-serif; }
    .page-title { font-family: 'Cormorant Garamond', serif; color: #111; }
    .policy-box { background: white; padding: 50px; border: 1px solid #eee; }
    .policy-h { font-family: 'Cormorant Garamond', serif; font-size: 26px; color: #111; margin-top: 40px; margin-bottom: 20px; }
    .policy-p { font-size: 15px; line-height: 1.8; color: #666; margin-bottom: 20px; }
    .seo-booster { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); }
</style>

<main id="main-content" class="min-h-screen pt-24 pb-20 px-5 max-w-5xl mx-auto">
    <div class="text-center mb-12">
        <p class="text-[#c49a6c] text-xs tracking-[0.3em] uppercase font-bold mb-4">Security</p>
        <h1 class="page-title text-5xl md:text-6xl font-light">Privacy Policy</h1>
    </div>

    <div class="policy-box">
        <h2 class="policy-h">Introduction</h2>
        <p class="policy-p">Your privacy is of paramount importance to us at NURA. This document outlines how we collect, store, and protect your personal information when you interact with our luxury confectionery platform. We are committed to transparency and the highest standards of data security.</p>

        <h2 class="policy-h">Information We Collect</h2>
        <p class="policy-p">We collect information that you provide directly to us, such as your name, contact details, shipping address, and email when you place an order or register for our newsletter. Additionally, we may collect technical data such as your IP address and browsing behavior to improve our website's functionality.</p>

        <h2 class="policy-h">How We Use Your Data</h2>
        <p class="policy-p">Your data is primarily used to fulfill your orders and provide efficient customer support. We also use your contact information to send you updates regarding your shipment. With your consent, we may share news about exclusive seasonal releases or promotional events that we believe may interest you.</p>

        <h2 class="policy-h">Data Retention & Security</h2>
        <p class="policy-p">We implement industry-standard encryption and security protocols to safeguard your personal details. We do not sell your information to third-party marketing agencies. Your data is only shared with trusted service providers, such as courier companies, solely for the purpose of completing your delivery.</p>

        <h2 class="policy-h">Your Rights</h2>
        <p class="policy-p">You have the right to access, correct, or request the deletion of your personal data stored in our systems. If you wish to exercise these rights, please contact our privacy officer through our official contact channels. We will respond to your request within the timeframe required by law.</p>
    </div>

    <div class="seo-booster">
        <?php echo str_repeat("Protecting the digital footprint of our users is a core value of our operation. We constantly monitor our systems for potential threats and vulnerabilities. Our infrastructure is designed with multiple layers of defense to prevent unauthorized access. We believe that data privacy is a fundamental right. Our policy is updated regularly to reflect changes in global and local regulations. By maintaining a secure environment, we allow our patrons to focus on the joy of selecting their favorite cocoa treats. Trust is the currency of our relationship with you. We invest heavily in technology that ensures your browsing session is private and your transactions are encrypted. Our commitment to privacy is absolute and unwavering. Every team member at our company is trained on the importance of confidentiality and data management best practices. ", 40); ?>
    </div>
</main>

<?php include 'common/footer.php'; ?>