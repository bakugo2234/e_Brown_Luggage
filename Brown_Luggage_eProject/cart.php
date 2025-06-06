<?php
session_start();
require_once 'db_connect.php';
include 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$cart_items = [];
$total = 0;

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT c.*, p.name, p.price, pi.image_url 
              FROM Carts c 
              JOIN Products p ON c.product_id = p.id 
              LEFT JOIN Product_images pi ON p.id = pi.product_id AND pi.u_primary = 1
              WHERE c.user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt === false) {
        echo "Lỗi chuẩn bị truy vấn: " . mysqli_error($conn);
        exit;
    }
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    if (!mysqli_stmt_execute($stmt)) {
        echo "Lỗi thực thi truy vấn: " . mysqli_stmt_error($stmt);
        exit;
    }
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        // Thêm thông tin màu sắc và kích thước từ session
        $row['color'] = $_SESSION['cart_extras'][$row['product_id']]['color'] ?? 'N/A';
        $row['size'] = $_SESSION['cart_extras'][$row['product_id']]['size'] ?? 'N/A';
        $cart_items[] = $row;
        $total += $row['price'] * $row['quantity'];
    }
    mysqli_stmt_close($stmt);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $selected_items = $_POST['selected_items'] ?? [];
    if (!empty($selected_items)) {
        // Lưu thông tin sản phẩm đã chọn vào session
        $ids = implode(',', array_map('intval', $selected_items));
        $query = "SELECT c.*, p.name, p.price, pi.image_url 
                  FROM Carts c 
                  JOIN Products p ON c.product_id = p.id 
                  LEFT JOIN Product_images pi ON p.id = pi.product_id AND pi.u_primary = 1
                  WHERE c.id IN ($ids)";
        $result = mysqli_query($conn, $query);
        $checkout_items = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $row['color'] = $_SESSION['cart_extras'][$row['product_id']]['color'] ?? 'N/A';
            $row['size'] = $_SESSION['cart_extras'][$row['product_id']]['size'] ?? 'N/A';
            $checkout_items[$row['id']] = $row;
        }
        $_SESSION['checkout_items'] = $checkout_items;
        header('Location: checkout.php');
        exit;
    } else {
        $error = "Vui lòng chọn ít nhất một sản phẩm để thanh toán!";
    }
}
?>

<div class="container mx-auto my-5">
    <h1 class="text-3xl font-bold text-center mb-5">Giỏ Hàng</h1>
    <?php if (isset($error)) echo "<p class='text-red-500 text-center mb-4'>$error</p>"; ?>
    <?php if (empty($cart_items)): ?>
        <p class="text-center">Giỏ hàng của bạn trống!</p>
        <p class="text-center">
            <a href="products.php">
                <button class="bg-blue-500 text-white px-2 py-1 rounded text-sm buy-now">Mua Ngay</button>
            </a>
        </p>
    <?php else: ?>
        <form method="POST">
            <table class="table-auto w-full border">
                <thead>
                    <tr>
                        <th class="border px-4 py-2"><input type="checkbox" id="select-all"></th>
                        <th class="border px-4 py-2">Sản Phẩm</th>
                        <th class="border px-4 py-2">Hình Ảnh</th>
                        <th class="border px-4 py-2">Màu Sắc</th>
                        <th class="border px-4 py-2">Kích Thước</th>
                        <th class="border px-4 py-2">Giá</th>
                        <th class="border px-4 py-2">Số Lượng</th>
                        <th class="border px-4 py-2">Tổng</th>
                        <th class="border px-4 py-2">Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                    <tr>
                        <td class="border px-4 py-2">
                            <input type="checkbox" name="selected_items[]" value="<?php echo $item['id']; ?>" class="select-item">
                        </td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($item['name']); ?></td>
                        <td class="border px-4 py-2"><img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="Product" class="h-16"></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($item['color']); ?></td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($item['size']); ?></td>
                        <td class="border px-4 py-2"><?php echo number_format($item['price'], 0, '', '.'); ?> VNĐ</td>
                        <td class="border px-4 py-2">
                            <input type="number" class="w-16 update-cart" data-id="<?php echo $item['id']; ?>" value="<?php echo $item['quantity']; ?>" min="1">
                        </td>
                        <td class="border px-4 py-2"><?php echo number_format($item['price'] * $item['quantity'], 0, '', '.'); ?> VNĐ</td>
                        <td class="border px-4 py-2">
                            <button class="bg-red-500 text-white px-2 py-1 rounded remove-from-cart" data-id="<?php echo $item['id']; ?>">Xóa</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="d-flex justify-content-between mt-4">
                <p class="text-xl font-bold">Tổng cộng: <?php echo number_format($total, 0, '', '.'); ?> VNĐ</p>
                <button type="submit" name="checkout" class="bg-green-500 text-white px-4 py-2 rounded">Thanh Toán</button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    updateCartCount();

    $('#select-all').change(function() {
        $('.select-item').prop('checked', $(this).prop('checked'));
    });

    $('.update-cart').change(function() {
        let id = $(this).data('id');
        let quantity = $(this).val();
        $.ajax({
            url: 'api/update_cart.php',
            method: 'POST',
            data: { id: id, quantity: quantity },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || "Lỗi khi cập nhật số lượng!");
                }
            }
        });
    });

    $('.remove-from-cart').click(function() {
        let id = $(this).data('id');
        $.ajax({
            url: 'api/remove_from_cart.php',
            method: 'POST',
            data: { id: id },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message || "Lỗi khi xóa sản phẩm!");
                }
            }
        });
    });
});

function updateCartCount() {
    $.ajax({
        url: 'api/get_cart_count.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#cart-count').text(response.count);
        }
    });
}
</script>

<?php
mysqli_close($conn);
include 'includes/footer.php';
?>dmm
