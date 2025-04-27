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
    $query = "SELECT c.*, p.name, p.price, p.discount, pi.image_url 
              FROM Carts c 
              JOIN Products p ON c.product_id = p.id 
              JOIN Product_Images pi ON p.id = pi.product_id 
              WHERE c.user_id = ? AND pi.is_primary = 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $cart_items);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result === false) {
        echo "Lỗi truy vấn SQL: " . mysqli_error($conn);
        exit;
    }
    while ($row = mysqli_fetch_assoc($result)) {
        $cart_items[] = $row;
        $total += ($row['price'] - $row['discount']) * $row['quantity'];
    }
} else {
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $product_ids = array_keys($_SESSION['cart']);
        $ids = implode(',', array_map('intval', $product_ids));
        if (!empty($ids)) {
            $query = "SELECT p.*, pi.image_url 
                      FROM Products p 
                      JOIN Product_Images pi ON p.id = pi.product_id 
                      WHERE p.id IN ($ids) AND pi.is_primary = 1";
            $result = mysqli_query($conn, $query);
            if ($result === false) {
                echo "Lỗi truy vấn SQL: " . mysqli_error($conn);
                exit;
            }
            while ($row = mysqli_fetch_assoc($result)) {
                $row['quantity'] = $_SESSION['cart'][$row['id']];
                $cart_items[] = $row;
                $total += ($row['price'] - $row['discount']) * $row['quantity'];
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    $selected_items = $_POST['selected_items'] ?? [];
    if (!empty($selected_items)) {
        $_SESSION['checkout_items'] = $selected_items;
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
                            <input type="checkbox" name="selected_items[]" value="<?php echo isset($item['id']) ? $item['id'] : $item['product_id']; ?>" class="select-item">
                        </td>
                        <td class="border px-4 py-2"><?php echo htmlspecialchars($item['name']); ?></td>
                        <td class="border px-4 py-2"><img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="Product" class="h-16"></td>
                        <td class="border px-4 py-2"><?php echo number_format($item['price'] - $item['discount'], 0, '', '.'); ?> VNĐ</td>
                        <td class="border px-4 py-2">
                            <input type="number" class="w-16 update-cart" data-id="<?php echo isset($item['id']) ? $item['id'] : $item['product_id']; ?>" value="<?php echo $item['quantity']; ?>" min="1">
                        </td>
                        <td class="border px-4 py-2"><?php echo number_format(($item['price'] - $item['discount']) * $item['quantity'], 0, '', '.'); ?> VNĐ</td>
                        <td class="border px-4 py-2">
                            <button class="bg-red-500 text-white px-2 py-1 rounded remove-from-cart" data-id="<?php echo isset($item['id']) ? $item['id'] : $item['product_id']; ?>">Xóa</button>
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
<a href="cart.php" class="fixed right-4 top-1/2 transform -translate-y-1/2 bg-blue-500 text-white p-4 rounded-full shadow-lg">
    <i class="fas fa-shopping-cart"></i>
    <span id="cart-count" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full px-2 py-1 text-xs">0</span>
</a>
<?php include 'includes/footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="includes/add_shop_cart.js"></script>
<script>
    $(document).ready(function() {
        updateCartCount();

        $('#select-all').change(function() {
            $('.select-item').prop('checked', $(this).prop('checked'));
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
?>