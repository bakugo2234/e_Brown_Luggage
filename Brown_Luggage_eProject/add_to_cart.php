<?php
session_start();

// Kiểm tra trạng thái đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng']);
    exit;
}

// Kết nối database
require_once 'db_connect.php';

// Lấy dữ liệu từ yêu cầu AJAX
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$color = isset($_POST['color']) ? $_POST['color'] : '';
$size = isset($_POST['size']) ? $_POST['size'] : '';

if ($product_id <= 0 || empty($color) || empty($size)) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
}

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Tạo key duy nhất cho sản phẩm dựa trên ID, màu và kích thước
$cart_key = $product_id . '_' . $color . '_' . $size;

// Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
if (isset($_SESSION['cart'][$cart_key])) {
    // Nếu đã có, tăng số lượng
    $_SESSION['cart'][$cart_key]['quantity'] += 1;
} else {
    // Nếu chưa có, thêm mới
    $_SESSION['cart'][$cart_key] = [
        'product_id' => $product_id,
        'color' => $color,
        'size' => $size,
        'quantity' => 1
    ];
}

// Trả về phản hồi JSON
echo json_encode(['success' => true]);
?>