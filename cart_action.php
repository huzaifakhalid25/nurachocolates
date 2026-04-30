<?php
ob_start(); 
session_start();
error_reporting(E_ALL); // Testing ke waqt errors on rakhein taake masla pakra jaye

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Helper function to calculate totals consistently
function getCartStats() {
    $total_price = 0;
    $total_items = 0;
    foreach ($_SESSION['cart'] as $item) {
        $p = floatval($item['price'] ?? $item['product_price'] ?? 0);
        $q = intval($item['quantity'] ?? $item['qty'] ?? 0);
        $total_price += ($p * $q);
        $total_items += $q;
    }
    return [
        'total_price' => number_format($total_price, 2, '.', ''), // Clean number for JS
        'display_total' => number_format($total_price, 0), // Formatted for display
        'total_items' => $total_items
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ================= 1. ADD TO CART =================
    if ($action === 'add') {
        $id = intval($_POST['product_id']);
        $name = trim($_POST['product_name']);
        
        // Price se comma ya extra characters hatana
        $price_raw = str_replace([',', 'Rs.', 'PKR', ' '], '', $_POST['product_price']);
        $price = floatval($price_raw);
        
        $image = trim($_POST['product_image']);
        $qty = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
        $bundle_qty = isset($_POST['bundle_qty']) ? intval($_POST['bundle_qty']) : 1; // Bundle flag get kiya

        if ($id > 0) {
            // UNIQUE CART KEY (ID + Price) taake bundle aur single mix na hon!
            $cart_key = $id . '_' . str_replace('.', '', $price);

            if (isset($_SESSION['cart'][$cart_key])) {
                $_SESSION['cart'][$cart_key]['quantity'] += $qty;
                $_SESSION['cart'][$cart_key]['price'] = $price; 
            } else {
                $_SESSION['cart'][$cart_key] = [
                    'id' => $id,
                    'name' => $name,
                    'price' => $price,
                    'image' => $image,
                    'quantity' => $qty,
                    'bundle_qty' => $bundle_qty // Bundle item pehchanne ke liye
                ];
            }
        }

        $stats = getCartStats();
        ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success', 
            'total_items' => $stats['total_items'],
            'total_price' => $stats['display_total']
        ]);
        exit;
    }

    // ================= 2. GET CART ITEMS =================
    if ($action === 'get') {
        $html = '';
        $stats = getCartStats();
        $total_val = 0;

        if (empty($_SESSION['cart'])) {
            $html = '<p class="text-gray-400 text-center mt-10 text-sm font-medium">Your cart is currently empty.</p>';
        } else {
            foreach ($_SESSION['cart'] as $cart_key => $item) {
                $p = floatval($item['price'] ?? 0);
                $q = intval($item['quantity'] ?? 0);
                $subtotal = $p * $q;
                $total_val += $subtotal;

                // Bundle Badge Logic
                $bundle_tag = '';
                if (isset($item['bundle_qty']) && $item['bundle_qty'] > 1) {
                    $bundle_tag = '
                    <div class="flex items-center gap-1 mt-1 text-gray-600 mb-1">
                        <svg class="w-3 h-3 text-black" fill="currentColor" viewBox="0 0 24 24"><path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"></path></svg>
                        <span class="text-[11px] font-medium">Bundle and save</span>
                    </div>';
                }
                
                $html .= '
                <div class="flex items-center gap-4 mb-4 border-b border-gray-100 pb-4">
                    <img src="'.htmlspecialchars($item['image']).'" class="w-16 h-16 object-cover rounded-xl shadow-sm border border-gray-50">
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-gray-800 leading-tight">'.htmlspecialchars($item['name']).'</h4>
                        <p class="text-xs text-[#c49a6c] font-bold mt-1">Rs. '.number_format($p, 0).'</p>
                        '.$bundle_tag.'
                        <div class="flex items-center gap-3 mt-2">
                            <button type="button" onclick="updateCartQty(\''.$cart_key.'\', \'minus\')" class="w-6 h-6 flex items-center justify-center bg-gray-100 rounded-full hover:bg-black hover:text-white transition font-bold cursor-pointer">-</button>
                            <span class="text-sm font-bold text-gray-700">'.$q.'</span>
                            <button type="button" onclick="updateCartQty(\''.$cart_key.'\', \'plus\')" class="w-6 h-6 flex items-center justify-center bg-gray-100 rounded-full hover:bg-black hover:text-white transition font-bold cursor-pointer">+</button>
                        </div>
                    </div>
                    <button type="button" onclick="removeFromCart(\''.$cart_key.'\')" class="text-red-400 hover:text-red-600 transition p-2 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>';
            }
        }

        ob_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success',
            'html' => $html,
            'total_price' => number_format($total_val, 0),
            'total_items' => $stats['total_items']
        ]);
        exit;
    }

    // ================= 3. UPDATE/REMOVE =================
    if ($action === 'update') {
        $cart_key = $_POST['cart_key']; 
        $type = $_POST['type']; 
        if (isset($_SESSION['cart'][$cart_key])) {
            if ($type === 'plus') $_SESSION['cart'][$cart_key]['quantity'] += 1;
            elseif ($type === 'minus') {
                $_SESSION['cart'][$cart_key]['quantity'] -= 1;
                if ($_SESSION['cart'][$cart_key]['quantity'] <= 0) unset($_SESSION['cart'][$cart_key]);
            }
        }
        ob_clean();
        header('Content-Type: application/json'); echo json_encode(['status' => 'success']); exit;
    }

    if ($action === 'remove') {
        $cart_key = $_POST['cart_key'];
        unset($_SESSION['cart'][$cart_key]);
        ob_clean();
        header('Content-Type: application/json'); echo json_encode(['status' => 'success']); exit;
    }
}