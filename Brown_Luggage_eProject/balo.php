<?php
require_once 'db_connect.php';
include 'includes/header.php';

$query = "SELECT 
    p.id AS product_id,
    p.name AS product_name,
    p.price AS product_price,
    GROUP_CONCAT(c.hex_code) AS colour_hex_code, -- Đổi tên alias để rõ ràng
    pi.image_url AS product_image,
    AVG(f.rating) AS average_rating
FROM 
    Products p
   LEFT JOIN Product_colors pc ON p.id = pc.product_id
    LEFT JOIN Colors c ON pc.color_id = c.id
    LEFT JOIN Product_images pi ON p.id = pi.product_id AND pi.u_primary = 1
    LEFT JOIN Feedbacks f ON p.id = f.product_id
GROUP BY 
    p.id, p.name, p.price, pi.image_url
ORDER BY 
    p.id;";
$result = mysqli_query($conn, $query);

?>

<div class="display_products">
    <div class="w-75 mx-auto">
        <!-- Phần Trang Chủ / Vali căn trái -->
        <div class="d-flex justify-content-start mb-2">
            <p class="mb-0 text-dark">
                <a href="index.php" class="link text-dark text-decoration-none">Trang Chủ</a> / Balo
            </p>
        </div>

        <!-- Custom Nav chứa banner -->
        <div class="custom-nav bg-secondary bg-light h-auto border-1 border-secondary-subtle rounded">
            <a href="">
                <img src="image/index/banner3.jpg" alt="" class="img-fluid w-100">
            </a>

            <p class="fw-bold fs-4 mt-3 mb-3">Balo nam nữ chính hãng</p>
        </div>


        
    </div>
    <div class="w-75 mx-auto"> <!-- Add this wrapper to match the custom-nav width and centering -->
    <div class="row">
        <?php while ($product = mysqli_fetch_assoc($result)): ?>
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <a href="detail_product.php?id=<?php echo $product['product_id']; ?>">
                    <div class="card product-card w-63 h-auto">
                        <p class="card-text fw-bold small d-flex align-items-center gap-1">
                            <?php echo number_format($product['average_rating'] ?? 0); ?><i class="bi bi-star-fill"></i>
                        </p>
                        <img class="w-auto h-auto" 
                             src="<?php echo htmlspecialchars($product['product_image'] ?: 'images/index/box-01.png'); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                        <div class="card-body ">
                            <div class="color-options d-flex justify-content-center align-items-center ">
                            <?php if (!empty($product['colour_hex_code'])): ?>
                                            <?php 
                                            $colors = explode(',', $product['colour_hex_code']); 
                                            foreach ($colors as $color): 
                                            ?>
                                                <div class="color-circle" 
                                                     style="background-color: <?php echo htmlspecialchars($color); ?>; 
                                                            width: 20px; 
                                                            height: 20px; 
                                                            border-radius: 50%; 
                                                            margin-right: 10px;">
                                                </div>
                                            <?php endforeach; ?>
                            
                                        <?php endif; ?>
                            </div>
                            <h5 class="card-title fw-bold d-flex justify-content-center align-items-center"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                            <p class="card-text d-flex justify-content-center align-items-center fw-bold">
                            <?php echo number_format($product['product_price'], 0, '', '.'); ?>₫
                            </p>
                        </div>
                    </div>
                </a>
                
            </div>
        <?php endwhile; ?>

        <div class="description w-75 mx-auto mb-4">
            <p class="mb-3">Balo với những ưu điểm nổi bật đã trở thành vật dụng được mọi người ưa chuộng trong những năm qua. Tuy xuất hiện từ lâu, tuy nhiên tính thẩm mỹ của balo không được đề cao. Theo dòng thời gian phát triển, những chiếc balo thô sơ ban đầu đã có nhiều thay đổi đáng kể, trở thành phụ kiện chiếm trọn niềm tin của người dùng.</p>
            <p> Thị trường balo hiện nay khá sôi động với sự góp mặt của các thương hiệu lớn nhỏ trong và ngoài nước. Các dòng balo nam, balo nữ hiện nay có đa dạng mẫu mã, kiểu dáng, màu sắc khác nhau, phù hợp để sử dụng trong nhiều trường hợp như du lịch, đi học hoặc làm việc.</p>
            <h1 class="flex items-center text-gray-900 text-[20px] font-normal mb-2">
    <span class="text-[#2aa1c0] font-sans font-normal text-[32px] leading-none mr-2">1</span>
    Những công dụng lý tưởng của balo
  </h1>
  <p class="font-extrabold text-[14px] mb-2">1.1 Balo là món phụ kiện thích hợp cho chuyến du lịch</p>
  <p class="text-[14px] mb-2">
  Balo là món phụ kiện lý tưởng đồng hành cùng mọi người trong nhiều chuyến đi, chẳng hạn như dã ngoại, du lịch, đi phượt hoặc leo núi.
  </p>
  <p class="text-[14px] mb-2">
  Với balo, mọi người có thể tinh gọn hành lý, đồng thời có thể linh hoạt di chuyển trên nhiều địa hình khác nhau. Sở hữu tính năng chống thấm nước hoàn hảo, balo sẽ dễ dàng cùng bạn di chuyển ngay cả trong điều kiện thời tiết không thuận lợi.

  </p>

  <p class="font-extrabold text-[14px] mb-2">1.2 Sử dụng balo đi học, làm việc</p>
  <p class="text-[14px] mb-4">
  Từ học sinh, sinh viên đến dân văn phòng, hầu như ai cũng ưa chuộng balo vì tính tiện lợi của mình. Với chiếc balo, học sinh, sinh viên có thể dùng để mang theo sách vở, laptop, giáo trình, thay vì sử dụng túi xách. 
 </p>

 <p class="text-[14px] mb-4">
 Đối với học sinh, chiếc balo có thiết kế cân bằng, ổn định sẽ không ảnh hưởng đến xương và cơ. Lúc này, chiếc balo có phần quai đeo mềm, chắc chắn là lựa chọn phù hợp.

</p>


<p class="text-[14px] mb-4">
Trong khi đó, laptop là món đồ không thể thiếu đối với sinh viên và dân văn phòng. Với chiếc balo, mọi người có thể dễ dàng mang theo laptop, hạn chế tình trạng lỉnh kỉnh khi di chuyển.
</p>
<div class="justify-items-center">
<img src="image/index/model_balo.jpg" alt="Model_Balo" class="rounded-3">
<p class="fst-italic m-3">Balo là người bạn đồng hành lý tưởng mỗi lần đến trường, đi làm</p>
</div>

 <p class="font-extrabold text-[14px] mb-2">1.3 Món đồ lý tưởng cho những lần mua sắm</p>
  <p class="text-[14px] mb-2">
  Thay vì phải xách những chiếc túi lớn nhỏ khác nhau trong mỗi lần mua sắm, một chiếc balo có không gian rộng sẽ giúp bạn đựng đồ hoàn hảo.
</p>

<p class="text-[14px] mb-2">
Với thiết kế hai quai đeo sau lưng, mọi người sẽ không còn cảm giác nặng tay khi xách quá nhiều túi cùng lúc. Điều này cũng góp phần giảm thiểu số lượng rác thải nhựa ra môi trường, quả là một công đôi việc phải không?
</p>

<p class="font-extrabold text-[14px] mb-2">1.4 Khẳng định gu thời trang riêng</p>

<p class="text-[14px] mb-2">
Không chỉ là vật dụng giúp đựng đồ đạc gọn gàng, balo còn là phụ kiện giúp mọi người khẳng định gu thẩm mỹ cá nhân.
</p>

<p class="text-[14px] mb-2">
Hiện nay, hầu hết các hãng sản xuất balo không chỉ tập trung vào chất lượng mà còn chú trọng về thiết kế. Điều đó giúp mọi người có thêm nhiều lựa chọn hơn với các kiểu balo đa dạng từ thiết kế, màu sắc
</p>

<p class="font-extrabold text-[14px] mb-2">1.5 Bảo vệ thiết bị bên trong hoàn hảo</p>
<p class="text-[14px] mb-2">
Balo mang đến cho người dùng sự an tâm khi sở hữu khả năng chống thấm nước, gió bụi lý tưởng. Nhiều hãng còn tích hợp thêm một ngăn chống sốc bên trong, giúp mọi người an tâm khi đựng laptop, máy ảnh hoặc các món đồ có giá trị.
</p>
</div>
</div>    

</div>


<?php
mysqli_free_result($result);
mysqli_close($conn);
include 'includes/footer.php';
?>