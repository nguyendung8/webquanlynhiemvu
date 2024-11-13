<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:home.php');
    exit();
}

// Thống kê doanh thu từ đơn thuốc
$query_total_revenue = "SELECT SUM(don_gia) AS total_revenue FROM don_thuoc";
$result_total_revenue = mysqli_query($conn, $query_total_revenue);
$total_revenue = mysqli_fetch_assoc($result_total_revenue)['total_revenue'];

// Thống kê doanh thu theo từng bác sĩ
$query_revenue_per_doctor = "
    SELECT bac_si.ten AS doctor_name, SUM(don_thuoc.don_gia) AS revenue
    FROM don_thuoc
    JOIN nguoi_dung AS bac_si ON don_thuoc.bac_si_id = bac_si.id
    GROUP BY don_thuoc.bac_si_id";
$result_revenue_per_doctor = mysqli_query($conn, $query_revenue_per_doctor);

// Thống kê số lượng bệnh nhân theo nhóm máu
$query_patients_per_blood_type = "
    SELECT nhom_mau, COUNT(*) AS count
    FROM benh_nhan
    GROUP BY nhom_mau";
$result_patients_per_blood_type = mysqli_query($conn, $query_patients_per_blood_type);


// Thống kê điểm đánh giá trung bình của từng bác sĩ
$query_avg_rating_per_doctor = "
    SELECT bac_si.ten AS doctor_name, AVG(danh_gia.diem_danh_gia) AS average_rating
    FROM danh_gia
    JOIN nguoi_dung AS bac_si ON danh_gia.bac_si_id = bac_si.id
    GROUP BY danh_gia.bac_si_id";
$result_avg_rating_per_doctor = mysqli_query($conn, $query_avg_rating_per_doctor);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Thống Kê</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_style.css">
    <link rel="stylesheet" href="css/new_style.css">
</head>
<body>

<?php include 'admin_header.php'; ?>

<section class="admin-statistics">
    <div class="container">
        <h1 class="title">Trang Thống Kê</h1>

        <!-- Thống Kê Doanh Thu -->
        <div class="card mt-4">
            <h5 class="card-header bg-primary text-white">Thống Kê Doanh Thu</h5>
            <div class="card-body">
                <h5 class="card-title">Tổng doanh thu từ đơn thuốc: <?php echo number_format($total_revenue); ?> VNĐ</h5>
                <h6 class="fs-3 card-subtitle mb-2 text-muted">Doanh thu theo từng bác sĩ:</h6>
                <ul class="fs-4">
                    <?php while ($row = mysqli_fetch_assoc($result_revenue_per_doctor)): ?>
                        <li><?php echo $row['doctor_name']; ?>: <?php echo number_format($row['revenue']); ?> VNĐ</li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

        <!-- Thống Kê Số Lượng Bệnh Nhân -->
        <div class="card mt-4">
            <h5 class="card-header bg-success text-white">Thống Kê Số Lượng Bệnh Nhân</h5>
            <div class="card-body">
                <h6 class="fs-3 card-subtitle mb-2 text-muted">Số lượng bệnh nhân theo nhóm máu:</h6>
                <ul class="fs-4">
                    <?php while ($row = mysqli_fetch_assoc($result_patients_per_blood_type)): ?>
                        <li>Nhóm máu <?php echo $row['nhom_mau']; ?>: <?php echo $row['count']; ?> bệnh nhân</li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>

        <!-- Thống Kê Đánh Giá và Chất Lượng Dịch Vụ -->
        <div class="card mt-4">
            <h5 class="card-header bg-warning text-white">Thống Kê Đánh Giá và Chất Lượng Dịch Vụ</h5>
            <div class="card-body">
                <h6 class="fs-3 card-subtitle mb-2 text-muted">Điểm đánh giá trung bình của từng bác sĩ:</h6>
                <ul class="fs-4">
                    <?php while ($row = mysqli_fetch_assoc($result_avg_rating_per_doctor)): ?>
                        <li><?php echo $row['doctor_name']; ?>: <?php echo round($row['average_rating'], 1); ?> điểm</li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
