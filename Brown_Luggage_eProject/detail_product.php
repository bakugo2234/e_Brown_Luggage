<?php
require_once 'db_connect.php';
include 'includes/header.php';

// Lấy product_id từ GET và đảm bảo là số nguyên
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id <= 0) {
    die("ID sản phẩm không hợp lệ");
}

// Truy vấn thông tin sản phẩm
$product_query = "SELECT 
    p.id AS product_id,
    p.name AS product_name,
    p.category_id,
    cat.name AS category_name,
    p.brand_id,
    brands.name AS brand_name,
    GROUP_CONCAT(DISTINCT sizes.name ORDER BY sizes.name) AS size_names,
    p.price AS product_price,
    GROUP_CONCAT(c.hex_code) AS colour_hex_code,
    pi.image_url AS product_image,
    AVG(f.rating) AS average_rating
FROM 
    Products p
    LEFT JOIN categories cat ON p.category_id = cat.id
    LEFT JOIN brands ON p.brand_id = brands.id
    LEFT JOIN Product_Sizes ps ON p.id = ps.product_id
    LEFT JOIN Sizes sizes ON ps.size_id = sizes.id
    LEFT JOIN Product_colors pc ON p.id = pc.product_id
    LEFT JOIN Colors c ON pc.color_id = c.id
    LEFT JOIN Product_images pi ON p.id = pi.product_id AND pi.u_primary = 1
    LEFT JOIN Feedbacks f ON p.id = f.product_id AND f.status = 'approved'
WHERE 
    p.id = ?
GROUP BY 
    p.id, p.name, p.price, 
    p.category_id, cat.name, p.brand_id, brands.name, pi.image_url
ORDER BY 
    p.id";

$product_stmt = mysqli_prepare($conn, $product_query);
if (!$product_stmt) {
    die("Lỗi chuẩn bị truy vấn sản phẩm: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($product_stmt, 'i', $product_id);
if (!mysqli_stmt_execute($product_stmt)) {
    die("Lỗi thực thi truy vấn sản phẩm: " . mysqli_stmt_error($product_stmt));
}
$product_result = mysqli_stmt_get_result($product_stmt);
$product = mysqli_fetch_assoc($product_result);

if (!$product) {
    die("Không tìm thấy sản phẩm với ID: " . $product_id);
}

// Truy vấn phản hồi
$feedback_query = "SELECT f.*, u.name 
                  FROM Feedbacks f 
                  JOIN Users u ON f.user_id = u.id 
                  WHERE f.product_id = ? AND f.status = 'approved' 
                  ORDER BY f.created_at DESC";
$feedback_stmt = mysqli_prepare($conn, $feedback_query);
if (!$feedback_stmt) {
    die("Lỗi chuẩn bị truy vấn phản hồi: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($feedback_stmt, 'i', $product_id);
if (!mysqli_stmt_execute($feedback_stmt)) {
    die("Lỗi thực thi truy vấn phản hồi: " . mysqli_stmt_error($feedback_stmt));
}
$feedback_result = mysqli_stmt_get_result($feedback_stmt);
?>

    <div class="display_products">
        <div class="w-75 mx-auto">
            <!-- Breadcrumb động -->
            <div class="d-flex justify-content-start mb-2">
                <p class="mb-0 text-dark">
                    <a href="index.php" class="link text-dark text-decoration-none">Trang Chủ</a> / 
                    <a href="index.php?category_id=<?php echo $product['category_id']; ?>" class="link text-dark text-decoration-none">
                        <?php echo htmlspecialchars($product['category_name'] ?? 'Danh mục không xác định'); ?>
                    </a>
                </p>
            </div>

            <!-- Chi tiết sản phẩm -->
            <div>
            <?php if ($product): ?>
        
        <div class="row">
            <div class="col-md-6">
            <img class="w-100 h-auto " 
                             src="<?php echo htmlspecialchars($product['product_image'] ?: 'images/index/box-01.png'); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>">            
            </div>
            <div class="col-md-6">
                <p><strong>Thương hiệu:</strong> <?php echo htmlspecialchars($product['brand_name']); ?></p>
                
                <div class="col-md-6">
                <h1 class="mb-4"><?php echo htmlspecialchars($product['product_name']); ?></h1>
                
                
                <p class="card-text fw-bold small d-flex align-items-center gap-1"> <!-- Rating -->
                        <?php echo number_format($product['average_rating'] ?? 0); ?><i class="bi bi-star-fill"></i>
                </p>

    <p><strong>Mã màu:</strong> 
                <!-- hiển thị màu -->
        <div class="color-options d-flex justify-content-center align-items-center"> 
                            <?php if (!empty($product['colour_hex_code'])): ?>
                                <?php 
                                           $colors = array_unique(array_map('trim', explode(',', $product['colour_hex_code'])));
                                            foreach ($colors as $color): 
                                            ?>
                                    <button class="color-circle" 
                                            style="background-color: <?php echo htmlspecialchars($color); ?>; 
                                                   width: 20px; 
                                                   height: 20px; 
                                                   border-radius: 50%; 
                                                   margin-right: 10px; 
                                                   border: none; 
                                                   cursor: pointer;"
                                            onclick="selectColor(<?php echo $product['product_id']; ?>, '<?php echo htmlspecialchars($color); ?>')"
                                            title="Chọn màu ">
                                    </button>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span>Không có màu</span>
                            <?php endif; ?>
                        </div>

                    <!-- Hiển thị size -->
                        <p><strong>Kích thước:</strong> 
                                <div class="size-options d-flex align-items-center">
                                    <?php if (!empty($product['size_names'])): ?>
                                        <?php $sizes = explode(',', $product['size_names']); ?>
                                        <?php foreach ($sizes as $size): ?>
                                            <button class="size-button ml-3 bg-light p-3 border border-danger" 
                                                    onclick="selectSize(<?php echo $product['product_id']; ?>, '<?php echo htmlspecialchars($size); ?>', this)"
                                                    title="Chọn kích thước <?php echo htmlspecialchars($size); ?>">
                                                <?php echo htmlspecialchars($size); ?>
                                            </button>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span>Không có kích thước</span>
                                    <?php endif; ?>
                                </div>
                            </p>
                    
                    
                  <!-- Hiển thị giá tiền -->
                <p><strong>Giá:</strong>
                    <div class="fs-1 fw-bold text-danger">
                        <?php echo number_format($product['product_price'], 0, '', '.'); ?>₫
                    </div>
                </p>
                <!-- Trong phần chi tiết sản phẩm -->
                <button class="btn btn-danger add-to-cart" 
                        data-id="<?php echo $product['product_id']; ?>" 
                        onclick="addToCart(<?php echo $product['product_id']; ?>)">MUA NGAY
                </button>
           
            </div>
        </div>

        
       
        <?php else: ?>
            <div class="alert alert-danger">Sản phẩm không tồn tại hoặc đã hết hàng.</div>
        <?php endif; ?>
    </div> 
    </div> 
    </div> 
    <script src="includes/add_shop_cart.js"></script>

    <!-- Đóng tài nguyên -->
    <?php
    mysqli_stmt_close($product_stmt);
    mysqli_stmt_close($feedback_stmt);
    mysqli_free_result($product_result);
    mysqli_free_result($feedback_result);
    mysqli_close($conn);
    include 'includes/footer.php';
    
    ?>
