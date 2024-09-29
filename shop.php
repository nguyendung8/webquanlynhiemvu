<?php
include 'config.php';

session_start();

$user_id = $_SESSION['user_id']; // tạo session người dùng

if (!isset($user_id)) { // session không tồn tại => quay lại trang đăng nhập
    header('location:home.php');
}

if (isset($_POST['add_to_cart'])) { // thêm sách vào giỏ hàng từ form submit name='add_to_cart'
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_quantity = $_POST['product_quantity'];

    if ($product_quantity == 0) {
        $message[] = 'Sản phẩm đã hết hàng!';
    } else {
        $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

        if (mysqli_num_rows($check_cart_numbers) > 0) { // kiểm tra sách có trong giỏ hàng chưa và tăng số lượng
            $result = mysqli_fetch_assoc($check_cart_numbers);
            $num = $result['quantity'] + $product_quantity;
            $select_quantity = mysqli_query($conn, "SELECT * FROM `Sach` WHERE TenSach='$product_name'");
            $fetch_quantity = mysqli_fetch_assoc($select_quantity);
            if ($num > $fetch_quantity['SoLuong']) {
                $num = $fetch_quantity['SoLuong'];
            }
            mysqli_query($conn, "UPDATE `cart` SET quantity='$num', price='$product_price' WHERE name = '$product_name' AND user_id = '$user_id'");
            $message[] = 'Sản phẩm đã có trong giỏ hàng và được thêm số lượng!';
        } else {
            mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity')") or die('query failed');
            $message[] = 'Sản phẩm đã được thêm vào giỏ hàng!';
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
    <title>Cửa hàng</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .list-cate {
            font-size: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
            padding-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: auto;
            margin-bottom: 20px;
            padding: 10px;
            width: fit-content;
            align-items: center;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }
        .list-cate a {
            color: #b40202;
            border-right: 1px solid #b40202;
            padding-right: 18px;
        }
        .list-cate a:hover {
            opacity: 0.7;
        }
        .list-cate a:first-child {
            padding-left: 18px;
        }
        .list-cate a:last-child {
            padding-right: 18px;
            border-right: none;
        }
        .head {
            background: url(./images/head_img.jpg) no-repeat;
            background-size: cover;
            background-position: center;
        }
        .box p {
            font-size: 17px;
            padding-bottom: 5px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<section class="products">

    <h1 class="title">Tất cả sản phẩm</h1>

    <div class="list-cate">
        <?php  
        $select_categoriess = mysqli_query($conn, "SELECT * FROM `TheLoai`") or die('query failed');
        if(mysqli_num_rows($select_categoriess) > 0){
            while($fetch_categoriess = mysqli_fetch_assoc($select_categoriess)){
                ?>
                <a href="?cate_id=<?php echo $fetch_categoriess['MaTheLoai']; ?>"><?php echo $fetch_categoriess['TenTheLoai']; ?></a>
                <?php
            }
        } else {
            echo '<p class="empty">Chưa có danh mục nào!</p>';
        }
        ?>
    </div>
    <div style="clear:both"></div>

    <div class="box-container">

        <?php  
        $select_num = mysqli_query($conn, "SELECT MaSach FROM `Sach`");
        $querry = mysqli_query($conn, "SELECT * FROM `Sach` LIMIT 1");
        $first_catge_id = mysqli_fetch_assoc($querry);
            if (isset($_GET['cate_id'])) {
                $cate_id = $_GET['cate_id'];
            } else {
                $cate_id = 1;
            }
               $select_products = mysqli_query($conn, "
                SELECT Sach.*, TacGia.TenTG, TheLoai.TenTheLoai, NXB.TenNXB, NCC.TenNCC, DonGia.MaDonGia, DonGia.DonGia
                FROM `Sach` 
                LEFT JOIN `TacGia` ON Sach.MaTacGia = TacGia.MaTG
                LEFT JOIN `TheLoai` ON Sach.MaTheLoai = TheLoai.MaTheLoai
                LEFT JOIN `NXB` ON Sach.MaNXB = NXB.MaNXB
                LEFT JOIN `NCC` ON Sach.MaNCC = NCC.MaNCC
                LEFT JOIN `DonGia` ON Sach.MaDonGia = DonGia.MaDonGia
                  WHERE TheLoai.MaTheLoai = $cate_id
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
               } else {
                     echo '<p class="empty">Chưa có sản phẩm được bán!</p>';
               }
        ?>
    </div>

</section>


<script src="js/script.js"></script>

</body>
</html>
