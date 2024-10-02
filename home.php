    <?php
    include 'config.php';
    session_start();

    
    $user_id = @$_SESSION['user_id']; // tạo session người dùng

    if(isset($_POST['add_to_cart'])) { // thêm sách vào giỏ hàng từ form submit name='add_to_cart'
        if(!isset($user_id)) {
            $message[] = 'Vui lòng đăng nhập để mua hàng!';
        } else {
            $product_name = $_POST['product_name'];
            $product_price = $_POST['product_price'];
            $product_quantity = $_POST['product_quantity'];

            if($product_quantity == 0) {
                $message[] = 'Sản phẩm đã hết hàng!';
            } else {
                $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

                if(mysqli_num_rows($check_cart_numbers) > 0) { // kiểm tra sách có trong giỏ hàng chưa và tăng số lượng
                    $result = mysqli_fetch_assoc($check_cart_numbers);
                    $num = $result['quantity'] + $product_quantity;
                    $select_quantity = mysqli_query($conn, "SELECT * FROM `Sach` WHERE TenSach='$product_name'");
                    $fetch_quantity = mysqli_fetch_assoc($select_quantity);
                    if($num > $fetch_quantity['SoLuong']) {
                    $num = $fetch_quantity['SoLuong'];
                    }
                    mysqli_query($conn, "UPDATE `cart` SET quantity='$num' WHERE name = '$product_name' AND user_id = '$user_id'");
                    $message[] = 'Sản phẩm đã có trong giỏ hàng và được thêm số lượng!';
                } else {
                    mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity')") or die('query failed');
                    $message[] = 'Sản phẩm đã được thêm vào giỏ hàng!';
                }
            }
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .box p {
            font-size: 17px;
            padding-bottom: 5px;
        }
        .action {
            display: flex;
            align-items: center;
        }
        .view-product {
            margin-top: 5px;
            padding: 5px 20px;
            background-color: burlywood;
            font-size: 16px;
            color: #fff;
            border-radius: 6px;
        }
        .view-product:hover {
            opacity: 0.9;
        }
        .slideshow-container {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
            overflow: hidden;
        }
        .slide {
            display: none;
            animation: fade 2s ease-in-out infinite;
        }
        @keyframes fade {
            0%, 100% {
                opacity: 0;
            }
            25%, 75% {
                opacity: 1;
            }
        }
        .slide img {
            width: 100%;
            height: 485px;
            border-radius: 9px;
        }
        .borrow_book:hover { 
            opacity: 0.9;
        }
        .borrow_book {
            padding: 5px 25px;
            background-image: linear-gradient(to right, #ff9800, #F7695D);
            border-radius: 4px;
            cursor: pointer;
            font-size: 20px;
            color: #fff;
            font-weight: 700;
        }
        .home-banner {
            min-height: 70vh;
            background:linear-gradient(rgba(0,0,0,.1), rgba(0,0,0,.1)), url(./image/bg_home.webp) no-repeat;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
    </head>
    <body>

    <?php include 'header.php'; ?>

    <section class="home home-banner">
    </section>

    <section class="products">

    <h1 class="title">Danh sách sách</h1>

    <div class="box-container">

        <?php  
            $select_products = mysqli_query($conn, "
                SELECT Sach.*, TacGia.TenTG, TheLoai.TenTheLoai, NXB.TenNXB, NCC.TenNCC, DonGia.MaDonGia, DonGia.DonGia
                FROM `Sach` 
                LEFT JOIN `TacGia` ON Sach.MaTacGia = TacGia.MaTG
                LEFT JOIN `TheLoai` ON Sach.MaTheLoai = TheLoai.MaTheLoai
                LEFT JOIN `NXB` ON Sach.MaNXB = NXB.MaNXB
                LEFT JOIN `NCC` ON Sach.MaNCC = NCC.MaNCC
                LEFT JOIN `DonGia` ON Sach.MaDonGia = DonGia.MaDonGia
                ORDER BY Sach.MaSach DESC") or die('query failed');
            
            if(mysqli_num_rows($select_products) > 0){
                while($fetch_products = mysqli_fetch_assoc($select_products)){
        ?>
                <form style="height: -webkit-fill-available;" action="" method="post" class="box">
                    <div class="name"><?php echo $fetch_products['TenSach']; ?></div>
                    <p>Tác giả: <?php echo $fetch_products['TenTG']; ?></p>
                    <p>Thể loại: <?php echo $fetch_products['TenTheLoai']; ?></p>
                    <p>Nhà xuất bản: <?php echo $fetch_products['TenNXB']; ?></p>
                    <p>Nhà cung cấp: <?php echo $fetch_products['TenNCC']; ?></p>
                    <p>Giá: <?php echo number_format($fetch_products['DonGia'],0,',','.' ); ?> đ</p>
                    <span style="font-size: 17px; display: flex;">Số lượng mua:</span>
                    <input type="number" min="<?=($fetch_products['SoLuong']>0) ? 1:0 ?>" max="<?php echo $fetch_products['SoLuong']; ?>" name="product_quantity" value="<?=($fetch_products['SoLuong']>0) ? 1:0 ?>" class="qty">
                    <input type="hidden" name="product_name" value="<?php echo $fetch_products['TenSach']; ?>">
                    <input type="hidden" name="product_price" value="<?php echo $fetch_products['DonGia']; ?>">
                    <input type="submit" value="Thêm vào giỏ hàng" name="add_to_cart" class="btn">
                </form>
        <?php
                }
            }else{
                echo '<p class="empty">Chưa có sản phẩm được bán!</p>';
            }
        ?>
    </div>

    <div class="load-more" style="margin-top: 2rem; text-align:center">
        <a href="shop.php" class="option-btn">Xem thêm sản phẩm</a>
    </div>

    </section>


    <script src="js/script.js"></script>

    </body>
    </html>
