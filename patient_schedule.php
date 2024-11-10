<?php
include 'config.php';
session_start();

if (!isset($_SESSION['patient_email'])) {
    header('location:home.php');
    exit();
}

$patient_email = $_SESSION['patient_email'];

// Lấy ID bệnh nhân từ bảng nguoi_dung
$query = "SELECT id FROM nguoi_dung WHERE email = '$patient_email' AND vai_tro = 'benh_nhan' LIMIT 1";
$result = mysqli_query($conn, $query);
$patient = mysqli_fetch_assoc($result);
$patient_id = $patient['id'];

// Lấy danh sách bác sĩ
$doctors_query = "SELECT nd.id, nd.ten, bs.chuyen_khoa FROM nguoi_dung nd JOIN bac_si bs ON nd.id = bs.id";
$doctors_result = mysqli_query($conn, $doctors_query);

// Thêm lịch hẹn
if (isset($_POST['register_appointment'])) {
    $doctor_id = $_POST['bac_si_id'];
    $appointment_time = $_POST['thoi_gian'];
    $note = mysqli_real_escape_string($conn, $_POST['ghi_chu']);
    
    $insert_query = "INSERT INTO lich_hen (benh_nhan_id, bac_si_id, thoi_gian, trang_thai, ghi_chu) 
                     VALUES ('$patient_id', '$doctor_id', '$appointment_time', 'cho_xac_nhan', '$note')";
    mysqli_query($conn, $insert_query);
    header('location: patient_schedule.php');
    exit();
}

// Lấy danh sách lịch hẹn của bệnh nhân
$appointments_query = "SELECT lh.*, nd.ten AS ten_bac_si, bs.chuyen_khoa 
                       FROM lich_hen lh 
                       JOIN nguoi_dung nd ON lh.bac_si_id = nd.id 
                       JOIN bac_si bs ON lh.bac_si_id = bs.id 
                       WHERE lh.benh_nhan_id = '$patient_id' 
                       ORDER BY lh.thoi_gian DESC";
$appointments_result = mysqli_query($conn, $appointments_query);

if (isset($_POST['cancel_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $cancel_query = "UPDATE lich_hen SET trang_thai = 'huy' WHERE id = '$appointment_id' AND benh_nhan_id = '$patient_id'";
    mysqli_query($conn, $cancel_query);
    $message[] = "Hủy lịch hẹn thành công.";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý lịch hẹn khám</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="./css/new_style.css">
</head>
<body>
    <?php include 'patient_header.php'; ?>

    <div class="container my-5">
        <h2 class="text-center mb-4">Quản lý lịch hẹn khám</h2>

        <!-- Form đăng ký lịch hẹn khám -->
        <div class="card mb-5">
            <div class="card-body">
                <h4 class="card-title">Đăng ký lịch hẹn mới</h4>
                <form method="POST">
                    <div class="mb-3">
                        <label for="bac_si_id" class="form-label">Chọn bác sĩ</label>
                        <select class="form-select" id="bac_si_id" name="bac_si_id" required>
                            <option value="">-- Chọn bác sĩ --</option>
                            <?php while ($doctor = mysqli_fetch_assoc($doctors_result)): ?>
                                <option value="<?php echo $doctor['id']; ?>">
                                    <?php echo $doctor['ten'] . " - " . $doctor['chuyen_khoa']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="thoi_gian" class="form-label">Chọn thời gian</label>
                        <input type="datetime-local" class="form-control" id="thoi_gian" name="thoi_gian" required>
                    </div>

                    <div class="mb-3">
                        <label for="ghi_chu" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3" placeholder="Thêm ghi chú (nếu có)"></textarea>
                    </div>

                    <button type="submit" name="register_appointment" class="new-btn btn-primary">Đăng ký lịch hẹn</button>
                </form>
            </div>
        </div>

        <!-- Hiển thị lịch hẹn hiện có -->
        <?php if (mysqli_num_rows($appointments_result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Bác sĩ</th>
                            <th>Chuyên khoa</th>
                            <th>Thời gian hẹn</th>
                            <th>Trạng thái</th>
                            <th>Ghi chú</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $stt = 1; ?>
                        <?php while ($appointment = mysqli_fetch_assoc($appointments_result)): ?>
                            <tr>
                                <td><?php echo $stt++; ?></td>
                                <td><?php echo $appointment['ten_bac_si']; ?></td>
                                <td><?php echo $appointment['chuyen_khoa']; ?></td>
                                <td><?php echo date("d/m/Y H:i", strtotime($appointment['thoi_gian'])); ?></td>
                                <td>
                                    <?php
                                        switch ($appointment['trang_thai']) {
                                            case 'cho_xac_nhan':
                                                echo "Chờ xác nhận";
                                                break;
                                            case 'da_xac_nhan':
                                                echo "Đã xác nhận";
                                                break;
                                            case 'huy':
                                                echo "Đã hủy";
                                                break;
                                            case 'hoan_thanh':
                                                echo "Hoàn thành";
                                                break;
                                        }
                                    ?>
                                </td>
                                <td><?php echo $appointment['ghi_chu']; ?></td>
                                <td>
                                    <?php if ($appointment['trang_thai'] == 'cho_xac_nhan' || $appointment['trang_thai'] == 'da_xac_nhan'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                            <button type="submit" name="cancel_appointment" class="new-btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn hủy lịch hẹn này?');">Hủy</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">Không có hành động</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-center fs-2">Không có lịch hẹn nào.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
