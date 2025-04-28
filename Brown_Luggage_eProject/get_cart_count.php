<?php
session_start();
require_once '../db_connect.php';

header('Content-Type: application/json');

$count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT COUNT(*) as count FROM Carts WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $count = $row['count'];
    mysqli_stmt_close($stmt);
}

echo json_encode(['count' => $count]);
mysqli_close($conn);
?>
