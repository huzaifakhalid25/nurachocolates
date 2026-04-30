<?php
// Session start karo taake current session access ho sake
session_start();

// Saare session variables ko clear kar do
$_SESSION = array();

// Session ko mukammal taur par destroy kar do
session_destroy();

require_once 'common/config.php'; 
include 'common/header.php'; 
?>

<style>
    /* ================= 3D Dark Theme ================= */
    .logout-section {
        background: radial-gradient(circle at center, #1a1a2e 0%, #0f0f1a 100%);
        min-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .logout-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(196, 154, 108, 0.3);
        border-radius: 20px;
        padding: 60px 40px;
        text-align: center;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5), inset 0 0 20px rgba(196, 154, 108, 0.1);
        max-width: 450px;
        width: 100%;
        /* Fade in smoothly */
        animation: fadeInCard 0.8s ease-out forwards;
        opacity: 0;
        transform: translateY(30px);
    }

    @keyframes fadeInCard {
        to { opacity: 1; transform: translateY(0); }
    }

    /* Golden Spinner Animation */
    .spinner {
        border: 3px solid rgba(196, 154, 108, 0.2);
        border-top: 3px solid #c49a6c;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 1s linear infinite;
        margin: 0 auto 25px auto;
    }

    @keyframes spin { 
        0% { transform: rotate(0deg); } 
        100% { transform: rotate(360deg); } 
    }
</style>

<div class="logout-section">
    <div class="logout-card">
        <div class="spinner"></div>
        
        <h2 class="text-3xl md:text-4xl brand-font font-bold text-[#c49a6c] mb-4 drop-shadow-md">See You Soon</h2>
        <p class="text-gray-300 text-sm mb-8 font-light leading-relaxed">You have been successfully logged out of your Nura luxury account.</p>
        
        <div class="w-full h-[1px] bg-gradient-to-r from-transparent via-[#c49a6c]/50 to-transparent mb-6"></div>
        
        <p class="text-[10px] text-[#00f0ff] uppercase tracking-[0.2em] font-bold animate-pulse">Redirecting to homepage...</p>
    </div>
</div>

<script>
    // 3 seconds (3000 milliseconds) ke baad automatically Home Page par bhej do
    setTimeout(function() {
        window.location.href = 'index.php';
    }, 3000);
</script>

<?php include 'common/footer.php'; ?>