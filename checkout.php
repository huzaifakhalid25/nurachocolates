<?php
session_start();
require_once 'common/config.php'; 
include 'common/header.php'; 

// Agar cart khali hai toh wapas index par bhej do
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo '<div class="min-h-[70vh] flex flex-col items-center justify-center bg-[#faf9f6] pt-20">
            <h2 class="text-3xl brand-font font-bold text-gray-800 mb-4">Your Cart is Empty</h2>
            <p class="text-gray-500 mb-8">Looks like you haven\'t added any luxury chocolates yet.</p>
            <a href="index.php" class="bg-black text-[#c49a6c] px-8 py-3 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-[#c49a6c] hover:text-black transition">Return to Shop</a>
          </div>';
    include 'common/footer.php';
    exit;
}
?>

<div class="bg-[#faf9f6] min-h-screen pt-32 pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl brand-font font-bold text-gray-900 mb-3">Secure Checkout</h1>
            <p class="text-[#c49a6c] uppercase tracking-[0.2em] text-xs font-bold">Complete your luxury order</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-10">
            
            <div class="lg:w-2/3 bg-white p-6 md:p-10 rounded-3xl shadow-sm border border-gray-100">
                <h2 class="text-xl brand-font font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Shipping Information</h2>
                
                <form action="place_order.php" method="POST" id="checkout-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Full Name *</label>
                            <input type="text" name="full_name" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#c49a6c] focus:ring-1 focus:ring-[#c49a6c] transition bg-gray-50 focus:bg-white" placeholder="John Doe">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Phone Number *</label>
                            <input type="tel" name="phone" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#c49a6c] focus:ring-1 focus:ring-[#c49a6c] transition bg-gray-50 focus:bg-white" placeholder="03XX-XXXXXXX">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Email Address (Optional)</label>
                        <input type="email" name="email" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#c49a6c] focus:ring-1 focus:ring-[#c49a6c] transition bg-gray-50 focus:bg-white" placeholder="john@example.com">
                    </div>

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Delivery Address *</label>
                        <textarea name="address" required rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#c49a6c] focus:ring-1 focus:ring-[#c49a6c] transition bg-gray-50 focus:bg-white resize-none" placeholder="House/Apartment, Street, Area"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">City *</label>
                            <select name="city" required class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:border-[#c49a6c] focus:ring-1 focus:ring-[#c49a6c] transition bg-gray-50 focus:bg-white appearance-none">
                                <option value="" disabled selected>Select your city</option>
                                <option value="Karachi">Karachi</option>
                                <option value="Lahore">Lahore</option>
                                <option value="Islamabad">Islamabad</option>
                                <option value="Rawalpindi">Rawalpindi</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>

                    <h2 class="text-xl brand-font font-bold text-gray-900 mb-6 border-b border-gray-100 pb-4">Payment Method</h2>
                    <div class="mb-8">
                        <label class="flex items-center gap-4 p-4 border-2 border-[#c49a6c] bg-[#c49a6c]/5 rounded-xl cursor-pointer">
                            <input type="radio" name="payment_method" value="COD" checked class="w-5 h-5 text-[#c49a6c] focus:ring-[#c49a6c]">
                            <div>
                                <p class="font-bold text-gray-900">Cash on Delivery (COD)</p>
                                <p class="text-xs text-gray-500 mt-1">Pay when you receive your order</p>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="lg:hidden w-full bg-black text-[#c49a6c] py-4 rounded-full font-bold uppercase tracking-widest hover:bg-[#c49a6c] hover:text-black transition shadow-[0_10px_20px_rgba(0,0,0,0.2)]">
                        Confirm Order
                    </button>

                </form>
            </div>

            <div class="lg:w-1/3">
                <div class="bg-black text-white p-6 md:p-8 rounded-3xl shadow-2xl sticky top-24">
                    <h2 class="text-2xl brand-font font-bold mb-6 text-[#c49a6c]">Order Summary</h2>
                    
                    <div class="space-y-4 mb-6 max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar">
                        <?php 
                        $subtotal = 0;
                        foreach ($_SESSION['cart'] as $id => $item): 
                            $item_total = $item['price'] * $item['qty'];
                            $subtotal += $item_total;
                        ?>
                        <div class="flex justify-between items-center gap-4">
                            <div class="flex items-center gap-3">
                                <div class="relative">
                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" class="w-12 h-12 object-cover rounded-lg border border-gray-700">
                                    <span class="absolute -top-2 -right-2 bg-[#c49a6c] text-black text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full"><?php echo $item['qty']; ?></span>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-200 line-clamp-1"><?php echo htmlspecialchars($item['name']); ?></p>
                                </div>
                            </div>
                            <p class="text-sm font-bold text-[#c49a6c]">Rs. <?php echo number_format($item_total, 2); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="border-t border-gray-800 pt-4 space-y-3 mb-6">
                        <div class="flex justify-between text-sm text-gray-400">
                            <span>Subtotal</span>
                            <span>Rs. <?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-400">
                            <span>Shipping</span>
                            <span class="text-[#c49a6c] font-bold">Calculated at next step</span>
                        </div>
                    </div>

                    <div class="border-t border-[#c49a6c]/30 pt-4 mb-8">
                        <div class="flex justify-between items-end">
                            <span class="text-lg font-bold">Total</span>
                            <span class="text-2xl font-bold text-[#c49a6c]">Rs. <?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <p class="text-[10px] text-gray-500 mt-1 text-right">Including all taxes</p>
                    </div>

                    <button type="submit" form="checkout-form" class="hidden lg:block w-full bg-[#c49a6c] text-black py-4 rounded-full font-bold uppercase tracking-widest hover:bg-white transition shadow-[0_0_20px_rgba(196,154,108,0.3)]">
                        Place Order
                    </button>
                    
                    <div class="mt-6 flex items-center justify-center gap-2 text-gray-500 text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        <span>256-bit Secure SSL Checkout</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar for Order Summary */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #c49a6c; border-radius: 10px; }
</style>

<?php include 'common/footer.php'; ?>