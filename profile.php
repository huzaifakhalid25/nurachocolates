<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Agar user login nahi hai, toh wapis login page par bhej do
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'common/config.php'; 

// Fetch user details from Database
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

include 'common/header.php'; 
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/vanilla-tilt/1.8.0/vanilla-tilt.min.js"></script>

<style>
    body { background-color: #faf9f6; }
    
    /* ================= VIP BLACK CARD CSS ================= */
    .vip-card {
        background: linear-gradient(135deg, #111 0%, #222 100%);
        border: 1px solid rgba(196, 154, 108, 0.3);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.4), inset 0 0 20px rgba(196, 154, 108, 0.1);
        position: relative;
        overflow: hidden;
        transform-style: preserve-3d;
    }
    
    .vip-card::before {
        content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
        background: radial-gradient(circle at center, rgba(196, 154, 108, 0.15) 0%, transparent 60%);
        opacity: 0; transition: opacity 0.5s; pointer-events: none; z-index: 1;
    }
    .vip-card:hover::before { opacity: 1; }

    /* The gold chip on the card */
    .chip {
        width: 45px; height: 35px;
        background: linear-gradient(135deg, #d4af37 0%, #aa771c 100%);
        border-radius: 6px; position: relative;
        overflow: hidden; border: 1px solid rgba(255,255,255,0.2);
    }
    .chip::after {
        content: ''; position: absolute; top: 50%; left: 0; width: 100%; height: 1px; background: rgba(0,0,0,0.2);
    }
    .chip::before {
        content: ''; position: absolute; left: 50%; top: 0; height: 100%; width: 1px; background: rgba(0,0,0,0.2);
    }
    
    .card-content { transform: translateZ(30px); z-index: 10; position: relative; }
</style>

<div class="min-h-[85vh] py-12 px-4 md:px-8 max-w-7xl mx-auto flex flex-col lg:flex-row gap-10">
    
    <div class="w-full lg:w-1/3 flex flex-col items-center">
        
        <div class="w-full max-w-[380px] aspect-[1.6/1] vip-card p-6 flex flex-col justify-between mb-8 cursor-pointer" 
             data-tilt data-tilt-max="15" data-tilt-speed="400" data-tilt-glare data-tilt-max-glare="0.3">
            
            <div class="card-content flex justify-between items-start">
                <div class="chip"></div>
                <h2 class="text-[#c49a6c] brand-font font-bold text-xl tracking-[0.2em] uppercase">Nura</h2>
            </div>
            
            <div class="card-content">
                <p class="text-gray-400 text-[9px] uppercase tracking-[0.2em] mb-1">Cardholder Name</p>
                <h3 class="text-white text-lg md:text-xl font-bold tracking-widest uppercase truncate">
                    <?php echo htmlspecialchars($user['full_name']); ?>
                </h3>
            </div>
            
            <div class="card-content flex justify-between items-end">
                <div>
                    <p class="text-gray-400 text-[9px] uppercase tracking-[0.2em] mb-1">Member Since</p>
                    <p class="text-white text-sm tracking-widest font-mono">
                        <?php echo date('m / y', strtotime($user['created_at'])); ?>
                    </p>
                </div>
                <div class="text-[#c49a6c] font-bold text-xs tracking-widest">VIP MEMBER</div>
            </div>
        </div>

        <div class="w-full max-w-[380px] bg-white rounded-3xl p-6 shadow-[0_10px_40px_rgba(0,0,0,0.03)] border border-gray-50">
            <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Account Settings</h3>
            
            <div class="space-y-4">
                <div class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-3 text-[#c49a6c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    <span class="text-sm font-medium truncate"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="flex items-center text-gray-600">
                    <svg class="w-5 h-5 mr-3 text-[#c49a6c]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    <span class="text-sm font-medium">Password: ••••••••</span>
                    <a href="#" class="ml-auto text-[10px] uppercase font-bold text-gray-400 hover:text-black transition">Change</a>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100">
                <a href="logout.php" class="w-full block text-center border border-red-100 bg-red-50 text-red-500 py-3 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-red-500 hover:text-white transition shadow-sm">
                    Sign Out
                </a>
            </div>
        </div>
    </div>

    <div class="w-full lg:w-2/3">
        <div class="bg-white rounded-3xl p-8 shadow-[0_10px_40px_rgba(0,0,0,0.03)] border border-gray-50 h-full">
            <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-4">
                <h2 class="text-3xl brand-font font-bold text-gray-900">Your Journey</h2>
                <span class="text-[10px] text-[#c49a6c] uppercase tracking-widest font-bold bg-[#c49a6c]/10 px-3 py-1 rounded-full">Order History</span>
            </div>

            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <h3 class="text-xl brand-font font-bold text-gray-900 mb-2">No orders yet</h3>
                <p class="text-gray-400 text-sm max-w-sm mx-auto leading-relaxed mb-8">Your luxury chocolate experience awaits. Discover our exquisite collection and treat yourself.</p>
                <a href="index.php#products-wrapper" class="bg-black text-white px-8 py-3.5 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-[#c49a6c] transition shadow-lg">
                    Start Shopping
                </a>
            </div>

            </div>
    </div>
</div>

<?php include 'common/footer.php'; ?>