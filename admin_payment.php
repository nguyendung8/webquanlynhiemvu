<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

// Cập nhật trạng thái thanh toán
if (isset($_POST['update_payment_status'])) {
    $prescription_id = $_POST['prescription_id'];
    $payment_status = $_POST['payment_status'];

    // Cập nhật trạng thái thanh toán trong cơ sở dữ liệu
    $update_status = mysqli_query($conn, "UPDATE don_thuoc SET trang_thai = '$payment_status' WHERE id = '$prescription_id'") or die('query failed');

    if ($update_status) {
        $message[] = 'Trạng thái thanh toán đã được cập nhật thành công!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thanh toán</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
        label {
            font-size: 17px;
            float: left;
        }
        th {
            font-size: 20px;
            text-align: center;
        }
        td {
            font-size: 18x;
            padding: 1.5rem 0.5rem !important;
            text-align: center;
        }
    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="payment-management">
    <h1 class="title">Quản lý thanh toán</h1>

    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Bệnh nhân</th>
                    <th>Bác sĩ</th>
                    <th>Ngày kê</th>
                    <th>Danh sách thuốc</th>
                    <th>Đơn giá</th>
                    <th>Trạng thái thanh toán</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_prescriptions = mysqli_query($conn, "SELECT don_thuoc.*, benh_nhan.ten AS benh_nhan_ten, bac_si.ten AS bac_si_ten FROM don_thuoc 
                                                             JOIN nguoi_dung AS benh_nhan ON don_thuoc.benh_nhan_id = benh_nhan.id 
                                                             JOIN nguoi_dung AS bac_si ON don_thuoc.bac_si_id = bac_si.id") or die('query failed');
                if (mysqli_num_rows($select_prescriptions) > 0) {
                    while ($fetch_prescription = mysqli_fetch_assoc($select_prescriptions)) {
                ?>
                        <tr>
                            <td><?php echo $fetch_prescription['id']; ?></td>
                            <td><?php echo $fetch_prescription['benh_nhan_ten']; ?></td>
                            <td><?php echo $fetch_prescription['bac_si_ten']; ?></td>
                            <td><?php echo date("d/m/Y", strtotime($fetch_prescription['ngay_ke'])); ?></td>
                            <td><?php echo $fetch_prescription['danh_sach_thuoc']; ?></td>
                            <td><?php echo number_format($fetch_prescription['don_gia']) . " VND"; ?></td>
                            <td><?php echo ucfirst($fetch_prescription['trang_thai']); ?></td>
                            <td>
                                <form action="" method="post" style="display:inline;">
                                    <input type="hidden" name="prescription_id" value="<?php echo $fetch_prescription['id']; ?>">
                                    <?php if ($fetch_prescription['trang_thai'] == 'Chưa thanh toán') { ?>
                                    <select name="payment_status" class="form-select" required>
                                        <option value="Chưa thanh toán" <?php echo $fetch_prescription['trang_thai'] == 'Chưa thanh toán' ? 'selected' : ''; ?>>Chưa thanh toán</option>
                                        <option value="Đã thanh toán" <?php echo $fetch_prescription['trang_thai'] == 'Đã thanh toán' ? 'selected' : ''; ?>>Đã thanh toán</option>
                                    </select>
                                    <button type="submit" name="update_payment_status" class="fs-3 btn-primary btn-sm mt-1">Cập nhật</button>
                                    <?php } else { ?>
                                    <button type="button" class="fs-3 btn-success btn-sm mt-1" disabled>Đã thanh toán</button>
                                    <?php } ?>
                                </form>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo '<tr><td colspan="9" class="text-center">Chưa có đơn thuốc nào!</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</section>

</body>
</html>
