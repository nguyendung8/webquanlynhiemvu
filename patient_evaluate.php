<?php
include 'config.php';
session_start();

if (!isset($_SESSION['patient_id'])) {
    header('location:login.php');
    exit();
}

$patient_id = $_SESSION['patient_id'];


if (isset($_GET['doctor_id'])) {
    $doctor_id = $_GET['doctor_id'];
} else {
    echo "Không tìm thấy bác sĩ.";
    exit();
}

// Xử lý khi form được submit
if (isset($_POST['submit_evaluation'])) {
    $rating = (int)$_POST['diem_danh_gia'];
    $content = mysqli_real_escape_string($conn, $_POST['noi_dung']);
    $current_date = date("Y-m-d H:i:s");

    // Thêm đánh giá vào bảng danh_gia
    $insert_query = "INSERT INTO danh_gia (benh_nhan_id, bac_si_id, diem_danh_gia, noi_dung, ngay_gui) 
                     VALUES ('$patient_id', '$doctor_id', '$rating', '$content', '$current_date')";
    $insert_result = mysqli_query($conn, $insert_query);

    if ($insert_result) {
        $message[] = "Đánh giá của bạn đã được gửi thành công!";
    } else {
        $message[] = "Có lỗi xảy ra. Vui lòng thử lại.";
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh giá </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'patient_header.php'; ?>

<div class="container my-5">
    <h2 class="text-center mb-4">Đánh giá dịch vụ</h2>

    <form method="POST" class="card p-4">
        <div class="mb-3">
            <label for="diem_danh_gia" class="form-label">Điểm đánh giá (1 - 5)</label>
            <select class="form-select" id="diem_danh_gia" name="diem_danh_gia" required>
                <option value="">-- Chọn điểm --</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?> sao</option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="noi_dung" class="form-label">Nội dung đánh giá</label>
            <textarea class="form-control" id="noi_dung" name="noi_dung" rows="4" placeholder="Nhập nội dung đánh giá..." required></textarea>
        </div>

        <button type="submit" name="submit_evaluation" class="new-btn btn-primary">Gửi đánh giá</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
