<?php
session_start();
require_once '../db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
    exit;
}

$id = (int)$_POST['id'];
$user_id = $_SESSION['user_id'];

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ!']);
    exit;
}

$query = "DELETE FROM Carts WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'ii', $id, $user_id);
$success = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($success) {
    // Xóa thông tin màu sắc và kích thước khỏi session
    $query = "SELECT product_id FROM Carts WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    if ($row && isset($_SESSION['cart_extras'][$row['product_id']])) {
        unset($_SESSION['cart_extras'][$row['product_id']]);
    }
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa sản phẩm!']);
}

mysqli_close($conn);
?>
