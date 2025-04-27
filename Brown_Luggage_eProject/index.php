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
<div class="custom-nav bg-secondary bg-light h-auto w-75 mx-auto justify-items-center   border-1 border-secondary-subtle rounded">
    
<a href=""><img src="image/index/banner.jpg" alt=""></a>

        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
    <div class="carousel-item active">
            <div class="row g-0">
                <!-- Hình ảnh 1: Larita Soly -->
                <div class="col-6">
                    <img src="image/index/home-box-1.jpg" class="d-block w-100" alt="Vali Larita Soly">
                </div>
                <!-- Hình ảnh 2: Larita Manzo -->
                <div class="col-6">
                    <img src="image/index/home-box-2.jpg" class="d-block w-100" alt="Vali Larita Manzo">
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="row g-0">
                <div class="col-6">
                    <img src="image/index/home-box-3.jpg" class="d-block w-100" alt="Slide 3">
                </div>
                <div class="col-6">
                    <img src="image/index/home-box-4.jpg" class="d-block w-100" alt="Slide 4">
                </div>
            </div>
        </div>
    </div>
   
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>





        </a>
            
        <ul class="nav justify-content-center d-flex justify-content-evenly align-items-center ">
            <li class="nav-item ">
            <a class="nav-link" href="#"><img class="img-fluid w-auto h-12" src="image/index/banner2.jpg" alt=""></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active text-dark fw-bold" href="#">VALI</a>
            </li>
            <li class="nav-item w-auto h-auto">
                <a class="nav-link text-dark fw-bold" href="#">BALO</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark fw-bold" href="#">TÚI XÁCH</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark fw-bold" href="#">PHỤ KIỆN</a>
            </li>
        
         </ul>
         
    </div>

    


<!-- Row aligned with Custom Nav -->
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
        
            <div class="col-lg-4 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 border-0">
                    <img src="image/index/box-01.png" class="card-img-top img-fluid" alt="Strength 1">
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 border-0">
                    <img src="image/index/box-02.png" class="card-img-top img-fluid" alt="Strength 2">
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 mb-4">
                <div class="card h-100 border-0">
                    <img src="image/index/box-03.png" class="card-img-top img-fluid" alt="Strength 3">
                </div>
            </div>
     

        
            <div class="col-lg-6 col-md-6 col-sm-6 mb-3">
            <div class="card h-100 border-0">
                <img src="image/index/model1.jpg" class="card-img-top img-fluid" alt="Model 1">
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 mb-3">
            <div class="card h-100 border-0">
                <img src="image/index/model2.jpg" class="card-img-top img-fluid" alt="Model 2">
            </div>
        </div>

        <div class="row">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card h-100 border-0 bg-light justify-content-center align-items-center">
                <img src="image/index/brand1.jpg" class="card-img-top img-fluid h-auto w-20 " alt="Brand 1">
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card h-100 border-0 bg-light justify-content-center align-items-center">
                <img src="image/index/brand2.png" class="card-img-top img-fluid" alt="Brand 2">
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card h-100 border-0 bg-light justify-content-center align-items-center">
                <img src="image/index/brand3.jpg" class="card-img-top img-fluid" alt="Brand 3">
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card h-100 border-0 bg-light justify-content-center align-items-center">
                <img src="image/index/brand4.png" class="card-img-top img-fluid" alt="Brand 4">
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card h-100 border-0 bg-light justify-content-center align-items-center">
                <img src="image/index/brand5.jpg" class="card-img-top img-fluid  h-auto w-15" alt="Brand 5">
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card h-100 border-0 bg-light justify-content-center align-items-center">
                <img src="image/index/brand6.jpg" class="card-img-top img-fluid" alt="Brand 6">
            </div>
        </div>
    </div>
           <img src="image/index/miss.jpg" alt="miss-univeser" class="mb-3 rounded-3"> 
    </div>
</div>

</div>


<?php
mysqli_free_result($result);
mysqli_close($conn);
include 'includes/footer.php';
?>