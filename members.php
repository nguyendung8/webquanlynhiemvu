<?php
include 'config.php';
session_start();

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['member_id'])) {
    header('location:home.php'); // Nếu chưa đăng nhập thì chuyển về trang đăng nhập
    exit;
}

$member_id = $_SESSION['member_id'];

// Thêm giao dịch thanh toán mới
if (isset($_POST['add_payment'])) {
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $payment_method = $_POST['payment_method'];
    $payment_date = date('Y-m-d H:i:s'); // Ngày giờ hiện tại

    $add_payment_query = mysqli_query($conn, "INSERT INTO `payments` (member_id, payment_date, amount, payment_method) VALUES('$member_id', '$payment_date', '$amount', '$payment_method')") or die('query failed');

    if ($add_payment_query) {
        $message[] = 'Thanh toán thành công!';
    } else {
        $message[] = 'Thanh toán thất bại!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Thanh Toán</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
       th {
         font-size: 20px;
         text-align: center;
      }
      td {
         font-size: 18px;
         padding: 1.5rem 0.5rem !important;
         text-align: center;
      }
    </style>
</head>
<body>

<?php include 'member_header.php'; // Header cho thành viên ?>

<section class="add-products">
    <h1 class="title">Thêm Giao Dịch Thanh Toán</h1>
    <form action="" method="post">
        <input type="number" name="amount" class="box" placeholder="Số tiền" required>
        <select name="payment_method" class="box" required>
            <option value="" disabled selected>Chọn phương thức thanh toán</option>
            <option value="credit_card">Thẻ tín dụng</option>
            <option value="paypal">PayPal</option>
            <option value="bank_transfer">Chuyển khoản ngân hàng</option>
        </select>
        <input type="submit" value="Thanh Toán" name="add_payment" class="btn-primary" style="margin-top: 5px; padding: 10px 25px; border-radius: 5px; font-size: 18px;">
    </form>
</section>

<section class="show-payments">
    <h1 class="title">Danh Sách Giao Dịch Thanh Toán</h1>
    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Mã Giao Dịch</th>
                    <th>Ngày Thanh Toán</th>
                    <th>Số Tiền</th>
                    <th>Phương Thức Thanh Toán</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_payments = mysqli_query($conn, "SELECT * FROM `payments` WHERE member_id = '$member_id'") or die('query failed');
                if (mysqli_num_rows($select_payments) > 0) {
                    while ($fetch_payment = mysqli_fetch_assoc($select_payments)) {
                        ?>
                        <tr>
                            <td><?php echo $fetch_payment['payment_id']; ?></td>
                            <td><?php echo $fetch_payment['payment_date']; ?></td>
                            <td><?php echo $fetch_payment['amount']; ?> VNĐ</td>
                            <td><?php echo ucfirst($fetch_payment['payment_method']); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    echo '<tr><td colspan="4" class="text-center">Chưa có giao dịch nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
