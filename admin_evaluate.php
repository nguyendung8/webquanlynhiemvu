<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:home.php');
    exit();
}

// Lấy danh sách đánh giá từ cơ sở dữ liệu
$query = "SELECT danh_gia.*, benh_nhan.ten AS benh_nhan_ten, bac_si.ten AS bac_si_ten 
          FROM danh_gia 
          JOIN nguoi_dung AS benh_nhan ON danh_gia.benh_nhan_id = benh_nhan.id 
          JOIN nguoi_dung AS bac_si ON danh_gia.bac_si_id = bac_si.id 
          ORDER BY danh_gia.ngay_gui DESC";
$result = mysqli_query($conn, $query) or die('query failed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đánh giá</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
        .rating-star {
            color: gold;
            font-size: 20px;
        }
    </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="admin-evaluate">
    <h1 class="title">Quản lý đánh giá</h1>

    <div class="container">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Bệnh nhân</th>
                    <th>Bác sĩ</th>
                    <th>Điểm đánh giá</th>
                    <th>Nội dung</th>
                    <th>Ngày gửi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['benh_nhan_ten']; ?></td>
                            <td><?php echo $row['bac_si_ten']; ?></td>
                            <td>
                                <?php 
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $row['diem_danh_gia'] ? '<i class="fas fa-star rating-star"></i>' : '<i class="far fa-star rating-star"></i>';
                                    }
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['noi_dung']); ?></td>
                            <td><?php echo date("d/m/Y H:i", strtotime($row['ngay_gui'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Không có đánh giá nào!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
