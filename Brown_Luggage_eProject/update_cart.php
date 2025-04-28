
<?php
session_start();
require_once '../db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập!']);
    exit;
}

$id = (int)$_POST['id'];
$quantity = (int)$_POST['quantity'];
$user_id = $_SESSION['user_id'];

if ($id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ!']);
    exit;
}

$query = "UPDATE Carts SET quantity = ? WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'iii', $quantity, $id, $user_id);
$success = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật số lượng!']);
}

mysqli_close($conn);
?>
