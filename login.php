<?php 
require_once 'common/config.php';
include 'common/header.php'; 
?>

<style>
    .auth-section { background-color: #faf9f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 100px 20px 40px 20px; overflow: hidden; }
    .auth-container { background-color: #fff; border-radius: 20px; box-shadow: 0 15px 40px rgba(0,0,0,0.08); position: relative; overflow: hidden; width: 100%; max-width: 900px; min-height: 550px; }
    .form-container { position: absolute; top: 0; height: 100%; transition: all 0.6s ease-in-out; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 0 40px; background-color: #fff; }
    .sign-in-container { left: 0; width: 50%; z-index: 2; opacity: 1; }
    .sign-up-container { left: 0; width: 50%; opacity: 0; z-index: 1; }
    .auth-container.right-panel-active .sign-in-container { transform: translateX(100%); opacity: 0; z-index: 1; }
    .auth-container.right-panel-active .sign-up-container { transform: translateX(100%); opacity: 1; z-index: 5; animation: show 0.6s; }
    @keyframes show { 0%, 49.99% { opacity: 0; z-index: 1; } 50%, 100% { opacity: 1; z-index: 5; } }
    .overlay-container { position: absolute; top: 0; left: 50%; width: 50%; height: 100%; overflow: hidden; transition: transform 0.6s ease-in-out; z-index: 100; }
    .auth-container.right-panel-active .overlay-container { transform: translateX(-100%); }
    .overlay { background-color: #1a1a1a; color: #fff; position: absolute; left: -100%; height: 100%; width: 200%; transform: translateX(0); transition: transform 0.6s ease-in-out; }
    .auth-container.right-panel-active .overlay { transform: translateX(50%); }
    .overlay-panel { position: absolute; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 0 40px; text-align: center; top: 0; height: 100%; width: 50%; transform: translateX(0); transition: transform 0.6s ease-in-out; background-size: cover; background-position: center; background-repeat: no-repeat; }
    .overlay-left { transform: translateX(-20%); background-image: linear-gradient(to right, rgba(26, 26, 26, 0.85), rgba(26, 26, 26, 0.7)), url('nura1.jpg'); }
    .auth-container.right-panel-active .overlay-left { transform: translateX(0); }
    .overlay-right { right: 0; transform: translateX(0); background-image: linear-gradient(to right, rgba(26, 26, 26, 0.85), rgba(26, 26, 26, 0.7)), url('nura2.jpg'); }
    .auth-container.right-panel-active .overlay-right { transform: translateX(20%); }
    @media (max-width: 768px) { .auth-container { min-height: 450px; } .form-container { padding: 0 20px; } .overlay-panel { padding: 0 20px; } }
</style>

<div class="auth-section">
    <div class="auth-container" id="authContainer">
        
        <div class="form-container sign-up-container">
            <form action="auth_logic.php" method="POST" class="w-full">
                <h2 class="text-2xl md:text-4xl brand-font font-bold text-gray-900 text-center mb-6 md:mb-8">Create Account</h2>
                <div class="w-full mb-3 md:mb-4">
                    <label class="text-[9px] md:text-[10px] uppercase tracking-widest text-gray-400 font-bold block mb-1">Full Name</label>
                    <input type="text" name="name" class="w-full border-b border-gray-200 py-2 focus:border-[#c49a6c] outline-none transition bg-transparent text-sm md:text-base" placeholder="John Doe" required>
                </div>
                <div class="w-full mb-3 md:mb-4">
                    <label class="text-[9px] md:text-[10px] uppercase tracking-widest text-gray-400 font-bold block mb-1">Email</label>
                    <input type="email" name="email" class="w-full border-b border-gray-200 py-2 focus:border-[#c49a6c] outline-none transition bg-transparent text-sm md:text-base" placeholder="user@nura.com" required>
                </div>
                <div class="w-full mb-6 md:mb-8 relative">
                    <label class="text-[9px] md:text-[10px] uppercase tracking-widest text-gray-400 font-bold block mb-1">Password</label>
                    <input type="password" name="password" id="reg-pass" class="w-full border-b border-gray-200 py-2 focus:border-[#c49a6c] outline-none transition bg-transparent text-sm md:text-base pr-8" placeholder="••••••••" required>
                    <button type="button" onclick="togglePassword('reg-pass')" class="absolute right-0 bottom-2 text-gray-400 hover:text-[#c49a6c] transition">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                </div>
                <button type="submit" name="register" class="w-full bg-black text-white py-3 md:py-4 rounded-full font-bold uppercase text-[10px] md:text-xs tracking-widest hover:bg-[#c49a6c] transition shadow-md">Sign Up</button>
            </form>
        </div>

        <div class="form-container sign-in-container">
            <form action="auth_logic.php" method="POST" class="w-full">
                <h2 class="text-2xl md:text-4xl brand-font font-bold text-gray-900 text-center mb-6 md:mb-8">Sign In</h2>
                <div class="w-full mb-4 md:mb-5">
                    <label class="text-[9px] md:text-[10px] uppercase tracking-widest text-gray-400 font-bold block mb-1">Email Address</label>
                    <input type="email" name="email" class="w-full border-b border-gray-200 py-2 focus:border-[#c49a6c] outline-none transition bg-transparent text-sm md:text-base" placeholder="user@nura.com" required>
                </div>
                <div class="w-full mb-2 relative">
                    <label class="text-[9px] md:text-[10px] uppercase tracking-widest text-gray-400 font-bold block mb-1">Password</label>
                    <input type="password" name="password" id="login-pass" class="w-full border-b border-gray-200 py-2 focus:border-[#c49a6c] outline-none transition bg-transparent text-sm md:text-base pr-8" placeholder="••••••••" required>
                    <button type="button" onclick="togglePassword('login-pass')" class="absolute right-0 bottom-2 text-gray-400 hover:text-[#c49a6c] transition">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                </div>
                <div class="w-full text-right mb-6 md:mb-8 mt-1">
                    <a href="forgot_password.php" class="text-[10px] md:text-xs text-gray-400 hover:text-[#c49a6c] transition font-medium">Forgot Password?</a>
                </div>
                <button type="submit" name="login" class="w-full bg-black text-white py-3 md:py-4 rounded-full font-bold uppercase text-[10px] md:text-xs tracking-widest hover:bg-[#c49a6c] transition shadow-md">Login</button>
            </form>
        </div>

        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h2 class="text-2xl md:text-4xl brand-font font-bold text-[#c49a6c] mb-2 md:mb-4">Welcome Back!</h2>
                    <p class="text-xs md:text-sm text-gray-200 font-light mb-6 md:mb-8 leading-relaxed">To keep connected with us please login with your personal info.</p>
                    <button class="border border-white bg-transparent text-white py-2 px-8 md:py-3 md:px-10 rounded-full font-bold uppercase text-[10px] md:text-xs tracking-widest hover:bg-white hover:text-black transition" id="signInBtn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h2 class="text-2xl md:text-4xl brand-font font-bold text-[#c49a6c] mb-2 md:mb-4">Hello, Friend!</h2>
                    <p class="text-xs md:text-sm text-gray-200 font-light mb-6 md:mb-8 leading-relaxed">Enter your details and start your luxury journey with Nura.</p>
                    <button class="border border-white bg-transparent text-white py-2 px-8 md:py-3 md:px-10 rounded-full font-bold uppercase text-[10px] md:text-xs tracking-widest hover:bg-white hover:text-black transition" id="signUpBtn">Sign Up</button>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    const signUpButton = document.getElementById('signUpBtn');
    const signInButton = document.getElementById('signInBtn');
    const container = document.getElementById('authContainer');

    signUpButton.addEventListener('click', () => { container.classList.add("right-panel-active"); });
    signInButton.addEventListener('click', () => { container.classList.remove("right-panel-active"); });

    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const btn = input.nextElementSibling;
        if (input.type === "password") {
            input.type = "text";
            btn.classList.add("text-[#c49a6c]"); 
            btn.innerHTML = `<svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>`;
        } else {
            input.type = "password";
            btn.classList.remove("text-[#c49a6c]");
            btn.innerHTML = `<svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>`;
        }
    }
</script>

<?php include 'common/footer.php'; ?>