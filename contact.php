<?php 
require_once 'common/config.php'; 
include 'common/header.php'; 
?>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
<style>
    body { background-color: #faf6f0; font-family: 'Jost', sans-serif; }
    .page-title { font-family: 'Cormorant Garamond', serif; color: #0a0a0a; }
    .gold-border { border-color: rgba(196,154,108,0.4); }
    .gold-border:focus { border-color: #c49a6c; outline: none; }
</style>

<main id="main-content" class="min-h-screen pt-24 pb-20 px-5 max-w-6xl mx-auto">
    <div class="text-center mb-16">
        <p class="text-[#c49a6c] text-xs tracking-[0.3em] uppercase font-semibold mb-4">Reach Out</p>
        <h1 class="page-title text-5xl md:text-7xl font-light mb-6">Contact Us</h1>
        <div class="flex items-center justify-center gap-4">
            <div class="h-px bg-[#c49a6c] w-12 opacity-50"></div>
            <span class="text-[#c49a6c] text-sm">✦</span>
            <div class="h-px bg-[#c49a6c] w-12 opacity-50"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-start">
        <div class="space-y-8 text-gray-700 text-[14px] leading-relaxed font-light">
            <p>
                We are always delighted to hear from our patrons. Whether you have a question about our signature collections, need assistance with a corporate order, or simply wish to share your experience with our premium cocoa treats, our dedicated concierge team is at your service.
            </p>
            <p>
                At NURA, we pride ourselves on delivering not just exceptional delicacies, but also an unparalleled level of customer care. Please allow up to 24 hours for our representatives to respond to your inquiries. We ensure that every piece of communication is handled with the utmost priority and respect.
            </p>
            
            <div class="pt-8 border-t border-gray-200">
                <h3 class="page-title text-2xl mb-4">Direct Information</h3>
                <p class="mb-2"><strong class="font-medium">Email:</strong> support@nurachocolates.rf.gd</p>
                <p class="mb-2"><strong class="font-medium">Hours:</strong> Monday - Saturday, 9:00 AM to 6:00 PM (PKT)</p>
                <p><strong class="font-medium">Location:</strong> Karachi, Pakistan</p>
            </div>
        </div>

        <div class="bg-white p-8 md:p-10 shadow-[0_4px_30px_rgba(0,0,0,0.03)] border border-gray-100">
            <form action="#" method="POST" class="space-y-6">
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Full Name</label>
                    <input type="text" required class="w-full bg-transparent border-b gold-border py-3 text-sm transition-colors" placeholder="Enter your name">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Email Address</label>
                    <input type="email" required class="w-full bg-transparent border-b gold-border py-3 text-sm transition-colors" placeholder="Enter your email">
                </div>
                <div>
                    <label class="block text-xs uppercase tracking-widest text-gray-500 mb-2">Your Message</label>
                    <textarea rows="4" required class="w-full bg-transparent border-b gold-border py-3 text-sm transition-colors resize-none" placeholder="How can we assist you?"></textarea>
                </div>
                <button type="submit" class="w-full bg-[#0a0a0a] text-white py-4 text-xs tracking-[0.2em] uppercase font-medium hover:bg-[#c49a6c] transition-colors duration-400">
                    Send Message
                </button>
            </form>
        </div>
    </div>
</main>

<?php include 'common/footer.php'; ?>