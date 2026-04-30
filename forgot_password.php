<?php 
require_once 'common/config.php';
include 'common/header.php'; 
?>

<div class="min-h-[80vh] flex items-center justify-center px-4 bg-[#faf9f6] py-12">
    <div class="max-w-md w-full bg-white p-10 rounded-[20px] shadow-[0_15px_40px_rgba(0,0,0,0.08)] relative overflow-hidden">
        
        <div class="absolute top-0 left-0 w-full h-1 bg-[#c49a6c]"></div>

        <div class="text-center mb-8 mt-2">
            <h2 class="text-3xl brand-font font-bold text-gray-900 mb-3">Reset Password</h2>
            <p class="text-sm text-gray-400 font-light leading-relaxed px-2">
                Enter the email address associated with your account, and we’ll send you a link to reset your password.
            </p>
        </div>
        
        <form action="auth_logic.php" method="POST" class="space-y-8">
            <div class="w-full">
                <label class="text-[10px] uppercase tracking-widest text-gray-400 font-bold block mb-2">Email Address</label>
                <input type="email" name="reset_email" class="w-full border-b border-gray-200 py-2 focus:border-[#c49a6c] outline-none transition bg-transparent text-base" placeholder="user@nura.com" required>
            </div>
            
            <button type="submit" name="forgot_password" class="w-full bg-black text-white py-4 rounded-full font-bold uppercase text-xs tracking-widest hover:bg-[#c49a6c] transition shadow-md">
                Send Reset Link
            </button>
        </form>
        
        <div class="mt-8 text-center border-t border-gray-50 pt-6">
            <p class="text-sm text-gray-500">
                Remember your password? 
                <a href="login.php" class="font-bold text-[#c49a6c] hover:text-black transition ml-1">Back to Login</a>
            </p>
        </div>
        
    </div>
</div>

<?php include 'common/footer.php'; ?>