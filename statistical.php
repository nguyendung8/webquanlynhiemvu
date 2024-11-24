<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

// Lấy dữ liệu tổng quan
$total_tasks_query = mysqli_query($conn, "SELECT COUNT(*) AS total_tasks FROM tasks WHERE user_id = '$user_id'");
$total_tasks = mysqli_fetch_assoc($total_tasks_query)['total_tasks'];

$completed_tasks_query = mysqli_query($conn, "SELECT COUNT(*) AS completed_tasks FROM tasks WHERE user_id = '$user_id' AND status = 'Hoàn thành'");
$completed_tasks = mysqli_fetch_assoc($completed_tasks_query)['completed_tasks'];

$in_progress_tasks_query = mysqli_query($conn, "SELECT COUNT(*) AS in_progress_tasks FROM tasks WHERE user_id = '$user_id' AND status = 'Đang thực hiện'");
$in_progress_tasks = mysqli_fetch_assoc($in_progress_tasks_query)['in_progress_tasks'];

$not_started_tasks_query = mysqli_query($conn, "SELECT COUNT(*) AS not_started_tasks FROM tasks WHERE user_id = '$user_id' AND status = 'Chưa bắt đầu'");
$not_started_tasks = mysqli_fetch_assoc($not_started_tasks_query)['not_started_tasks'];

// Lấy dữ liệu cho biểu đồ tiến độ
$priority_stats_query = mysqli_query($conn, "SELECT priority, 
    COUNT(*) AS total, 
    SUM(CASE WHEN status = 'Hoàn thành' THEN 1 ELSE 0 END) AS completed 
    FROM tasks 
    WHERE user_id = '$user_id' 
    GROUP BY priority");

$priority_stats = [];
while ($row = mysqli_fetch_assoc($priority_stats_query)) {
    $priority_stats[] = $row;
}

// Lấy dữ liệu biểu đồ tiến độ theo thời gian
$completion_trend_query = mysqli_query($conn, "SELECT DATE(end_date) AS date, COUNT(*) AS completed_count
    FROM tasks 
    WHERE user_id = '$user_id' AND status = 'Hoàn thành' AND end_date IS NOT NULL
    GROUP BY DATE(end_date)");

$completion_trend = [];
while ($row = mysqli_fetch_assoc($completion_trend_query)) {
    $completion_trend[] = $row;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Thống kê</title>

   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
   <link rel="stylesheet" href="css/style.css">
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <style>
        .stat-container {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .stat-box {
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        #statusChart {
            margin: auto;
            width: 600px !important;
            height: 600px !important;
        }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="container mt-4">
    <h1 class="text-center mb-4">Thống kê Nhiệm vụ</h1>
    
    <!-- Thống kê tổng quan -->
    <div class="stat-container">
        <div class="stat-box bg-primary text-white">
            <h3>Tổng số nhiệm vụ</h3>
            <p class="fs-2"><?php echo $total_tasks; ?></p>
        </div>
        <div class="stat-box bg-success text-white">
            <h3>Đã hoàn thành</h3>
            <p class="fs-2"><?php echo $completed_tasks; ?></p>
        </div>
        <div class="stat-box bg-warning text-white">
            <h3>Đang thực hiện</h3>
            <p class="fs-2"><?php echo $in_progress_tasks; ?></p>
        </div>
        <div class="stat-box bg-danger text-white">
            <h3>Chưa bắt đầu</h3>
            <p class="fs-2"><?php echo $not_started_tasks; ?></p>
        </div>
    </div>

    <!-- Biểu đồ trạng thái -->
    <div class="mt-5">
        <h2 class="text-center">Trạng thái Nhiệm vụ</h2>
        <canvas id="statusChart"></canvas>
    </div>

    <!-- Biểu đồ tiến độ theo mức độ ưu tiên -->
    <div class="mt-5">
        <h2 class="text-center">Tiến độ theo mức độ ưu tiên</h2>
        <canvas id="priorityChart"></canvas>
    </div>

    <!-- Biểu đồ tiến độ theo thời gian -->
    <div class="mt-5">
        <h2 class="text-center">Tiến độ theo thời gian</h2>
        <canvas id="completionTrendChart"></canvas>
    </div>
</div>

<script>
    // Biểu đồ trạng thái nhiệm vụ
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['Chưa bắt đầu', 'Đang thực hiện', 'Hoàn thành'],
            datasets: [{
                data: [
                    <?php echo $not_started_tasks; ?>,
                    <?php echo $in_progress_tasks; ?>,
                    <?php echo $completed_tasks; ?>
                ],
                backgroundColor: ['#ff6b6b', '#feca57', '#1dd1a1']
            }]
        }
    });

    // Biểu đồ tiến độ theo mức độ ưu tiên
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    const priorityData = {
        labels: <?php echo json_encode(array_column($priority_stats, 'priority')); ?>,
        datasets: [{
            label: 'Hoàn thành',
            data: <?php echo json_encode(array_column($priority_stats, 'completed')); ?>,
            backgroundColor: '#1dd1a1'
        }, {
            label: 'Tổng số nhiệm vụ',
            data: <?php echo json_encode(array_column($priority_stats, 'total')); ?>,
            backgroundColor: '#feca57'
        }]
    };
    new Chart(priorityCtx, {
        type: 'bar',
        data: priorityData
    });

    // Biểu đồ tiến độ theo thời gian
    const trendCtx = document.getElementById('completionTrendChart').getContext('2d');
    const trendData = {
        labels: <?php echo json_encode(array_column($completion_trend, 'date')); ?>,
        datasets: [{
            label: 'Hoàn thành',
            data: <?php echo json_encode(array_column($completion_trend, 'completed_count')); ?>,
            backgroundColor: '#1dd1a1',
            borderColor: '#10ac84',
            fill: false
        }]
    };
    new Chart(trendCtx, {
        type: 'line',
        data: trendData
    });
</script>

   <!-- Bootstrap Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
<script src="js/slide_show.js"></script>

</body>
</html>
