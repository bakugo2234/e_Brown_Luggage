<?php 
    include "db_connect.php";

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Improved title for SEO and clarity -->
    <title>Brown Luggage - Premium Luggage & Bags</title>
    <!-- SEO meta tags -->
    <meta name="description" content="Shop premium luggage, backpacks, handbags, and accessories at Brown Luggage. Enjoy exclusive deals and quality products.">
    <meta name="keywords" content="luggage, backpacks, handbags, accessories, Brown Luggage">
    <!-- Favicon for branding -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <!-- Latest Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="index.css">

    <script src="https://cdn.tailwindcss.com">
    </script>
          <link
              href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
              rel="stylesheet"/>
          <link
              href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&amp;display=swap"
              rel="stylesheet"/>
          <style>
     body {
        font-family: 'Roboto', sans-serif;
      }
    </style>
</head>
<body>
    <img src="https://mia.vn/media/uploads/2025-04-15/topbar-pc.jpg" alt="Mua nhiều Giảm nhiều promotional banner" class="slogan">

    <header style="position: static;">
        <div class="logo">
            <a href="index.php"><span>Brown Luggage</span></a>
        </div>
        <nav>
            <ul>
                <li><a href="vali.php">Vali</a></li>
                <li><a href="balo.php">Balo</a></li>
                <li><a href="bag.php">Túi xách</a></li>
                <li><a href="accessory.php">Phụ kiện</a></li>
                <li><a href="outet.php" class="outlet">OUTLET SALE</a></li>
            </ul>
        </nav>
        <div class="icon-bar d-flex justify-content-end align-items-center p-2">
    <div class="search-container position-relative d-inline-block">
        <a href=""><i class="bi bi-search fs-6 p-2"></i></a>
        <a href="cart.php" class="position-relative">
            <i class="bi bi-bag fs-6 p-2"></i>
            <?php
            $cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
            if ($cart_count > 0):
            ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?php echo $cart_count; ?>
                </span>
            <?php endif; ?>
        </a>
    </div>
</div>
          
        
    </header>